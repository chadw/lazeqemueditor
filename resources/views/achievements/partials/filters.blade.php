<form method="GET" action="{{ url()->current() }}" class="flex gap-2 items-end">
    <div class="w-60">
        <label class="label py-1" for="achievement-filter-query">
            <span class="label-text inline-flex items-center gap-1">ID or text
                <x-ui.field-help text="Enter an exact numeric ID or part of an achievement name or description." />
            </span>
        </label>
        <input id="achievement-filter-query" class="input w-full" type="search" name="q"
            value="{{ request('q') }}" placeholder="ID, name, or description">
    </div>
    <div class="w-44">
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
    <div class="w-44">
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
    <div class="w-44">
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
    <div class="w-44">
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
    <div class="w-28">
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
    <div class="flex gap-2">
        <button type="submit" class="btn btn-soft btn-success">
            Filter
        </button>

        @if(request()->hasAny([
            'q','category_id','enabled','event_type','reward','per_page'
        ]))
            <a href="{{ url()->current() }}" class="btn btn-soft btn-error">
                Reset
            </a>
        @endif
    </div>
    @if(request('sort'))<input type="hidden" name="sort" value="{{ request('sort') }}">@endif
    @if(request('direction'))<input type="hidden" name="direction" value="{{ request('direction') }}">@endif
</form>
