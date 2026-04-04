<form method="GET" action="{{ url()->current() }}" class="flex gap-2 items-end">
    <div class="w-52">
        <label class="label label-text">Name</label>
        <input type="text" name="name" value="{{ request('name') }}"
            placeholder="id or name" class="input w-full">
    </div>
    <div class="flex gap-2">
        <button type="submit" class="btn btn-soft btn-success">
            Filter
        </button>

        @if (request()->hasAny(['name']))
            <a href="{{ url()->current() }}" class="btn btn-soft btn-error">
                Reset
            </a>
        @endif
    </div>
</form>
