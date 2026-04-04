<div x-data="factionSelector({
    faction: @js($selectedFactionId)
})" class="flex gap-2 items-end">
    <div class="w-96">
        <label class="label label-text">Faction</label>
        <select class="select w-full" @change="changeFaction($event.target.value)">
            <option value="">Select Faction...</option>
            @foreach ($factions as $k => $v)
                <option value="{{ $k }}" @selected((string) $selectedFactionId === (string) $k)>
                    {{ $k }}: {{ $v }}
                </option>
            @endforeach
        </select>
    </div>
</div>
