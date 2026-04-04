<form method="GET" action="{{ url()->current() }}" class="flex gap-2 items-end">
    <div class="w-60">
        <label class="label label-text">Character</label>
        <input type="text" name="char" value="{{ request('char') }}" class="input w-full"
            placeholder="Character ID or Name">
    </div>
    <div class="w-60">
        <label class="label label-text">Faction</label>
        <input type="text" name="faction" value="{{ request('faction') }}" class="input w-full"
            placeholder="Faction ID or Name">
    </div>
    <div class="flex gap-2">
        <button type="submit" class="btn btn-soft btn-success">
            Filter
        </button>

        @if (request()->hasAny(['char', 'faction']))
            <a href="{{ url()->current() }}" class="btn btn-soft btn-error">
                Reset
            </a>
        @endif
    </div>
</form>
