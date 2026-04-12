<form method="GET" action="{{ url()->current() }}" class="flex gap-2 items-end">
    <div class="w-60">
        <label class="label label-text">Loot Table</label>
        <input type="text" name="table" value="{{ request('table') }}" class="input w-full"
            placeholder="Loot Table ID or Name">
    </div>
    <div class="w-60">
        <label class="label label-text">Item</label>
        <input type="text" name="item" value="{{ request('item') }}" class="input w-full"
            placeholder="Item ID or Name">
    </div>
    <div class="flex gap-2">
        <button type="submit" class="btn btn-soft btn-success">
            Filter
        </button>

        @if (request()->hasAny(['table']))
            <a href="{{ url()->current() }}" class="btn btn-soft btn-error">
                Reset
            </a>
        @endif
    </div>
</form>
