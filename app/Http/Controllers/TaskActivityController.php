<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\TaskActivityRequest;

class TaskActivityController extends Controller
{
    public function store(Task $task, TaskActivityRequest $request)
    {
        $data = $request->validated();
        $data['taskid'] = $task->id;

        $maxActivityId = TaskActivity::where('taskid', $task->id)->max('activityid') ?? -1;
        $data['activityid'] = $maxActivityId + 1;

        $activity = TaskActivity::create($data);

        return response()->json($activity);
    }

    public function update(Task $task, $activity, TaskActivityRequest $request)
    {
        $activity = TaskActivity::where('taskid', $task->id)
            ->where('activityid', $activity)
            ->firstOrFail();

        $activity->update($request->validated());

        toast()->success('Saved!', "Task Activity updated.");

        return response()->json([
            'success' => true,
            'data'    => $activity->fresh(),
            'redirect'=> url()->previous(),
        ], 200);
    }

    public function destroy(Task $task, $activity)
    {
        $activity = TaskActivity::where('taskid', $task->id)
            ->where('activityid', $activity)
            ->firstOrFail();

        $activity->delete();
        return response()->json(['success' => true]);
    }

    public function reorder(Request $request, Task $task)
    {
        $order = $request->input('order', []);

        if (empty($order)) {
            return response()->json(['message' => 'No order provided'], 422);
        }

        DB::transaction(function () use ($order, $task) {

            $tempBase = 1000;
            foreach (array_values($order) as $i => $oldActivityId) {
                TaskActivity::where('taskid', $task->id)
                    ->where('activityid', $oldActivityId)
                    ->update([
                        'activityid' => $tempBase + $i,
                    ]);
            }

            foreach (array_values($order) as $newIndex => $oldActivityId) {
                TaskActivity::where('taskid', $task->id)
                    ->where('activityid', $tempBase + $newIndex)
                    ->update([
                        'activityid' => $newIndex,
                    ]);
            }
        });

        $activities = $task->taskActivities()
            ->orderBy('activityid')
            ->get();

        return response()->json([
            'success' => true,
            'activities' => $activities,
        ]);
    }
}
