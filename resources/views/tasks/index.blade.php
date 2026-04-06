@extends('layouts.app')
@section('page-title', 'Tasks')

@section('content')
    <div x-data>

        <x-top-links>
            <x-slot name="left">
                @include('tasks.partials.filters')
            </x-slot>
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

        <x-search-results :items="$tasks" title="Tasks">
            <x-ui.table>
                <x-slot:head>
                    <tr>
                        <x-th-sort field="id" label="ID" class="w-[5%]" />
                        <x-th-sort field="title" label="Name" />
                        <th scope="col" class="w-[10%] text-center">Min/Max Players</th>
                        <th scope="col" class="w-[10%] text-center">Min/Max Level</th>
                        <x-th-sort field="task_activities_count" label="Steps" class="w-[5%] text-center" />
                        <x-th-sort field="enabled" label="Enabled" class="w-[5%] text-center" />
                        <th scope="col" class="w-[15%] text-right">-</th>
                    </tr>
                </x-slot:head>
                <x-slot:body>
                    @foreach ($tasks as $task)
                        <tr>
                            <td>{{ $task->id }}</td>
                            <td>
                                <a href="{{ route('tasks.edit', $task) }}"
                                    class="text-base link-info link-hover">
                                    {{ $task->title }}
                                </a>
                            </td>
                            <td class="text-center">{{ $task->min_players }} - {{ $task->max_players }}</td>
                            <td class="text-center">{{ $task->min_level }} - {{ $task->max_level }}</td>
                            <td class="text-center">{{ $task->task_activities_count }}</td>
                            <td class="text-center">
                                <x-status :ok="$task->enabled" />
                            </td>
                            <td class="text-right">
                                <div class="join">
                                    <form action="{{ route('tasks.clone', $task) }}" method="POST" class="inline"
                                        onsubmit="return confirm('Clone this Task?')">
                                        @csrf
                                        <button class="join-item btn btn-sm btn-soft btn-accent tooltip" data-tip="Clone">
                                            <x-ui.icon name="clone" />
                                        </button>
                                    </form>
                                    <a href="{{ route('tasks.edit', $task) }}" data-tip="Edit"
                                        class="join-item btn btn-sm btn-soft">
                                        <x-ui.icon name="edit" />
                                    </a>
                                    <form action="{{ route('tasks.destroy', $task) }}" method="POST" class="inline"
                                        onsubmit="return confirm('Delete this Task?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="join-item btn btn-sm btn-soft btn-error tooltip"
                                            data-tip="Delete">
                                            <x-ui.icon name="delete" />
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </x-slot:body>
            </x-ui.table>
        </x-search-results>

        <div class="mt-4">
            {{ $tasks->links() }}
        </div>
    </div>
@endsection
