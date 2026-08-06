@extends('layouts.app')
@section('title', 'Achievements')
@section('page-title', 'Achievements')

@section('content')
    @php
        $eventTypes = $metadata['event_types'] ?? [];
    @endphp

    <div class="space-y-4">
        <x-top-links class="overflow-visible">
            <x-slot name="left">
                <form method="GET" action="{{ route('achievements.index') }}"
                    class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-7 gap-2 w-full">
                    <div class="form-control">
                        <label class="label py-1" for="achievement-filter-query">
                            <span class="label-text inline-flex items-center gap-1">ID or text
                                <x-ui.field-help text="Enter an exact numeric ID or part of an achievement name or description." />
                            </span>
                        </label>
                        <input id="achievement-filter-query" class="input w-full" type="search" name="q"
                            value="{{ request('q') }}" placeholder="ID, name, or description">
                    </div>
                    <div class="form-control">
                        <label class="label py-1" for="achievement-filter-category">
                            <span class="label-text inline-flex items-center gap-1">Category
                                <x-ui.field-help text="Show definitions directly associated with the selected client category." />
                            </span>
                        </label>
                        <select id="achievement-filter-category" class="select w-full" name="category_id">
                            <option value="">All categories</option>
                            @foreach($categories as $id => $label)
                                <option value="{{ $id }}" @selected((string) request('category_id') === (string) $id)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-control">
                        <label class="label py-1" for="achievement-filter-enabled">
                            <span class="label-text inline-flex items-center gap-1">Enable state
                                <x-ui.field-help text="Enabled definitions are eligible for the active server snapshot; disabled definitions remain drafts." />
                            </span>
                        </label>
                        <select id="achievement-filter-enabled" class="select w-full" name="enabled">
                            <option value="">Any enable state</option>
                            <option value="1" @selected(request('enabled') === '1')>Enabled</option>
                            <option value="0" @selected(request('enabled') === '0')>Disabled</option>
                        </select>
                    </div>
                    <div class="form-control">
                        <label class="label py-1" for="achievement-filter-event">
                            <span class="label-text inline-flex items-center gap-1">Event type
                                <x-ui.field-help text="Show definitions authored with the selected game event, including disabled criterion rows." />
                            </span>
                        </label>
                        <select id="achievement-filter-event" class="select w-full" name="event_type">
                            <option value="">Any event type</option>
                            @foreach($eventTypes as $value => $label)
                                <option value="{{ $value }}" @selected((string) request('event_type') === (string) $value)>
                                    {{ $value }}: {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-control">
                        <label class="label py-1" for="achievement-filter-reward">
                            <span class="label-text inline-flex items-center gap-1">Reward state
                                <x-ui.field-help text="Distinguish automatic completion grants, selectable reward sets, and definitions without rewards." />
                            </span>
                        </label>
                        <select id="achievement-filter-reward" class="select w-full" name="reward">
                            <option value="">Any reward state</option>
                            <option value="any" @selected(request('reward') === 'any')>Has rewards</option>
                            <option value="automatic" @selected(request('reward') === 'automatic')>Automatic grants</option>
                            <option value="selectable" @selected(request('reward') === 'selectable')>Selectable set</option>
                            <option value="none" @selected(request('reward') === 'none')>No rewards</option>
                        </select>
                    </div>
                    <div class="form-control">
                        <label class="label py-1" for="achievement-filter-page-size">
                            <span class="label-text inline-flex items-center gap-1">Page size
                                <x-ui.field-help text="Limits each server-side result page so large achievement catalogs remain responsive." />
                            </span>
                        </label>
                        <select id="achievement-filter-page-size" class="select w-full" name="per_page">
                            @foreach([25, 50, 100, 200] as $pageSize)
                                <option value="{{ $pageSize }}" @selected((int) request('per_page', 50) === $pageSize)>{{ $pageSize }} rows</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex flex-col justify-end">
                        <div class="join">
                            <button class="join-item btn btn-soft btn-info" type="submit">
                                <x-ui.icon name="search" /> Filter
                            </button>
                            <a class="join-item btn btn-soft" href="{{ route('achievements.index') }}">Clear</a>
                        </div>
                    </div>
                    @if(request('sort'))<input type="hidden" name="sort" value="{{ request('sort') }}">@endif
                    @if(request('direction'))<input type="hidden" name="direction" value="{{ request('direction') }}">@endif
                </form>
            </x-slot>
            <x-slot name="right">
                <a href="{{ route('achievement-categories.index') }}" class="btn btn-soft btn-accent"
                    title="Manage the achievement category tree">Categories</a>
                <a href="{{ route('achievements.create') }}" class="btn btn-soft btn-success"
                    title="Create a new achievement definition">
                    <x-ui.icon name="add" /> New Achievement
                </a>
            </x-slot>
        </x-top-links>

        <x-ui.alert-info>
            Saved definition changes become active only after <code>#reload achievements global</code> (or a zone restart).
            The zone validates the complete snapshot and keeps the previous snapshot active if validation fails.
        </x-ui.alert-info>

        <x-search-results :items="$achievements" title="Achievements">
            <x-ui.table>
                <x-slot:head>
                    <tr>
                        <x-th-sort field="id" label="ID" class="w-[8%]" />
                        <x-th-sort field="name" label="Name" />
                        <th class="w-[15%]">Categories</th>
                        <x-th-sort field="points" label="Points" class="w-[7%] text-center" />
                        <th class="w-[15%] text-center">Components / Policy / Rewards</th>
                        <x-th-sort field="definition_version" label="Version" class="w-[7%] text-center" />
                        <x-th-sort field="enabled" label="Enabled" class="w-[7%] text-center" />
                        <th class="w-[14%] text-right">-</th>
                    </tr>
                </x-slot:head>
                <x-slot:body>
                    @forelse($achievements as $achievement)
                        @php
                            $categoryLabels = collect($achievement->category_names ?? []);
                            if ($categoryLabels->isEmpty() && isset($achievement->associations)) {
                                $categoryLabels = collect($achievement->associations)
                                    ->map(fn($association) => data_get($association, 'category.name'))
                                    ->filter();
                            }
                            $componentCount = (int) ($achievement->components_count ?? 0);
                            $criteriaCount = (int) ($achievement->criteria_count ?? 0);
                            $rewardCount = (int) ($achievement->rewards_count ?? 0);
                            $hasSet = (bool) ($achievement->has_reward_set ?? $achievement->reward_set_exists ?? false);
                        @endphp
                        <tr>
                            <td class="tabular-nums">{{ $achievement->id }}</td>
                            <td>
                                <a href="{{ route('achievements.edit', $achievement->id) }}" class="link link-info font-medium">
                                    {{ $achievement->name ?: '(unnamed achievement)' }}
                                </a>
                                @if($achievement->description)
                                    <div class="text-xs opacity-60 line-clamp-1 mt-1">{{ $achievement->description }}</div>
                                @endif
                            </td>
                            <td>
                                @forelse($categoryLabels->take(2) as $label)
                                    <span class="badge badge-sm badge-soft mr-1">{{ $label }}</span>
                                @empty
                                    <span class="badge badge-sm badge-warning">Uncategorized</span>
                                @endforelse
                                @if($categoryLabels->count() > 2)
                                    <span class="text-xs opacity-60">+{{ $categoryLabels->count() - 2 }}</span>
                                @endif
                            </td>
                            <td class="text-center tabular-nums">{{ number_format((int) $achievement->points) }}</td>
                            <td class="text-center">
                                <span class="badge badge-soft tooltip" data-tip="Components">{{ $componentCount }}</span>
                                <span class="opacity-40">/</span>
                                <span class="badge badge-soft tooltip" data-tip="Criteria">{{ $criteriaCount }}</span>
                                <span class="opacity-40">/</span>
                                <span class="badge badge-soft tooltip" data-tip="Reward grants{{ $hasSet ? ' plus a selectable set' : '' }}">
                                    {{ $rewardCount }}{{ $hasSet ? '+' : '' }}
                                </span>
                            </td>
                            <td class="text-center tabular-nums">
                                {{ $achievement->definition_version }}
                                @if($achievement->reset_on_version_change)
                                    <span class="text-warning tooltip" data-tip="Version mismatch resets character state">*</span>
                                @endif
                            </td>
                            <td class="text-center"><x-status :ok="$achievement->enabled" /></td>
                            <td class="text-right">
                                <div class="join">
                                    <form method="POST" action="{{ route('achievements.clone', $achievement->id) }}"
                                        onsubmit="return confirm('Clone this definition as a new disabled achievement? Cast restrictions will not be copied.')">
                                        @csrf
                                        <button class="join-item btn btn-sm btn-soft btn-accent tooltip" data-tip="Safe clone">
                                            <x-ui.icon name="clone" />
                                        </button>
                                    </form>
                                    <a href="{{ route('achievements.edit', $achievement->id) }}"
                                        class="join-item btn btn-sm btn-soft tooltip" data-tip="Edit">
                                        <x-ui.icon name="edit" />
                                    </a>
                                    <form method="POST" action="{{ route('achievements.destroy', $achievement->id) }}"
                                        onsubmit="return confirm('Delete this definition and its content graph? Character history is preserved. Disabling is usually safer for deployed achievements.')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="join-item btn btn-sm btn-soft btn-error tooltip" data-tip="Delete definition">
                                            <x-ui.icon name="delete" />
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center italic opacity-60">No achievements found.</td></tr>
                    @endforelse
                </x-slot:body>
            </x-ui.table>
        </x-search-results>

        <div>{{ $achievements->links() }}</div>
    </div>
@endsection
