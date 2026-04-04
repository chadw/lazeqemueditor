<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Task;
use App\Models\Spell;
use App\Models\NpcType;
use Illuminate\Http\Request;

class IdPickerController extends Controller
{
    public function freeIds(Request $request)
    {
        $type = $request->query('type', 'spells');

        $modelMap = [
            'spells' => Spell::class,
            'items'  => Item::class,
            'npcs'   => NpcType::class,
            'tasks'  => Task::class,
        ];

        $modelClass = $modelMap[$type] ?? Spell::class;

        if (!class_exists($modelClass)) {
            return response()->json([]);
        }

        try {
            $ids = $modelClass::query()->orderBy('id')->pluck('id')->toArray();
        } catch (\Throwable $e) {
            return response()->json([]);
        }

        $blocks = [];
        $prev = 0;
        foreach ($ids as $id) {
            $gap = $id - $prev - 1;
            if ($gap >= 10) {
                $start = $prev + 1;
                $end = $id - 1;
                $blocks[] = [
                    'start' => $start,
                    'end'   => $end,
                    'count' => $end - $start + 1,
                ];
            }
            $prev = $id;
        }

        $max = $ids ? end($ids) : 0;
        $tailStart = $max + 1;
        $tailEnd = $max + 1000;
        if ($tailEnd - $tailStart + 1 >= 10) {
            $blocks[] = ['start' => $tailStart, 'end' => $tailEnd, 'count' => $tailEnd - $tailStart + 1];
        }

        return response()->json($blocks);
    }
}
