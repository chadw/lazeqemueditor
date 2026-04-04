<?php

namespace App\Http\Controllers;

use App\Models\TradeskillContainerTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\DiscordAlerts\Facades\DiscordAlert;

class TradeskillContainerTemplateController extends Controller
{
    public function index()
    {
        $tsContainers = collect(config('everquest.tradeskill_containers'))->keyBy('id');

        $templates = TradeskillContainerTemplate::with('items.item')
            ->withCount('items')
            ->orderBy('name')
            ->get()
            ->map(function ($template) use ($tsContainers) {

                $template->items->each(function ($templateItem) use ($tsContainers) {

                    if ($templateItem->item) {
                        $templateItem->resolved_item = [
                            'id'   => $templateItem->item->id,
                            'name' => $templateItem->item->Name,
                            'icon' => $templateItem->item->icon ?? null,
                        ];
                        return;
                    }

                    if ($tsContainers->has($templateItem->item_id)) {
                        $seed = $tsContainers[$templateItem->item_id];

                        $templateItem->resolved_item = [
                            'id'   => $seed['id'],
                            'name' => $seed['name'],
                            'icon' => $seed['icon'] ?? null,
                        ];
                    }
                });

                return $template;
            });

        return view('tradeskills.container-templates.index', compact('templates'));
    }

    public function store(Request $request)
    {
        $result = DB::transaction(function () use ($request, &$template, &$createdItems) {

            $template = TradeskillContainerTemplate::create(
                $request->only('name', 'skill')
            );

            $items = collect($request->items ?? [])
                ->pluck('item_id')
                ->filter()
                ->unique()
                ->values()
                ->map(fn($itemId) => ['item_id' => $itemId])
                ->toArray();

            $createdItems = collect();
            activity()->withoutLogs(function () use ($template, $items, &$createdItems) {
                if (!empty($items)) {
                    $createdItems = $template->items()->createMany($items);
                }
            });

            return [
                'template' => $template,
                'items' => $createdItems,
            ];
        });

        $template = $result['template'];
        $createdItems = $result['items'];

        $itemList = $createdItems->pluck('item_id')->implode(', ') ?: 'None';

        DiscordAlert::message(
            "[CREATED] [TradeskillContainerTemplate] '{$template->name}' "
                . "(Skill: {$template->skill}) - Items: {$itemList}"
        );

        toast()->success('Tradeskill Container Template', 'created.');

        return back();
    }

    public function update(Request $request, TradeskillContainerTemplate $tradeskillContainerTemplate)
    {
        $result = DB::transaction(function () use ($request, $tradeskillContainerTemplate) {

            $originalName  = $tradeskillContainerTemplate->name;
            $originalSkill = $tradeskillContainerTemplate->skill;

            $tradeskillContainerTemplate->update(
                $request->only('name', 'skill')
            );

            $items = collect($request->items ?? [])
                ->pluck('item_id')
                ->filter()
                ->unique()
                ->values()
                ->map(fn ($itemId) => ['item_id' => $itemId])
                ->toArray();

            activity()->withoutLogs(function () use ($tradeskillContainerTemplate, $items) {
                $tradeskillContainerTemplate->items()->delete();

                if (!empty($items)) {
                    $tradeskillContainerTemplate->items()->createMany($items);
                }
            });

            return [
                'originalName'  => $originalName,
                'originalSkill' => $originalSkill,
                'newName'       => $tradeskillContainerTemplate->name,
                'newSkill'      => $tradeskillContainerTemplate->skill,
                'items'         => collect($items)->pluck('item_id')->implode(', ') ?: 'None',
            ];
        });

        DiscordAlert::message(
            "[UPDATED] [TradeskillContainerTemplate] "
            . "'{$result['newName']}' "
            . "(Skill: {$result['newSkill']}) "
            . "- Items: {$result['items']}"
        );

        toast()->success('Saved!', 'Tradeskill Container Template updated.');

        return back();
    }

    public function destroy(TradeskillContainerTemplate $tradeskillContainerTemplate)
    {
        $tradeskillContainerTemplate->delete();

        return back();
    }

    public function items(TradeskillContainerTemplate $template)
    {
        return response()->json($template->items);
    }
}
