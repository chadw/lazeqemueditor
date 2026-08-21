<form method="GET" action="{{ route('character-achievements.index') }}" class="flex flex-wrap items-end gap-2">
    <div class="w-44">
        <label class="label label-text">Name or ID</label>
        <input type="search" name="q" value="{{ request('q') }}"
            class="input input-bordered w-full" placeholder="e.g. 42 or Firiona"
            aria-label="Search by character ID or name">
    </div>
    <div class="flex gap-2 items-center">
        <button type="submit" class="btn btn-soft btn-success">Filter</button>
        @if (request()->hasAny(['q']))
            <a href="{{ url()->current() }}" class="btn btn-soft btn-error">Reset</a>
        @endif
    </div>
</form>
