<?php

namespace App\Http\Controllers;

use App\Models\Zone;
use App\Models\QuestGlobal;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Filters\QuestGlobalFilter;
use App\Http\Requests\QuestGlobalRequest;

class QuestGlobalController extends Controller
{
    public function index(Request $request)
    {
        $hasFilters = $request->hasAny(['char', 'npc', 'zone', 'name']);

        $qglobals = (new QuestGlobalFilter($request))
            ->apply(QuestGlobal::query())
            ->with([
                'zone' => fn ($q) => $q->select('zoneidnumber', 'short_name', 'long_name'),
                'character' => fn ($q) => $q->select('id', 'name'),
                'npc' => fn ($q) => $q->select('id', 'name'),
            ])
            ->when(!$hasFilters, function ($query) {
                return $query->orderBy('name');
            })
            ->paginate(50)
            ->withQueryString();

        // get zones
        $zones = Zone::selectZones();

        return view('qglobals.index', compact('qglobals', 'zones'));
    }

    public function store(QuestGlobalRequest $request)
    {
        $data = $request->validated();
        $data['expdate'] = $data['expdate'] ? Carbon::parse($data['expdate'])->timestamp : null;

        QuestGlobal::create($data);

        return redirect()->route('qglobals.index')->with('success', 'Data Bucket created.');
    }

    public function update(QuestGlobalRequest $request)
    {
        $data = $request->validated();
        $fields = $request->only(['name', 'value', 'charid', 'npcid', 'zoneid', 'expdate']);
        $fields['expdate'] = $data['expdate'] ? Carbon::parse($data['expdate'])->timestamp : null;

        QuestGlobal::where('name', $data['old_name'])
            ->where('charid', $data['old_charid'])
            ->where('npcid', $data['old_npcid'])
            ->where('zoneid', $data['old_zoneid'])
            ->update($fields);

        return back()->with('success', 'Quest Global updated.');
    }

    public function destroy(Request $request)
    {
        QuestGlobal::where('name', $request->name)
            ->where('charid', $request->charid)
            ->where('npcid', $request->npcid)
            ->where('zoneid', $request->zoneid)
            ->delete();

        return back()->with('success', 'Quest global deleted.');
    }
}
