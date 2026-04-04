<?php

namespace App\Http\Controllers;

use App\Filters\TradeskillFilter;
use App\Http\Controllers\Controller;
use App\Http\Requests\TradeskillRecipeRequest;
use App\Models\BaseModel;
use App\Models\TradeskillContainerTemplate;
use App\Models\TradeskillRecipe;
use Illuminate\Http\Request;
use Spatie\DiscordAlerts\Facades\DiscordAlert;

class TradeskillRecipeController extends Controller
{
    public function index(Request $request)
    {
        $tradeskills = collect(config('everquest.skills.tradeskill'))->sort();

        $recipes = (new TradeskillFilter($request))
            ->apply(TradeskillRecipe::query()->with('resultEntries.item'))
            ->sortable('id')
            ->paginate(25)
            ->withQueryString();

        return view('tradeskills.index', compact('recipes', 'tradeskills'));
    }

    public function store(TradeskillRecipeRequest $request)
    {
        $data = $request->validated();
        $data['must_learn'] = TradeskillRecipe::buildLearnValue(
            (int) $request->l_method,
            (int) $request->l_message,
            (int) $request->l_search
        );

        $recipe = TradeskillRecipe::create($data);

        toast()->success('Saved!', 'Recipe created.');

        return response()->json([
            'success'  => true,
            'data'     => $recipe,
            'redirect' => route('tradeskills.edit', $recipe),
        ], 201);
    }

    public function edit(TradeskillRecipe $recipe)
    {
        $recipe->load('entries.item', 'learnedByItem');

        $containerTemplates = TradeskillContainerTemplate::withCount('items')
            ->get()
            ->map(function ($template) {
                return [
                    'id'          => 'template_' . $template->id,
                    'name'        => "[Template] {$template->name}",
                    'icon'        => 'container-template',
                    'template_id' => $template->id,
                ];
            });

        $tradeskills = collect(config('everquest.skills.tradeskill'))->sort();

        return view('tradeskills.edit', compact(
            'recipe',
            'tradeskills',
            'containerTemplates',
        ));
    }

    public function update(TradeskillRecipeRequest $request, TradeskillRecipe $recipe)
    {
        $data = $request->validated();
        $data['must_learn'] = TradeskillRecipe::buildLearnValue(
            (int) $request->l_method,
            (int) $request->l_message,
            (int) $request->l_search
        );

        $recipe->update($data);

        toast()->success('Saved!', 'Recipe updated.');

        return response()->json([
            'success'  => true,
            'data'     => $recipe,
            'redirect' => url()->previous(),
        ], 201);
    }

    public function destroy(TradeskillRecipe $recipe)
    {
        $recipe->delete();

        toast()->success('Saved!', 'Recipe deleted.');

        if (str_contains(url()->previous(), '/edit')) {
            return redirect()->route('tradeskills.index');
        }

        return back();
    }

    public function clone(TradeskillRecipe $recipe)
    {
        $newRecipe = BaseModel::withoutEvents(function () use ($recipe) {
            return $recipe->cloneWithEntries();
        });

        $user = auth()->user()?->name ?? 'System';
        $message = "[CLONED] [TradeskillRecipe] - **User**: {$user}, **Recipe:** ({$recipe->id}) {$recipe->name}, " .
            "**Cloned to:** ({$newRecipe->id}) {$newRecipe->name}";
        DiscordAlert::message($message);

        return redirect()
            ->route('tradeskills.edit', $newRecipe)
            ->with('success', 'Recipe cloned');
    }
}
