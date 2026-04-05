<?php

namespace App\Http\Controllers;

use App\Filters\AaAbilityFilter;
use App\Http\Requests\AaAbilityRequest;
use App\Models\AaAbility;
use App\Models\AaRank;
use App\Models\AaRankEffect;
use App\Models\AaRankPrereq;
use App\Services\AaRankService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\DiscordAlerts\Facades\DiscordAlert;

class AaAbilityController extends Controller
{
    public function index(Request $request)
    {
        $query = AaAbility::orderBy('name');

        $abilities = (new AaAbilityFilter($request))
            ->apply($query)
            ->paginate(25)
            ->withQueryString();

        $allAbilities = AaAbility::orderBy('name')->get();

        return view('aa.index', compact('abilities', 'allAbilities'));
    }

    public function create()
    {
        $allAbilities = AaAbility::orderBy('name')->get();

        return view('aa.create', compact('allAbilities'));
    }

    public function edit(AaAbility $ability, AaRankService $rankService)
    {
        $ids = [];

        $current = $ability->firstRank()
            ->select('id', 'next_id')
            ->first();

        while ($current) {
            $ids[] = $current->id;

            if (!$current->next_id) {
                break;
            }

            $current = AaRank::query()
                ->select('id', 'next_id')
                ->where('id', $current->next_id)
                ->first();
        }

        $ranks = AaRank::whereIn('id', $ids)
            ->with([
                'effects',
                'prereqs.ability',
                'spell_',
            ])
            ->get()
            ->keyBy('id');

        $orderedRanks = collect();
        $currentId = $ability->first_rank_id;

        while ($currentId) {
            $rank = $ranks[$currentId] ?? null;

            if (!$rank) {
                break;
            }

            $orderedRanks->push($rank);
            $currentId = $rank->next_id;
        }

        $rankCount = $orderedRanks->count();

        $allAbilities = AaAbility::orderBy('name')->get();

        return view('aa.edit', [
            'ability' => $ability,
            'allRanks' => $orderedRanks,
            'rankCount' => $rankCount,
            'allAbilities' => $allAbilities,
        ]);
    }

    public function store(AaAbilityRequest $request)
    {
        $data = $request->validated();

        if (empty(trim($data['name'] ?? ''))) {
            $data['name'] = 'System AA ' . now()->format('YmdHis');
        }

        $model = AaAbility::create($data);
        toast()->success('Saved!', 'AA Ability created.');

        return response()->json([
            'success'  => true,
            'data'     => $model->fresh(),
            'redirect' => url()->previous(),
        ], 201);
    }

    public function update(AaAbilityRequest $request, AaAbility $ability)
    {
        $data = $request->validated();

        $ability->update($data);
        toast()->success('Saved!', 'AA Ability updated.');

        return response()->json([
            'success'  => true,
            'data'     => $ability->fresh(),
            'redirect' => url()->previous(),
        ], 201);
    }

    public function destroy(AaAbility $ability)
    {
        DB::transaction(function () use ($ability) {
            $ids = [];

            $current = $ability->first_rank_id ? AaRank::query()
                ->select('id', 'next_id')
                ->where('id', $ability->first_rank_id)
                ->first() : null;

            while ($current) {
                $ids[] = $current->id;
                if (!$current->next_id) {
                    break;
                }

                $current = AaRank::query()
                    ->select('id', 'next_id')
                    ->where('id', $current->next_id)
                    ->first();
            }

            if (!empty($ids)) {
                AaRankEffect::whereIn('rank_id', $ids)->delete();
                AaRankPrereq::whereIn('rank_id', $ids)->delete();
                AaRank::whereIn('id', $ids)->delete();
            }

            $ability->delete();
        });

        toast()->success('Deleted!', 'AA ability and related ranks deleted.');

        return redirect()->route('aa.index');
    }

