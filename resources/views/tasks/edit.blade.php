@extends('layouts.app')
@section('title', "Edit Task: {$task->title}")
@section('page-title', "Edit Task: {$task->title}")

@section('content')
    <div x-data="{
        task: {{ Js::from($task) }},
        types: {{ Js::from(config('everquest.task_type')) }},
        durations: {{ Js::from(config('everquest.task_duration')) }}
    }">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="md:col-span-3">
                <form method="POST" action="{{ route('tasks.update', $task) }}" id="task-edit-form">
                    @csrf
                    @method('PUT')
                    @include('tasks.forms.form', ['task' => $task])
                </form>

                <div class="divider"></div>

                @include('tasks.partials.index-activity', ['task' => $task])
            </div>

            <div class="md:col-span-1">
                @include('tasks.partials.preview', ['task' => $task])
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.store('taskActivities').set(
                @json($task->taskActivities->sortBy('activityid')->values())
            );
        });
    </script>
@endsection
