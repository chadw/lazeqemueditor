<?php

namespace App\Http\Controllers;

use App\Models\AaRank;
use Illuminate\Http\Request;
use App\Http\Requests\AaRankRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Models\AaRankEffect;
use App\Models\AaRankPrereq;
use App\Models\AaAbility;
use Spatie\DiscordAlerts\Facades\DiscordAlert;

class AaRankController extends Controller
{
    public function store(AaRankRequest $request, AaRank $rank)
    {
        $data = $request->validated();

        if (empty($data['id'])) {
            $maxId = AaRank::max('id') ?? 0;
            $data['id'] = $maxId + 1;
        }

        $rank = AaRank::create($data);

        if ($request->prev_id) {
            AaRank::where('id', $request->prev_id)
                ->update(['next_id' => $rank->id]);
        }

        return back()->with('success', 'AA Rank created.');
    }

    public function update(AaRankRequest $request, AaRank $rank)
    {
        $payload = $request->all();

        if (!array_key_exists('effects', $payload) && !array_key_exists('prereqs', $payload) && !array_key_exists('main', $payload)) {
            $data = $request->validated();
            $rank->update($data);
            return back()->with('success', 'AA Rank updated.');
        }

        return back()->with('success', 'AA Rank updated.');
    }

    public function batchSave(Request $request, AaRank $rank)
    {
        $payload = $request->all();

        $main = $payload['main'] ?? [];
        $effects = $payload['effects'] ?? [];
        $prereqs = $payload['prereqs'] ?? [];

        // validate main using existing request rules
        $mainRules = (new AaRankRequest())->rules();
        $validatedMain = Validator::make($main, $mainRules)->validate();

        // validate effects array
        $effectRules = [
            '*.slot' => 'required|integer|min:1',
            '*.effect_id' => 'nullable|integer',
            '*.base1' => 'nullable|integer',
            '*.base2' => 'nullable|integer',
        ];
        $validatedEffects = Validator::make($effects, $effectRules)->validate();

        // validate prereqs array
        $prereqRules = [
            '*.aa_id' => 'required|integer',
            '*.points' => 'nullable|integer',
        ];
        $validatedPrereqs = Validator::make($prereqs, $prereqRules)->validate();

        $oldMain = $rank->only(array_keys($validatedMain));
        $oldEffects = AaRankEffect::where('rank_id', $rank->id)->get()->keyBy('slot')->toArray();
        $oldPrereqs = AaRankPrereq::where('rank_id', $rank->id)->get()->keyBy('aa_id')->toArray();

        $conn = DB::connection('eqemu');
        $conn->beginTransaction();
        try {
            AaRank::withoutEvents(fn () => $rank->update($validatedMain));

            $newSlots = array_column($validatedEffects, 'slot');
            DB::connection('eqemu')->table('aa_rank_effects')
                ->where('rank_id', $rank->id)
                ->whereNotIn('slot', $newSlots)
                ->delete();

            foreach ($validatedEffects as $eff) {
                DB::connection('eqemu')->table('aa_rank_effects')->updateOrInsert(
                    ['rank_id' => $rank->id, 'slot' => $eff['slot']],
                    [
                        'rank_id' => $rank->id,
                        'slot' => $eff['slot'],
                        'effect_id' => $eff['effect_id'] ?? 0,
                        'base1' => $eff['base1'] ?? 0,
                        'base2' => $eff['base2'] ?? 0,
                    ]
                );
            }

            foreach ($validatedPrereqs as $p) {
                DB::connection('eqemu')->table('aa_rank_prereqs')->updateOrInsert(
                    ['rank_id' => $rank->id, 'aa_id' => $p['aa_id']],
                    ['rank_id' => $rank->id, 'aa_id' => $p['aa_id'], 'points' => $p['points'] ?? 0]
                );
            }

            $newAaIds = array_column($validatedPrereqs, 'aa_id');
            DB::connection('eqemu')->table('aa_rank_prereqs')
                ->where('rank_id', $rank->id)
                ->whereNotIn('aa_id', $newAaIds)
                ->delete();

            $conn->commit();
        } catch (\Throwable $e) {
            try { $conn->rollBack(); } catch (\Throwable $_) {}
            return response()->json(['success' => false, 'message' => 'Save failed: '.$e->getMessage()], 500);
        }

        $this->sendRankDiscordAlert($rank->id, $oldMain, $validatedMain, $oldEffects, $validatedEffects, $oldPrereqs, $validatedPrereqs);

        return response()->json(['success' => true]);
    }

