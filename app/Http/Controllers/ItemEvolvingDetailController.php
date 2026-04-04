<?php

namespace App\Http\Controllers;

use App\Http\Requests\ItemEvolvingDetailRequest;
use App\Models\ItemEvolvingDetail;
use App\Models\Zone;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ItemEvolvingDetailController extends Controller
{
    public function index(Request $request)
    {
        $perPage = (int) $request->input('per_page', 10);
        $groupQuery = ItemEvolvingDetail::select('item_evo_id')
            ->groupBy('item_evo_id')
            ->orderBy('item_evo_id');

        $groups = $groupQuery->paginate($perPage)->withQueryString();
        $groupIds = $groups->getCollection()->pluck('item_evo_id')->values()->all();

        $items = ItemEvolvingDetail::with('item')
            ->whereIn('item_evo_id', $groupIds)
            ->orderBy('item_evo_id')
            ->orderBy('item_evolve_level')
            ->get()
            ->groupBy('item_evo_id');

        $zones = Zone::selectZones();

        return view('items.evolving-items.index', compact('groups', 'items', 'zones'));
    }

    public function store(ItemEvolvingDetailRequest $request)
    {
        $data = $request->validated();

        try {
            $model = null;
            DB::transaction(function () use (&$model, $data) {
                $d = $data;
                if (empty($d['item_evo_id'])) {
                    $d['item_evo_id'] = ItemEvolvingDetail::max('item_evo_id') + 1;
                }

                $model = ItemEvolvingDetail::create($d);
            });

            toast()->success('Saved!', "Evolving detail created");

            return response()->json([
                'success' => true,
                'data'    => $model,
                'redirect' => url()->previous(),
            ], 201);
        } catch (\Throwable $e) {
            report($e);
            toast()->error('Save failed', 'Evolving detail not saved.');
            return response()->json([
                'success' => false,
                'message' => 'Save failed',
            ], 500);
        }
    }

    public function update(ItemEvolvingDetailRequest $request, ItemEvolvingDetail $evolving_item)
    {
        $data = $request->validated();

        $evolving_item->update($data);

        toast()->success('Saved!', "Evolving detail updated");

        return response()->json([
            'success' => true,
            'data'    => $evolving_item->fresh(),
            'redirect' => url()->previous(),
        ], 200);
    }

    public function destroy(ItemEvolvingDetail $evolving_item)
    {
        $evolving_item->delete();

        toast()->success('Deleted', 'Evolving detail delete.');

        return back();
    }

    public function options(): Collection
    {
        return ItemEvolvingDetail::evolvingOptions();
    }
}
