<?php

namespace App\Http\Controllers;

use App\Models\LdonTrapEntry;
use App\Models\LdonTrapTemplate;
use Illuminate\Http\Request;

class LdonTrapEntryController extends Controller
{
    public function store(Request $request, LdonTrapTemplate $trapTemplate)
    {
        $trapTemplate->entries()->create([]);

        return back()->with('success', 'Trap entry added.');
    }

    public function destroy(LdonTrapEntry $trapEntry)
    {
        $trapEntry->delete();

        return back()->with('success', 'Trap entry deleted.');
    }
}
