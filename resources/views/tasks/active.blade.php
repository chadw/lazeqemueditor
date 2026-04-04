@extends('layouts.app')
@section('page-title', 'Active Tasks')

@section('content')
    <div x-data @keydown.window.escape="if(!$store.modalForm.saving) $store.modalForm.close()">

        <x-top-links>
            <a href="{{ route('tasks.index') }}" class="btn btn-accent btn-soft">
                Back to Tasks
            </a>
            <div class="dropdown dropdown-bottom dropdown-end dropdown-hover z-90">
                <div tabindex="0" role="button" class="btn btn-soft btn-info">Tasks Status</div>
                <ul tabindex="-1" class="dropdown-content menu bg-base-100 rounded-box z-1 w-60 p-2 shadow-sm">
                    <li><a href="{{ route('tasks.active') }}">Active</a></li>
                    <li><a href="{{ route('tasks.completed') }}">Completed</a></li>
                </ul>
            </div>
            <div class="dropdown dropdown-bottom dropdown-end dropdown-hover z-90">
                <div tabindex="0" role="button" class="btn btn-soft btn-info">Shared Tasks</div>
                <ul tabindex="-1" class="dropdown-content menu bg-base-100 rounded-box z-1 w-60 p-2 shadow-sm">
                    <li><a href="{{ route('tasks.shared-tasks.active') }}">Active</a></li>
                    <li><a href="{{ route('tasks.shared-tasks.completed') }}">Completed</a></li>
                </ul>
            </div>
        </x-top-links>

        <x-ui.table>
            <x-slot:head>
                <tr>
                    <x-th-sort field="character" label="Character" class="w-[20%]" />
                    <x-th-sort field="task" label="Task" />
                    <x-th-sort field="type" label="Type" class="w-[10%]" />
                    <th scope="col" class="w-[10%]">Accepted</th>
                    <th scope="col" class="w-[5%]">Rewarded</th>
                    <th scope="col" class="w-[10%] text-right">-</th>
                </tr>
            </x-slot:head>
            <x-slot:body>
                @foreach ($tasks as $task)
                    <tr
                        x-data
                        data-task='@json($task)'
                        class="{{ $task->task ? '' : 'bg-error/20! text-error' }}"
                    >
                        <td>
                            {{ $task->character->name }}
                            <span class="badge badge-sm badge-soft ml-1">
                                {{ $task->charid }}
                            </span>
                        </td>
                        <td>
                            {{ $task->task->title ?? 'Missing Task' }}
                            <span class="badge badge-sm badge-soft ml-1">
                                {{ $task->taskid }}
                            </span>
                        </td>
                        <td>{{ config('everquest.task_type')[$task->type] ?? 'Unknown' }}</td>
                        <td>{{ $task->acceptedtime->format('M d, Y H:i A') }}</td>
                        <td>
                            <x-status :ok="$task->was_rewarded" />
                        </td>
                        <td class="text-right">
                            <div class="inline join">
                                <form action="{{ route('tasks.delete-active', [$task->taskid, $task->charid]) }}"
                                    method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-soft btn-error tooltip"
                                        data-tip="Delete"
                                        onclick="return confirm('Delete Active Task?')">
                                        <x-ui.icon name="delete" />
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </x-slot:body>
        </x-ui.table>

        <div class="mt-4">{{ $tasks->links() }}</div>
    </div>
@endsection
