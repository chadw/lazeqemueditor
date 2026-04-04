@extends('layouts.app')
@section('page-title', 'Completed Shared Tasks')

@section('content')
    <div x-data @keydown.window.escape="if(!$store.modalForm.saving) $store.modalForm.close()">

        <x-top-links>
            <a href="{{ route('tasks.index') }}" class="btn btn-accent btn-soft">
                Back to Tasks
            </a>
            <div class="dropdown dropdown-bottom dropdown-end dropdown-hover">
                <div tabindex="0" role="button" class="btn btn-soft btn-info">Tasks Status</div>
                <ul tabindex="-1" class="dropdown-content menu bg-base-100 rounded-box z-1 w-60 p-2 shadow-sm">
                    <li><a href="{{ route('tasks.index') }}">Active</a></li>
                    <li><a href="{{ route('tasks.index') }}">Completed</a></li>
                </ul>
            </div>
            <div class="dropdown dropdown-bottom dropdown-end dropdown-hover">
                <div tabindex="0" role="button" class="btn btn-soft btn-info">Shared Tasks</div>
                <ul tabindex="-1" class="dropdown-content menu bg-base-100 rounded-box z-1 w-60 p-2 shadow-sm">
                    <li><a href="{{ route('tasks.shared-tasks.active') }}">Active</a></li>
                    <li><a href="{{ route('tasks.shared-tasks.completed') }}">Completed</a></li>
                </ul>
            </div>
        </x-top-links>

        <div class="card bg-base-100 shadow">
            <div class="border border-base-content/5 overflow-x-auto">
                <table class="table table-auto table-zebra md:table-fixed w-full">
                    <thead class="text-xs uppercase bg-neutral">
                        <tr>
                            <th scope="col" class="w-[10%]">Task ID</th>
                            <th scope="col">Name</th>
                            <th scope="col" class="w-[10%]">Accepted</th>
                            <th scope="col" class="w-[10%]">Expires</th>
                            <th scope="col" class="w-[5%]">Locked</th>
                            <th scope="col" class="w-[10%] text-right">-</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($tasks as $task)
                            <tr x-data data-task='@json($task)'>
                                <td scope="row">{{ $task->task_id }}</td>
                                <td>{{ $task->task->title }}</td>
                                <td>{{ $task->accepted_time->format('M d, Y H:i A') }}</td>
                                <td>{{ $task->expire_time?->format('M d, Y H:i A') ?? '-' }}</td>
                                <td>
                                    <x-status :ok="$task->is_locked" />
                                </td>
                                <td class="text-right">
                                    <div class="inline join">
                                        <button
                                            type="button"
                                            class="join-item btn btn-sm btn-soft"
                                            @click="$store.modalForm.openEdit(
                                                $el.closest('tr').dataset.task,
                                                '',
                                                {
                                                    modal: 'taskDetails',
                                                    resourceName: 'Task Details'
                                                }
                                            )">
                                            <x-ui.icon name="show" /> View Task
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4">{{ $tasks->links() }}</div>

        <x-ui.modal-show id="taskDetails">
            <div
                x-data="{
                    activityTypes: {{ Js::from(config('everquest.task_activity_type')) }},
                    classes: {{ Js::from(config('everquest.classes')) }},
                }
            ">
                @include('tasks.shared-tasks.partials.show')
            </div>
        </x-ui.modal-show>
    </div>
@endsection
