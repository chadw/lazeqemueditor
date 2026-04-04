<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CompletedSharedTask;
use App\Models\SharedTask;

class SharedTaskController extends Controller
{
    public function active(Request $request)
    {
        $tasks = SharedTask::with([
            'task',
            'members.character',
            'activityStates',
            'taskActivities',
            'dz'
        ])
        ->paginate(50)
        ->withQueryString();

        return view('tasks.shared-tasks.active', compact('tasks'));
    }

    public function completed(Request $request)
    {
        $tasks = CompletedSharedTask::with([
            'task',
            'members.character',
            'activityStates',
            'taskActivities',
            //'dz'
        ])
        ->paginate(50)
        ->withQueryString();

        return view('tasks.shared-tasks.completed', compact('tasks'));
    }
}
