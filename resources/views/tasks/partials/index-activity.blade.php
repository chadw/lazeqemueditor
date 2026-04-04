<div x-data @keydown.window.escape="if(!$store.modalForm.saving) $store.modalForm.close()">
    <div class="card bg-base-100 card-sm shadow-sm">
        <div class="card-body">
            <h2 class="card-title flex items-center">
                Task Activities
                <button type="button" class="btn btn-sm btn-soft btn-success ml-auto"
                    @click="$store.modalForm.openCreate({
                        baseUrl: '{{ route('tasks.activities.store', $task) }}',
                        resourceName: 'Task Activity',
                    })">
                    New Task Activity
                </button>
            </h2>
            <div class="card bg-base-100 card-sm shadow-sm mb-4">
                <div class="border border-base-content/5 overflow-x-auto">
                    <table class="table table-auto table-zebra md:table-fixed w-full">
                        <thead class="text-sm uppercase bg-base-300">
                            <tr>
                                <th scope="col" class="w-[5%]">-</th>
                                <th scope="col" class="truncate w-[5%]">Activity ID</th>
                                <th scope="col" class="truncate w-[10%]">Req Activity ID</th>
                                <th scope="col" class="w-[5%]">Step</th>
                                <th scope="col" class="w-[10%]">Type</th>
                                <th scope="col">Target</th>
                                <th scope="col">Override</th>
                                <th scope="col" class="w-[5%]">Optional</th>
                                <th scope="col" class="w-[10%] text-right">-</th>
                            </tr>
                        </thead>
                        <tbody
                            x-data="activitySorter({
                                reorderUrl: '{{ route('tasks.activities.reorder', $task) }}'
                            })"
                            x-init="init()"
                        >
                            @foreach ($task->taskActivities as $activity)
                                <tr
                                    x-data
                                    data-activityid="{{ $activity->activityid }}"
                                    data-activity='@json($activity)'
                                    class="cursor-default"
                                >
                                    <td>
                                        <button type="button"
                                            class="btn btn-soft btn-sm btn-accent drag-handle cursor-grab"
                                            title="Drag to reorder">
                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                class="h-5 w-5">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <path d="M4 9a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" />
                                                <path d="M4 15a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" />
                                                <path d="M11 9a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" />
                                                <path d="M11 15a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" />
                                                <path d="M18 9a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" />
                                                <path d="M18 15a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" />
                                            </svg>
                                        </button>
                                    </td>
                                    <td data-activity-index>{{ $activity->activityid }}</td>
                                    <td>{{ $activity->req_activity_id }}</td>
                                    <td>{{ $activity->step }}</td>
                                    <td>{{ $activity->activity_type }}</td>
                                    <td>{{ $activity->target_name }}</td>
                                    <td class="truncate">
                                        {{ $activity->description_override ?: 'N/A' }}
                                    </td>
                                    <td class="text-center">
                                        <x-status :ok="$activity->optional" />
                                    </td>
                                    <td class="text-right">
                                        <div class="inline join">
                                            <button type="button"
                                                class="join-item btn btn-sm btn-soft"
                                                @click="
                                                    const activity = JSON.parse($el.closest('tr').dataset.activity);
                                                    $store.modalForm.openEdit(
                                                        activity,
                                                        '{{ route('tasks.activities.update', [
                                                            'task' => $task->id,
                                                            'activity' => $activity->activityid,
                                                        ]) }}',
                                                        {
                                                            resourceName: 'Edit Task Activity',
                                                            reqActivityOptions: $store.taskActivities.selectOptions({
                                                                excludeId: activity.activityid,
                                                            }),
                                                        }
                                                    )
                                                "
                                            >
                                                <x-ui.icon name="edit" />
                                            </button>
                                            <form action="{{ route('tasks.activities.destroy', [
                                                'task' => $task->id,
                                                'activity' => $activity->activityid,
                                            ]) }}"
                                                method="POST" class="inline">
                                                @csrf @method('DELETE')
                                                <button class="join-item btn btn-sm btn-soft btn-error"
                                                    onclick="return confirm('Delete?')">
                                                    <x-ui.icon name="delete" />
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <x-modal-form>
        @include('tasks.forms.activity')
    </x-modal-form>
</div>