    public function clone(Request $request, AaAbility $ability)
    {
        $new = $ability->replicate();

        $suffix = ' (Copy)';
        $newName = $ability->name . $suffix;
        if (AaAbility::where('name', $newName)->exists()) {
            $newName = $ability->name . $suffix . ' ' . now()->format('YmdHis');
        }

        $new->name = $newName;

        // count for discord msg
        $rankCount = 0;
        $prereqCount = 0;

        try {
            Model::withoutEvents(function () use (&$new, $ability, &$rankCount, &$prereqCount) {
                $conn = DB::connection('eqemu');

                $conn->transaction(function () use (&$new, $ability, &$rankCount, &$prereqCount, $conn) {
                    // rank chain
                    $ranksToClone = [];
                    $rptr = $ability->first_rank_id ? AaRank::find($ability->first_rank_id) : null;
                    while ($rptr) {
                        $ranksToClone[] = $rptr;
                        $rptr = $rptr->next_id > 0 ? AaRank::find($rptr->next_id) : null;
                    }

                    // reserve ids for ranks
                    $nextRankId = (int) ($conn->table('aa_ranks')->lockForUpdate()->max('id') ?? 0);
                    $oldToNewRank = [];
                    foreach ($ranksToClone as $r) {
                        $nextRankId++;
                        $oldToNewRank[$r->id] = $nextRankId;
                    }

                    // clone ability with first_rank_id set to new id (if any)
                    $table = $new->getTable();
                    $max = $conn->table($table)->lockForUpdate()->max('id');
                    $newId = (($max ?? 0) + 1);

                    $attrs = $new->getAttributes();
                    $attrs['id'] = $newId;
                    $attrs['first_rank_id'] = isset($oldToNewRank[$ability->first_rank_id]) ? $oldToNewRank[$ability->first_rank_id] : 0;
                    $attrs['enabled'] = $attrs['enabled'] ?? 0;
                    $attrs['grant_only'] = $attrs['grant_only'] ?? 0;
                    $attrs['reset_on_death'] = $attrs['reset_on_death'] ?? 0;
                    $attrs['auto_grant_enabled'] = $attrs['auto_grant_enabled'] ?? 0;

                    $conn->table($table)->insert($attrs);
                    $new = AaAbility::find($newId);

                    // insert ranks using allocated ids and set prev/next mapped values
                    foreach ($ranksToClone as $r) {
                        $rid = $oldToNewRank[$r->id];
                        $rattrs = $r->getAttributes();
                        $rattrs['id'] = $rid;
                        $rattrs['prev_id'] = isset($oldToNewRank[$r->prev_id]) ? $oldToNewRank[$r->prev_id] : 0;
                        $rattrs['next_id'] = isset($oldToNewRank[$r->next_id]) ? $oldToNewRank[$r->next_id] : 0;

                        $conn->table('aa_ranks')->insert($rattrs);
                        $rankCount++;

                        // clone effects
                        foreach ($r->effects()->get() as $eff) {
                            $conn->table('aa_rank_effects')->updateOrInsert(
                                ['rank_id' => $rid, 'slot' => $eff->slot],
                                [
                                    'rank_id' => $rid,
                                    'slot' => $eff->slot,
                                    'effect_id' => $eff->effect_id ?? 0,
                                    'base1' => $eff->base1 ?? 0,
                                    'base2' => $eff->base2 ?? 0,
                                ]
                            );
                        }

                        // clone prereqs
                        foreach ($r->prereqs()->get() as $pr) {
                            $conn->table('aa_rank_prereqs')->updateOrInsert(
                                ['rank_id' => $rid, 'aa_id' => $pr->aa_id],
                                ['rank_id' => $rid, 'aa_id' => $pr->aa_id, 'points' => $pr->points ?? 0]
                            );
                            $prereqCount++;
                        }
                    }
                });
            });

            try {
                $userName = auth()->user()?->name ?? 'System';
                $message = "[CLONED] [AaAbility] **User**: {$userName}, **Original:** ({$ability->id}) {$ability->name}, **Cloned to:** ({$new->id}) {$new->name} ({$rankCount} ranks, {$prereqCount} prereqs)";
                DiscordAlert::message($message);
            } catch (\Throwable $e) {
            }

            toast()->success('Cloned!', 'AA ability cloned.');

            return redirect()->route('aa.edit', $new);
        } catch (\Throwable $e) {
            toast()->error('Clone failed', 'AA clone rolled back.');

            return redirect()->route('aa.index');
        }
    }

    public function search(Request $request)
    {
        $search = $request->string('q');

        return AaAbility::query()
            ->select('id', 'name')
            ->when($search, function ($q) use ($search) {
                $q->where('id', $search)
                    ->orWhere('name', 'like', "%{$search}%");
            })
            ->orderBy('id')
            ->limit(50)
            ->get();
    }
}
