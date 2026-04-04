<form method="GET" action="{{ url()->current() }}" class="flex gap-2 items-end">
    <div class="w-52">
        <label class="label label-text">Character</label>
        <input type="text" name="character" value="{{ request('character') }}"
            placeholder="id or name" class="input w-full">
    </div>
    <div class="w-44">
        <label class="label label-text">Alt Currency</label>
        <select name="currency" class="select w-full">
            <option value="">All Alt Currencies</option>
            @foreach ($altCurrency->pluck('item.Name', 'id') as $value => $label)
                <option value="{{ $value }}" @selected(request('currency') == $value)>
                    {{ $label }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="flex gap-2">
        <button type="submit" class="btn btn-soft btn-success">
            Filter
        </button>

        @if (request()->hasAny(['character', 'currency']))
            <a href="{{ url()->current() }}" class="btn btn-soft btn-error">
                Reset
            </a>
        @endif
    </div>
</form>
