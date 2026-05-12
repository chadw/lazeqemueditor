<?php

namespace App\Services;

use App\Models\Zone;
use App\Models\SpawnTwo;
use App\Models\SpawnGroup;
use App\Models\SpawnEntry;
use App\Models\SpawnTwoDisabled;
use App\Models\NpcType;
use Illuminate\Support\Facades\DB;

class ZoneCloner
{
    public function cloneZone(Zone $zone): Zone
    {
        return DB::connection('eqemu')->transaction(function () use ($zone) {
            $zoneId = $zone->zoneidnumber;
            $short = $zone->short_name;

            $newVersion = Zone::where('zoneidnumber', $zoneId)->max('version') + 1;

            // Clone zone row
            $newZone = $zone->replicate();
            $newZone->version = $newVersion;
            $newZone->id = null;
            $newZone->save();

            // Load spawn2 for this zone+version
            $spawn2s = SpawnTwo::where('zone', $short)
                ->where('version', $zone->version)
                ->get();

            $oldSpawngroupIds = $spawn2s->pluck('spawngroupID')->unique()->filter()->values();

            // Clone spawngroups
            $spawngroupMap = [];
            if ($oldSpawngroupIds->isNotEmpty()) {
                $groups = SpawnGroup::whereIn('id', $oldSpawngroupIds)->get();
                foreach ($groups as $g) {
                    $new = $g->replicate();
                    $new->id = null;
                    $new->save();
                    $spawngroupMap[$g->id] = $new->id;
                }
            }

            // Clone spawn entries and collect npc ids
            $oldSpawngroupIdsArr = $oldSpawngroupIds->all();
            $entries = $oldSpawngroupIdsArr ? SpawnEntry::whereIn('spawngroupID', $oldSpawngroupIdsArr)->get() : collect();

            $npcIds = $entries->pluck('npcID')->unique()->filter()->values();

            // Clone npc types
            $npcMap = [];
            if ($npcIds->isNotEmpty()) {
                $npcs = NpcType::whereIn('id', $npcIds)->get();
                foreach ($npcs as $npc) {
                    $newNpc = $npc->replicate();
                    $newNpc->id = null;
                    $newNpc->save();
                    $npcMap[$npc->id] = $newNpc->id;
                }
            }

            // Clone spawn2 rows (keeping zone short_name, but bumping version and mapping spawngroupID)
            $spawn2Map = [];
            foreach ($spawn2s as $s2) {
                $newS2 = $s2->replicate();
                $newS2->id = null;
                if (isset($spawngroupMap[$s2->spawngroupID])) {
                    $newS2->spawngroupID = $spawngroupMap[$s2->spawngroupID];
                }
                $newS2->version = $newVersion;
                $newS2->save();
                $spawn2Map[$s2->id] = $newS2->id;
            }

            // Clone spawn2_disabled
            if (!empty($spawn2Map)) {
                $oldSpawn2Ids = array_keys($spawn2Map);
                $disabled = SpawnTwoDisabled::whereIn('spawn2_id', $oldSpawn2Ids)->get();
                foreach ($disabled as $d) {
                    $newD = $d->replicate();
                    $newD->id = null;
                    $newD->spawn2_id = $spawn2Map[$d->spawn2_id] ?? $d->spawn2_id;
                    $newD->save();
                }
            }

            // Clone spawn entries with updated spawngroupID and npcID
            foreach ($entries as $entry) {
                $newEntry = $entry->replicate();
                if (isset($spawngroupMap[$entry->spawngroupID])) {
                    $newEntry->spawngroupID = $spawngroupMap[$entry->spawngroupID];
                }
                if (isset($npcMap[$entry->npcID])) {
                    $newEntry->npcID = $npcMap[$entry->npcID];
                }
                $newEntry->save();
            }

            return $newZone;
        });
    }

