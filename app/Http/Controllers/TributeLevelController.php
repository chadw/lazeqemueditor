<?php

namespace App\Http\Controllers;

use App\Http\Requests\TributeLevelRequest;
use App\Models\TributeLevel;
use App\Models\Tribute;
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

        $levelCount = TributeLevel::where('tribute_id', $tribute_id)->count();
        Tribute::where('id', $tribute_id)->update(['unknown' => $levelCount]);

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

        $updated = TributeLevel::where('tribute_id', $tribute_id)
            ->where('level', $level)
            ->update($updateData);

        if ($updated) {
            $entry->fill($updateData);
        }

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

        $levelCount = TributeLevel::where('tribute_id', $tribute_id)->count();
        Tribute::where('id', $tribute_id)->update(['unknown' => $levelCount]);

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
