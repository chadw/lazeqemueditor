<form method="GET" action="{{ route('spells.index') }}" class="flex gap-2 items-end">
    <div class="w-64">
        <label class="label label-text">Spell</label>
        <input
            type="text"
            name="name"
            value="{{ request('name') }}"
            placeholder="Spell id or Name"
            class="input w-full"
        />
    </div>
    <div class="w-60">
        <label class="label label-text">Class</label>
        <select name="class" class="select w-full">
            <option value="all">All Classes</option>
            @foreach ($classes as $key => $label)
                <option value="{{ $key }}" @selected(request('class') === $key)>
                    {{ $label }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="flex gap-2">
        <button type="submit" class="btn btn-soft btn-success">
            Filter
        </button>
        @if(request()->hasAny(['q', 'class']))
            <a href="{{ url()->current() }}" class="btn btn-soft btn-error">
                Reset
            </a>
        @endif
    </div>
</form>
