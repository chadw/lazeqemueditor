<?php

namespace App\Http\Controllers;

use App\Models\AaRank;
use App\Models\AaRankEffect;
use Illuminate\Http\Request;
use App\Http\Requests\AaRankEffectRequest;

class AaRankEffectController extends Controller
{
    public function store(Request $request, AaRank $rank)
    {
        $data = $request->validate([
            'slot'      => 'required|integer|min:1',
            'effect_id' => 'required|integer',
            'base1'     => 'nullable|integer',
            'base2'     => 'nullable|integer',
        ]);

        $data['rank_id'] = $rank->id;
        AaRankEffect::updateOrCreate(
            ['rank_id' => $rank->id, 'slot' => $data['slot']],
            $data
        );

        return back()->with('success', 'Effect saved.');
    }

    public function update(Request $request, AaRank $rank, int $slot)
    {
        $data = $request->validate([
            'effect_id' => 'required|integer',
            'base1'     => 'nullable|integer',
            'base2'     => 'nullable|integer',
        ]);
        AaRankEffect::where('rank_id', $rank->id)
            ->where('slot', $slot)
            ->update($data);

        return back()->with('success', 'Effect updated.');
    }

    public function destroy(AaRank $rank, int $slot)
    {
        AaRankEffect::where('rank_id', $rank->id)
            ->where('slot', $slot)
            ->delete();

        return back()->with('success', 'Effect deleted.');
    }
}
