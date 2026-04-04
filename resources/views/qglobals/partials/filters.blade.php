<form method="GET" action="{{ url()->current() }}" class="flex gap-2 items-end">
    <div class="w-52">
        <label class="label label-text">Name</label>
        <input type="text" name="name" value="{{ request('name') }}" class="input w-full">
    </div>
    <div class="w-52">
        <label class="label label-text">Character</label>
        <input type="text" name="character" value="{{ request('character') }}"
            placeholder="id or name" class="input w-full">
    </div>
    <div class="w-52">
        <label class="label label-text">NPC</label>
        <input type="text" name="npc" value="{{ request('npc') }}"
            placeholder="id or name" class="input w-full">
    </div>
    <div class="w-52">
        <x-form.select
            name="zone"
            label="Zone"
            :options="[0 => 'None'] + $zones"
            selected="{{ request('zone') }}"
        />
    </div>
    <div class="flex gap-2">
        <button type="submit" class="btn btn-soft btn-success">
            Filter
        </button>

        @if (request()->hasAny(['name', 'character', 'npc', 'zone']))
            <a href="{{ url()->current() }}" class="btn btn-soft btn-error">
                Reset
            </a>
        @endif
    </div>
</form>
