<?php

namespace App\Http\Controllers;

use App\Filters\AaAbilityFilter;
use App\Http\Requests\AaAbilityRequest;
use App\Models\AaAbility;
use App\Models\AaRank;
use App\Models\AaRankEffect;
use App\Models\AaRankPrereq;
use App\Services\AaRankService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

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

        DB::transaction(function () use (&$new, $ability) {
            $new->save();

            $oldToNewRank = [];
            $rank = AaRank::find($ability->first_rank_id);
            while ($rank) {
                $rnew = $rank->replicate();
                $rnew->id = null;
                $rnew->prev_id = 0;
                $rnew->next_id = 0;
                $rnew->save();

                $oldToNewRank[$rank->id] = $rnew->id;

                // clone effects
                foreach ($rank->effects()->get() as $eff) {
                    $neff = $eff->replicate();
                    $neff->rank_id = $rnew->id;
                    $neff->save();
                }

                // clone prereqs
                foreach ($rank->prereqs()->get() as $pr) {
                    $npr = $pr->replicate();
                    $npr->rank_id = $rnew->id;
                    $npr->save();
                }

                $rank = $rank->next_id > 0 ? AaRank::find($rank->next_id) : null;
            }

            foreach ($oldToNewRank as $oldId => $newId) {
                $old = AaRank::find($oldId);
                $n = AaRank::find($newId);
                $n->prev_id = ($old->prev_id && isset($oldToNewRank[$old->prev_id])) ? $oldToNewRank[$old->prev_id] : 0;
                $n->next_id = ($old->next_id && isset($oldToNewRank[$old->next_id])) ? $oldToNewRank[$old->next_id] : 0;
                $n->save();
            }

            $firstOld = $ability->first_rank_id;
            if ($firstOld && isset($oldToNewRank[$firstOld])) {
                $new->first_rank_id = $oldToNewRank[$firstOld];
                $new->save();
            }
        });

        return response()->json(['success' => true, 'redirect' => route('aa.edit', $new)]);
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
