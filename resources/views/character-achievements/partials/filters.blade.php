<form method="GET" action="{{ route('characters.achievements.show', $character->id) }}"
    class="grid grid-cols-1 gap-3 p-4 md:grid-cols-12 md:items-end">
    <div class="md:col-span-5 tooltip"
        data-tip="A numeric value checks the exact ID and also searches presentation text.">
        <label class="label label-text">Achievement ID, name, or description</label>
        <input
            type="search"
            name="q"
            value="{{ $metadata['filters']['q'] }}"
            class="input w-full"
            placeholder="Search this catalog page source"
        >
    </div>
    <div class="md:col-span-3 tooltip"
        data-tip="Matches a direct achievement-category association; it does not automatically include descendants.">
        <label class="label label-text">Category</label>
        <select name="category" class="select w-full">
            <option value="">All categories</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}"
                    @selected((string) $metadata['filters']['category'] === (string) $category->id)>
                    {{ $category->name }} [{{ $category->id }}]
                </option>
            @endforeach
        </select>
    </div>
    <div class="md:col-span-2 tooltip"
        data-tip="Computed only from persisted completion, progress, reward, and pending-update rows.">
        <label class="label label-text">Durable State</label>
        <select name="state" class="select select-bordered w-full">
            @foreach ($metadata['durable_states'] as $value => $label)
                <option value="{{ $value }}" @selected($metadata['filters']['state'] === $value)>
                    {{ $label }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="flex gap-2 md:col-span-2">
        <button type="submit" class="btn btn-soft btn-success flex-1">Filter</button>
        <a href="{{ route('characters.achievements.show', $character->id) }}" class="btn btn-soft btn-error">Clear</a>
    </div>
</form>
