<?php

namespace App\Http\Controllers;

use App\Models\FactionList;
use App\Filters\FactionAssociationFilter;
use Illuminate\Http\Request;
use App\Models\FactionAssociation;
use App\Http\Requests\FactionAssociationRequest;

class FactionAssociationController extends Controller
{
    public function index(Request $request)
    {
        $query = FactionAssociation::with([
            'factionList',
            'faction1', 'faction2', 'faction3', 'faction4', 'faction5',
            'faction6', 'faction7', 'faction8', 'faction9', 'faction10'
        ])
            ->orderBy(
                FactionList::select('name')
                ->whereColumn('faction_list.id', 'faction_association.id')
                ->limit(1)
            );

        $associations = (new FactionAssociationFilter($request))
            ->apply($query)
            ->paginate(50)
            ->withQueryString();

        return view('factions.associations.index', compact('associations'));
    }

    public function store(FactionAssociationRequest $request)
    {
        $model = FactionAssociation::create($request->validated());

        toast()->success('Saved!', 'Faction Association created.');

        return response()->json([
            'success' => true,
            'data'    => $model,
            'redirect'=> route('factions.associations.index'),
        ], 201);
    }

    public function update(FactionAssociationRequest $request, FactionAssociation $association)
    {
        $association->update($request->validated());

        toast()->success('Saved!', 'Faction Association updated.');

        return response()->json([
            'success' => true,
            'data'    => $association,
            'redirect'=> route('factions.associations.index'),
        ], 201);
    }

    public function destroy(FactionAssociation $association)
    {
        $association->delete();

        toast()->success('Deleted!', 'Faction Association deleted.');

        return back();
    }
}