    public function batchSaveMultiple(Request $request)
    {
        $payload = $request->all();
        //dd($payload);

        $ranks = $payload['ranks'] ?? [];

        if (!is_array($ranks) || empty($ranks)) {
            return response()->json(['success' => false, 'message' => 'No ranks provided'], 422);
        }

        // validation rules used below
        $mainRules = (new AaRankRequest())->rules();
        $effectRules = [
            '*.slot' => 'required|integer|min:1',
            '*.effect_id' => 'nullable|integer',
            '*.base1' => 'nullable|integer',
            '*.base2' => 'nullable|integer',
        ];
        $prereqRules = [
            '*.aa_id' => 'required|integer',
            '*.points' => 'nullable|integer',
        ];

        foreach ($ranks as $r) {
            $rankId = $r['rankId'] ?? null;
            $main = $r['main'] ?? [];
            if ($rankId && isset($main['rank_id']) && intval($main['rank_id']) !== intval($rankId)) {
                return response()->json(['success' => false, 'message' => 'Rank ID mismatch in bulk payload', 'rankId' => $rankId], 422);
            }
        }

        $prepared = [];
        foreach ($ranks as $r) {
            $rankId = $r['rankId'] ?? null;
            if (!$rankId) continue;

            $rank = AaRank::find($rankId);
            if (!$rank) continue;

            $main = $r['main'] ?? [];
            $effects = $r['effects'] ?? [];
            $prereqs = $r['prereqs'] ?? [];

            $validatedMain = Validator::make($main, $mainRules)->validate();
            $validatedEffects = Validator::make($effects, $effectRules)->validate();
            $validatedPrereqs = Validator::make($prereqs, $prereqRules)->validate();

            $prepared[] = [
                'rank' => $rank,
                'validatedMain' => $validatedMain,
                'validatedEffects' => $validatedEffects,
                'validatedPrereqs' => $validatedPrereqs,
                'oldMain' => $rank->only(array_keys($validatedMain)),
                'oldEffects' => AaRankEffect::where('rank_id', $rank->id)->get()->keyBy('slot')->toArray(),
                'oldPrereqs' => AaRankPrereq::where('rank_id', $rank->id)->get()->keyBy('aa_id')->toArray(),
            ];
        }

        $conn = DB::connection('eqemu');
        $conn->beginTransaction();
        try {
            foreach ($prepared as $p) {
                $rank = $p['rank'];

                AaRank::withoutEvents(fn () => $rank->update($p['validatedMain']));

                $newSlots = array_column($p['validatedEffects'], 'slot');
                DB::connection('eqemu')->table('aa_rank_effects')
                    ->where('rank_id', $rank->id)
                    ->whereNotIn('slot', $newSlots)
                    ->delete();

                foreach ($p['validatedEffects'] as $eff) {
                    DB::connection('eqemu')->table('aa_rank_effects')->updateOrInsert(
                        ['rank_id' => $rank->id, 'slot' => $eff['slot']],
                        [
                            'rank_id' => $rank->id,
                            'slot' => $eff['slot'],
                            'effect_id' => $eff['effect_id'] ?? 0,
                            'base1' => $eff['base1'] ?? 0,
                            'base2' => $eff['base2'] ?? 0,
                        ]
                    );
                }

                foreach ($p['validatedPrereqs'] as $pr) {
                    DB::connection('eqemu')->table('aa_rank_prereqs')->updateOrInsert(
                        ['rank_id' => $rank->id, 'aa_id' => $pr['aa_id']],
                        ['rank_id' => $rank->id, 'aa_id' => $pr['aa_id'], 'points' => $pr['points'] ?? 0]
                    );
                }

                $newAaIds = array_column($p['validatedPrereqs'], 'aa_id');
                DB::connection('eqemu')->table('aa_rank_prereqs')
                    ->where('rank_id', $rank->id)
                    ->whereNotIn('aa_id', $newAaIds)
                    ->delete();
            }

            $conn->commit();
        } catch (\Throwable $e) {
            try { $conn->rollBack(); } catch (\Throwable $_) {}
            Log::error('AaRankController::batchSaveMultiple exception during save', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Save failed: '.$e->getMessage()], 500);
        }

        $this->sendMultiRankDiscordAlert($prepared);

        return response()->json(['success' => true]);
    }

    private function buildRankChangeLines(
        int $rankId,
        array $oldMain,
        array $newMain,
        array $oldEffects,
        array $newEffects,
        array $oldPrereqs,
        array $newPrereqs
    ): array {
        $lines = [];

        $skipKeys = ['rank_id', '_token', '_method'];
        $mainParts = [];
        foreach ($newMain as $key => $newVal) {
            if (in_array($key, $skipKeys, true)) continue;
            $oldVal = $oldMain[$key] ?? null;
            if ((string)($oldVal ?? '') !== (string)($newVal ?? '')) {
                $mainParts[] = "**{$key}:** {$newVal} *(**was**: {$oldVal})*";
            }
        }
        if ($mainParts) {
            $lines[] = implode(', ', $mainParts);
        }

        $newSlotMap = [];
        foreach ($newEffects as $eff) {
            $slot = $eff['slot'];
            $newSlotMap[$slot] = true;
            $old = $oldEffects[$slot] ?? null;
            $effParts = [];
            foreach (['effect_id', 'base1', 'base2'] as $field) {
                $oldVal = $old[$field] ?? 0;
                $newVal = $eff[$field] ?? 0;
                if ((string)$oldVal !== (string)$newVal) {
                    $effParts[] = "**{$field}:** {$newVal} *(**was**: {$oldVal})*";
                }
            }
            if ($effParts) {
                $prefix = $old === null ? "New Effect Slot {$slot}" : "Effect Slot {$slot}";
                $lines[] = "**{$prefix}:** " . implode(', ', $effParts);
            }
        }

        foreach ($oldEffects as $slot => $old) {
            if (!isset($newSlotMap[$slot])) {
                $lines[] = "**Deleted Effect Slot {$slot}:** effect_id: {$old['effect_id']}, base1: {$old['base1']}, base2: {$old['base2']}";
            }
        }

        $newAaIdMap = [];
        foreach ($newPrereqs as $p) {
            $aaId = $p['aa_id'];
            $newAaIdMap[$aaId] = true;
            $old = $oldPrereqs[$aaId] ?? null;
            $oldPoints = (string)($old['points'] ?? 0);
            $newPoints = (string)($p['points'] ?? 0);
            if ($oldPoints !== $newPoints) {
                $prefix = $old === null ? "New Prereq AA {$aaId}" : "Prereq AA {$aaId}";
                $lines[] = "**{$prefix}:** points: {$newPoints} *(**was**: {$oldPoints})*";
            }
        }

        foreach ($oldPrereqs as $aaId => $old) {
            if (!isset($newAaIdMap[$aaId])) {
                $lines[] = "**Deleted Prereq AA {$aaId}:** points: {$old['points']}";
            }
        }

        return $lines;
    }

    private function sendRankDiscordAlert(
        int $rankId,
        array $oldMain,
        array $newMain,
        array $oldEffects,
        array $newEffects,
        array $oldPrereqs,
        array $newPrereqs
    ): void {
        try {
            $lines = $this->buildRankChangeLines($rankId, $oldMain, $newMain, $oldEffects, $newEffects, $oldPrereqs, $newPrereqs);
            if (empty($lines)) return;

            $user = Auth::user()?->name ?? 'System';
            $aaName = $this->abilityNameForRank($rankId);
            $message = "[UPDATE] [AaRank] - **User**: {$user}, **AA:** {$aaName}, **id:** {$rankId}\n" . implode("\n", $lines);

            if (strlen($message) > 1950) {
                $message = substr($message, 0, 1950) . "\n... (truncated)";
            }

            DiscordAlert::message($message);
        } catch (\Throwable $e) {
            Log::error('AaRankController: Discord alert failed', ['error' => $e->getMessage()]);
        }
    }

    private function sendMultiRankDiscordAlert(array $prepared): void
    {
        try {
            $user = Auth::user()?->name ?? 'System';
            $sections = [];

            foreach ($prepared as $p) {
                $rankId = $p['rank']->id;
                $lines = $this->buildRankChangeLines(
                    $rankId,
                    $p['oldMain'],
                    $p['validatedMain'],
                    $p['oldEffects'],
                    $p['validatedEffects'],
                    $p['oldPrereqs'],
                    $p['validatedPrereqs']
                );

                if (!empty($lines)) {
                    $sections[] = "**Rank #{$rankId}:** " . implode(', ', $lines);
                }
            }

            if (empty($sections)) return;

            $aaName = $this->abilityNameForRank($prepared[0]['rank']->id);
            $message = "[BATCH UPDATE] [AaRank] - **User**: {$user}, **AA:** {$aaName}\n" . implode("\n", $sections);

            if (strlen($message) > 1950) {
                $message = substr($message, 0, 1950) . "\n... (truncated)";
            }

            DiscordAlert::message($message);
        } catch (\Throwable $e) {
            Log::error('AaRankController: Discord bulk alert failed', ['error' => $e->getMessage()]);
        }
    }

    private function abilityNameForRank(int $rankId): string
    {
        $currentId = $rankId;
        $visited = [];

        while ($currentId) {
            if (isset($visited[$currentId])) break;
            $visited[$currentId] = true;

            $rank = AaRank::select('id', 'prev_id')->find($currentId);
            if (!$rank) break;

            if (!$rank->prev_id || $rank->prev_id <= 0) {
                $ability = AaAbility::where('first_rank_id', $rank->id)->first();
                return $ability->name ?? 'Unknown';
            }

            $currentId = $rank->prev_id;
        }

        return 'Unknown';
    }

    public function destroy(AaRank $rank)
    {
        $rank->delete();

        return back()->with('success', 'AA Rank deleted.');
    }
}
