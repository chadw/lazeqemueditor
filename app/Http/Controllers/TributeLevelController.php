<?php

namespace App\Http\Controllers;

use App\Http\Requests\TributeLevelRequest;
use App\Models\TributeLevel;
use Illuminate\Support\Facades\DB;
use Spatie\DiscordAlerts\Facades\DiscordAlert;

class TributeLevelController extends Controller
{
    public function store(TributeLevelRequest $request, int $tribute_id)
    {
        $validated = $request->validated();

        $level = ($validated['level'] > 0)
            ? $validated['level']
            : (TributeLevel::where('tribute_id', $tribute_id)->max('level') ?? 0) + 1;

        $data = array_merge($validated, [
            'tribute_id' => $tribute_id,
            'level'      => (int)$level
        ]);

        $entry = new TributeLevel();
        $entry->fill($data);
        $entry->silentSave();

        $compositeId = "T:{$tribute_id}-L:{$level}";

        activity()
            ->performedOn($entry)
            ->useLog('tribute_level')
            ->event('created')
            ->tap(function ($activity) use ($compositeId) {
                $activity->subject_id = $compositeId;
            })
            ->withProperties(['attributes' => $data])
            ->log('created');

        $user = auth()->user()?->name ?? 'System';
        $msg = "[CREATED] [TributeLevel] - **User**: {$user}, **id**: {$compositeId}, " .
            "**level**: {$data['level']}, **cost**: {$data['cost']}, **item_id**: {$data['item_id']}";

        DiscordAlert::message($msg);

        toast()->success('Level Added', "Tribute #{$tribute_id} Level {$level} has been saved.");

        return back();
    }

    public function update(TributeLevelRequest $request, int $tribute_id, int $level)
    {
        $validated = $request->validated();

        $entry = TributeLevel::where('tribute_id', $tribute_id)
            ->where('level', $level)
            ->firstOrFail();

        $oldAttributes = $entry->toArray();
        $updateData = collect($validated)->except(['tribute_id', 'level'])->toArray();

        $entry->fill($updateData);
        $entry->silentSave();

        $compositeId = "T:{$tribute_id}-L:{$level}";

        activity()
            ->performedOn($entry)
            ->useLog('tribute_level')
            ->event('updated')
            ->tap(function ($activity) use ($compositeId) {
                $activity->subject_id = $compositeId;
            })
            ->withProperties([
                'attributes' => $updateData,
                'old' => $oldAttributes
            ])
            ->log('updated');

        $user = auth()->user()?->name ?? 'System';
        $costMsg = "{$entry->cost}" . ($entry->cost != $oldAttributes['cost'] ? " *(was: {$oldAttributes['cost']})*" : "");
        $itemMsg = "{$entry->item_id}" . ($entry->item_id != $oldAttributes['item_id'] ? " *(was: {$oldAttributes['item_id']})*" : "");

        $msg = "[UPDATED] [TributeLevel] - **User**: {$user}, **id**: {$compositeId}, " .
            "**cost**: {$costMsg}, **item_id**: {$itemMsg}";

        DiscordAlert::message($msg);

        toast()->success('Level Updated', "Tribute #{$tribute_id} Level {$level} has been saved.");

        return back();
    }

    public function destroy(int $tribute_id, int $level)
    {
        $entry = TributeLevel::where('tribute_id', $tribute_id)
            ->where('level', $level)
            ->firstOrFail();

        $oldAttributes = $entry->toArray();
        $compositeId = "T:{$tribute_id}-L:{$level}";

        TributeLevel::where('tribute_id', $tribute_id)
            ->where('level', $level)
            ->delete();

        DB::table('activity_log')->insert([
            'log_name'      => 'tribute_level',
            'description'   => 'deleted',
            'event'         => 'deleted',
            'subject_type'  => TributeLevel::class,
            'subject_id'    => $compositeId,
            'causer_type'   => \App\Models\User::class,
            'causer_id'     => auth()->id(),
            'properties'    => json_encode(['old' => $oldAttributes]),
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);

        $user = auth()->user()?->name ?? 'System';
        $msg = "[DELETED] [TributeLevel] - **User**: {$user}, **id**: {$compositeId}, " .
            "**level**: {$oldAttributes['level']}, **cost**: {$oldAttributes['cost']}, **item_id**: {$oldAttributes['item_id']}";

        DiscordAlert::message($msg);

        toast()->success('Level Removed', "Level {$level} has been deleted.");

        return back();
    }
}
