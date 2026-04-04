<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\TradeskillRecipeEntryRequest;
use App\Models\TradeskillContainerTemplate;
use App\Models\TradeskillRecipe;
use App\Models\TradeskillRecipeEntry;

class TradeskillRecipeEntryController extends Controller
{
    public function store(TradeskillRecipeEntryRequest $request)
    {
        $data = $request->validated();

        if (!empty($data['template_container_id'])) {

            $template = TradeskillContainerTemplate::with('items')
                ->find($data['template_container_id']);

            if ($template) {

                $recipe = TradeskillRecipe::findOrFail($data['recipe_id']);

                $entries = $template->items->map(function ($item) use ($data) {
                    return [
                        'item_id'        => $item->item_id,
                        'successcount'   => $data['successcount'] ?? 0,
                        'failcount'      => $data['failcount'] ?? 0,
                        'componentcount' => $data['componentcount'] ?? 1,
                        'salvagecount'   => $data['salvagecount'] ?? 0,
                        'iscontainer'    => 0,
                    ];
                })->toArray();

                $recipe->entries()->createMany($entries);

                toast()->success('Saved!', "Tradeskill Recipe Component(s) created from container template.");

                return response()->json([
                    'success'  => true,
                    'data'     => $recipe,
                    'redirect' => url()->previous(),
                ], 200);
            }
        }

        unset($data['template_container_id']);
        $model = TradeskillRecipeEntry::create($data);

        toast()->success('Saved!', "Tradeskill Recipe Component created.");

        return response()->json([
            'success'  => true,
            'data'     => $model,
            'redirect' => url()->previous(),
        ], 200);
    }

    public function update(TradeskillRecipeEntryRequest $request, TradeskillRecipeEntry $entry)
    {
        $data = $request->validated();
        $data['recipe_id'] = $entry->recipe_id;

        $entry->update($data);

        toast()->success('Saved!', "Tradeskill Recipe Component updated.");

        return response()->json([
            'success'  => true,
            'data'     => $entry,
            'redirect' => url()->previous(),
        ], 200);
    }

    public function destroy(TradeskillRecipeEntry $entry)
    {
        $entry->delete();

        return back()->with('success', 'Tradeskill Recipe Component deleted.');
    }
}
