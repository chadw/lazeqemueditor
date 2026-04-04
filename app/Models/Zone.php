<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Zone extends BaseModel
{
    protected $connection = 'eqemu';
    protected $table = 'zone';
    public $timestamps = false;
    protected $guarded = [];

    protected $casts = [
        'suspendbuffs' => 'boolean',
        'hotzone' => 'boolean',
        'bypass_expansion_check' => 'boolean',
        'idle_when_empty' => 'boolean',
        'canlevitate' => 'boolean',
        'castoutdoor' => 'boolean',
        'cancombat' => 'boolean',
        'peqzone' => 'boolean',
    ];

    public function spawns(): HasMany
    {
        return $this->hasMany(SpawnTwo::class, 'zone', 'short_name');
    }

    public function zonepoints(): HasMany
    {
        return $this->hasMany(ZonePoint::class, 'zone', 'short_name');
    }

    public function blockedSpells(): HasMany
    {
        return $this->hasMany(BlockedSpell::class, 'zoneid', 'zoneidnumber');
    }

    public function doors(): HasMany
    {
        return $this->hasMany(Door::class, 'zone', 'short_name');
    }

    public function groundspawns(): HasMany
    {
        return $this->hasMany(GroundSpawn::class, 'zoneid', 'zoneidnumber');
    }

    public function custobjdata(): HasMany
    {
        return $this->hasMany(CustObjData::class, 'zonesn', 'short_name');
    }

    public function traps(): HasMany
    {
        return $this->hasMany(Trap::class, 'zone', 'short_name');
    }

    public function objects(): HasMany
    {
        return $this->hasMany(ContainerObject::class, 'zoneid', 'zoneidnumber');
    }

    public function taskActivities(): HasMany
    {
        return $this->hasMany(TaskActivity::class, 'zones', 'zoneidnumber');
    }

    public static function getAllZones(): Collection
    {
        return self::select(
                'id', 'zoneidnumber', 'expansion', 'short_name', 'long_name', 'version', 'zone_exp_multiplier'
            )
            ->orderBy('expansion', 'asc')
            ->orderBy('long_name', 'asc')
            ->get()
            ->groupBy('expansion');
    }

    public static function baseZones(): Collection
    {
        return self::query()
            ->select('zone.id', 'zone.zoneidnumber', 'zone.short_name', 'zone.long_name', 'zone.version')
            ->joinSub(
                self::query()
                    ->select('short_name', DB::raw('MIN(version) as min_version'))
                    ->groupBy('short_name'),
                'zone_min',
                fn ($join) => $join
                    ->on('zone.short_name', '=', 'zone_min.short_name')
                    ->on('zone.version', '=', 'zone_min.min_version')
            )
            ->orderBy('zone.short_name')
            ->get();
    }

    public static function versionsFor(int $zoneidnumber): Collection
    {
        return self::query()
            ->where('zoneidnumber', $zoneidnumber)
            ->orderBy('version')
            ->get(['id', 'version']);
    }

    public static function selectZones(): array
    {
        return self::select('zoneidnumber', 'short_name', 'long_name')
            ->orderBy('short_name')
            ->get()
            ->unique('short_name')
            ->keyBy('short_name')
            ->mapWithKeys(fn($z) => [
                $z->zoneidnumber => $z->short_name . ' - ' . $z->long_name
            ])
            ->toArray();
    }

    public static function resolveZone(int $zoneid, ?int $version = null): Builder
    {
        $query = self::where('zoneidnumber', $zoneid);

        if ($version !== null) {
            $query->where('version', $version);
        } else {
            $query->orderBy('version');
        }

        return $query;
    }

    public static function zoneOptions(): Collection
    {
        return self::select('zoneidnumber', 'short_name', 'long_name')
            ->orderBy('short_name')
            ->groupBy('zoneidnumber')
            ->get();
    }
}
