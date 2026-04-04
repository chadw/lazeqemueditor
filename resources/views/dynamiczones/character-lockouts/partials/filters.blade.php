<form method="GET" action="{{ url()->current() }}" class="flex gap-2 items-end">
    <div class="w-60">
        <label class="label label-text">Character</label>
        <input type="text" name="char" value="{{ request('char') }}"
            placeholder="Character id or name" class="input w-full" />
    </div>
    <div class="w-60">
        <label class="label label-text">Expedition</label>
        <input type="text" name="expedition" value="{{ request('expedition') }}"
            placeholder="Expedition name" class="input w-full" />
    </div>
    <div class="flex gap-2">
        <button type="submit" class="btn btn-soft btn-success">
            Filter
        </button>

        @if (request()->hasAny(['char', 'expedition']))
            <a href="{{ url()->current() }}" class="btn btn-soft btn-error">
                Reset
            </a>
        @endif
    </div>
</form>
