<form method="GET" action="{{ url()->current() }}" class="flex gap-2 items-end">
    <div class="w-44">
        <label class="label label-text">Class</label>
        <select name="class" class="select w-full">
            <option value="">All</option>
            @foreach (config('everquest.classes') as $k => $v)
                <option value="{{ $k }}" {{ (string) request('class') === (string) $k ? 'selected' : '' }}>
                    {{ $v }} ({{ $k }})</option>
            @endforeach
        </select>
    </div>

    <div class="w-28">
        <label class="label label-text">Level</label>
        <input type="number" name="level" value="{{ request('level') }}" min="1" max="110"
            placeholder="Level" class="input w-full" />
    </div>

    <div class="flex gap-2 items-center">
        <button type="submit" class="btn btn-soft btn-success">Filter</button>
        @if (request()->hasAny(['class', 'level']))
            <a href="{{ url()->current() }}" class="btn btn-soft btn-error">Reset</a>
        @endif
    </div>
</form>
