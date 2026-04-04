<form method="GET" action="{{ url()->current() }}" class="flex gap-2 items-end">
    <div class="w-60">
        <label class="label label-text">Dynamic Zone</label>
        <input type="text" name="dynamic_zone" value="{{ request('dynamic_zone') }}" class="input w-full"
            placeholder="Name contains...">
    </div>

    <div class="w-60">
        <label class="label label-text">Leader</label>
        <input type="text" name="leader" value="{{ request('leader') }}" class="input w-full"
            placeholder="Leader id or name">
    </div>

    <div class="flex gap-2">
        <button type="submit" class="btn btn-soft btn-success">
            Filter
        </button>

        @if (request()->hasAny(['dynamic_zone', 'event_name']))
            <a href="{{ url()->current() }}" class="btn btn-soft btn-error">
                Reset
            </a>
        @endif
    </div>
</form>
