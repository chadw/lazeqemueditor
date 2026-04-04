<?php

namespace App\Http\Controllers;

use App\Models\AaRank;
use Illuminate\Http\Request;

class AaRankPrereqController extends Controller
{
    public function store(Request $request, AaRank $rank)
    {
        $rank->prereqs()->updateOrCreate(
            ['aa_id' => $request->aa_id],
            $request->validated()
        );

        return back();
    }

    public function destroy(AaRank $rank, $aaId)
    {
        $rank->prereqs()->where('aa_id', $aaId)->delete();
        return back();
    }
}
