<?php

namespace App\Http\Controllers;

use App\Http\Requests\LootDropEntryRequest;
use App\Models\LootDrop;
use App\Models\LootdropEntry;

class LootDropEntryController extends Controller
{
    public function store(LootDropEntryRequest $request, LootDrop $drop)
    {
        $data = $request->validated();
        $data['lootdrop_id'] = $drop->id;

        $model = new LootdropEntry($data);
        $model->silentSave();

        activity()
            ->performedOn($drop)
            ->useLog('loot')
            ->tap(function ($activity) use ($drop) {
                $activity->subject_type = LootdropEntry::class;
                $activity->subject_id = $drop->id;
            })
            ->event('created')
            ->withProperties(['attributes' => $data])
            ->log('created');

        toast()->success('Saved!', "Item added to loot drop.");

        return response()->json([
            'success'  => true,
            'data'     => $model->fresh(),
            'redirect' => url()->previous(),
        ], 200);
    }

    public function update(LootDropEntryRequest $request, LootDrop $drop, int $item)
    {
        $data = $request->validated();
        $data['lootdrop_id'] = $drop->id;

        $entry = LootdropEntry::where('lootdrop_id', $drop->id)
            ->where('item_id', $item)
            ->firstOrFail();
        $oldAttributes = $entry->getAttributes();

        LootdropEntry::where('lootdrop_id', $drop->id)
            ->where('item_id', $item)
            ->update($data);

        $entry->fill($data);
        $updated = $entry;

        activity()
            ->performedOn($entry)
            ->useLog('loot')
            ->tap(function ($activity) use ($drop) {
                $activity->subject_type = LootdropEntry::class;
                $activity->subject_id = $drop->id;
            })
            ->event('updated')
            ->withProperties(['attributes' => $data, 'old' => $oldAttributes])
            ->log('updated');

        toast()->success('Saved!', "Item added to loot drop.");

        return response()->json([
            'success'  => true,
            'data'     => $updated,
            'redirect' => url()->previous(),
        ], 200);
    }

    public function destroy(LootDrop $drop, int $item)
    {
        $entry = LootdropEntry::where('lootdrop_id', $drop->id)
            ->where('item_id', $item)
            ->firstOrFail();
        $oldAttributes = $entry->getAttributes();

        LootdropEntry::where('lootdrop_id', $drop->id)
            ->where('item_id', $item)
            ->delete();

        activity()
            ->performedOn($drop)
            ->useLog('loot')
            ->tap(function ($activity) use ($drop) {
                $activity->subject_type = LootdropEntry::class;
                $activity->subject_id = $drop->id;
            })
            ->event('deleted')
            ->withProperties(['old' => $oldAttributes])
            ->log('deleted');

        toast()->success('Deleted!', "Item removed from loot drop.");

        return back();
    }
}