    public function generateSql(Zone $zone): string
    {
        $pdo = DB::connection('eqemu')->getPdo();
        $quote = fn($v) => $this->quoteValue($v, $pdo);

        $zoneId = $zone->zoneidnumber;
        $short = $zone->short_name;

        $newVersion = Zone::where('zoneidnumber', $zoneId)->max('version') + 1;

        $lines = [];
        $lines[] = "START TRANSACTION;";

        // zone row
        $zoneAttrs = $zone->getAttributes();
        unset($zoneAttrs['id']);
        $zoneAttrs['version'] = $newVersion;

        [$cols, $vals] = $this->buildInsertParts($zoneAttrs, $quote);
        $lines[] = "INSERT INTO `zone` ({$cols}) VALUES ({$vals});";
        $lines[] = "SET @zone_newid = LAST_INSERT_ID();";

        // spawn2 rows
        $spawn2s = SpawnTwo::where('zone', $short)
            ->where('version', $zone->version)
            ->get();

        $oldSpawngroupIds = $spawn2s->pluck('spawngroupID')->unique()->filter()->values();

        // spawngroups
        $spawngroupMapVars = [];
        if ($oldSpawngroupIds->isNotEmpty()) {
            $groups = SpawnGroup::whereIn('id', $oldSpawngroupIds)->get();
            foreach ($groups as $g) {
                $attrs = $g->getAttributes();
                unset($attrs['id']);
                [$c, $v] = $this->buildInsertParts($attrs, $quote);
                $lines[] = "INSERT INTO `spawngroup` ({$c}) VALUES ({$v});";
                $var = "@sg_{$g->id}";
                $lines[] = "SET {$var} = LAST_INSERT_ID();";
                $spawngroupMapVars[$g->id] = $var;
            }
        }

        // spawn entries (need to collect entries now)
        $oldSpawngroupIdsArr = $oldSpawngroupIds->all();
        $entries = $oldSpawngroupIdsArr ? SpawnEntry::whereIn('spawngroupID', $oldSpawngroupIdsArr)->get() : collect();

        // npc types
        $npcIds = $entries->pluck('npcID')->unique()->filter()->values();
        $npcMapVars = [];
        if ($npcIds->isNotEmpty()) {
            $npcs = NpcType::whereIn('id', $npcIds)->get();
            foreach ($npcs as $npc) {
                $attrs = $npc->getAttributes();
                unset($attrs['id']);
                [$c, $v] = $this->buildInsertParts($attrs, $quote);
                $lines[] = "INSERT INTO `npc_types` ({$c}) VALUES ({$v});";
                $var = "@npc_{$npc->id}";
                $lines[] = "SET {$var} = LAST_INSERT_ID();";
                $npcMapVars[$npc->id] = $var;
            }
        }

        // spawn2 rows
        $spawn2MapVars = [];
        foreach ($spawn2s as $s2) {
            $attrs = $s2->getAttributes();
            unset($attrs['id']);
            // map spawngroupID to variable
            if (isset($spawngroupMapVars[$s2->spawngroupID])) {
                $attrs['spawngroupID'] = $spawngroupMapVars[$s2->spawngroupID];
                // we'll inject as raw variable later
            }
            $attrs['version'] = $newVersion;

            // build columns and values but allow raw variable tokens
            [$cols, $vals] = $this->buildInsertPartsAllowingVars($attrs, $quote);
            $lines[] = "INSERT INTO `spawn2` ({$cols}) VALUES ({$vals});";
            $var = "@s2_{$s2->id}";
            $lines[] = "SET {$var} = LAST_INSERT_ID();";
            $spawn2MapVars[$s2->id] = $var;
        }

        // spawn2_disabled
        if (!empty($spawn2MapVars)) {
            $oldSpawn2Ids = array_keys($spawn2MapVars);
            $disabled = SpawnTwoDisabled::whereIn('spawn2_id', $oldSpawn2Ids)->get();
            foreach ($disabled as $d) {
                $attrs = $d->getAttributes();
                unset($attrs['id']);
                // map spawn2_id to var
                if (isset($spawn2MapVars[$d->spawn2_id])) {
                    $attrs['spawn2_id'] = $spawn2MapVars[$d->spawn2_id];
                }
                [$c, $v] = $this->buildInsertPartsAllowingVars($attrs, $quote);
                $lines[] = "INSERT INTO `spawn2_disabled` ({$c}) VALUES ({$v});";
            }
        }

        // spawnentry
        foreach ($entries as $entry) {
            $attrs = $entry->getAttributes();
            // map spawngroupID and npcID
            if (isset($spawngroupMapVars[$entry->spawngroupID])) {
                $attrs['spawngroupID'] = $spawngroupMapVars[$entry->spawngroupID];
            }
            if (isset($npcMapVars[$entry->npcID])) {
                $attrs['npcID'] = $npcMapVars[$entry->npcID];
            }
            [$c, $v] = $this->buildInsertPartsAllowingVars($attrs, $quote);
            $lines[] = "INSERT INTO `spawnentry` ({$c}) VALUES ({$v});";
        }

        $lines[] = "COMMIT;";

        // Replace variable placeholders: our buildInsertPartsAllowingVars returns variable tokens as strings like @sg_1
        return implode("\n", $lines) . "\n";
    }

    private function buildInsertParts(array $attrs, callable $quote): array
    {
        $cols = [];
        $vals = [];
        foreach ($attrs as $k => $v) {
            $cols[] = "`{$k}`";
            $vals[] = $quote($v);
        }

        return [implode(', ', $cols), implode(', ', $vals)];
    }

    private function buildInsertPartsAllowingVars(array $attrs, callable $quote): array
    {
        $cols = [];
        $vals = [];
        foreach ($attrs as $k => $v) {
            $cols[] = "`{$k}`";
            if (is_string($v) && str_starts_with($v, '@')) {
                // raw variable token
                $vals[] = $v;
            } else {
                $vals[] = $quote($v);
            }
        }

        return [implode(', ', $cols), implode(', ', $vals)];
    }

    private function quoteValue(mixed $v, \PDO $pdo): string
    {
        if ($v === null) {
            return 'NULL';
        }
        if (is_bool($v)) {
            return $v ? '1' : '0';
        }
        if (is_int($v) || is_float($v)) {
            return (string)$v;
        }
        // For variables (like @sg_1) passed as strings, we keep them raw elsewhere.
        return $pdo->quote((string)$v);
    }
}
