<div x-data="merchantSelector({
    zone: @js(request()->get('zone')),
    version: @js(request()->get('v')),
    npc: @js(request()->get('npc')),
})" class="flex gap-4 mb-6">
    <select class="select w-72" @change="changeZone($event.target.value)">
        <option value="">Select Zone...</option>
        @foreach ($zones as $z)
            <option value="{{ $z->zoneidnumber }}" @selected(request()->get('zone') == $z->zoneidnumber)>
                {{ $z->short_name }}
            </option>
        @endforeach
    </select>

    <select class="select w-24" x-show="zone" @change="changeVersion($event.target.value)">
        @foreach ($versions as $v)
            <option value="{{ $v->version }}" @selected(request()->get('v') == $v->version)>
                v{{ $v->version }}
            </option>
        @endforeach
    </select>

    <select class="select w-72" x-show="zone" @change="changeNpc($event.target.value)">
        <option value="">Select NPC...</option>
        @foreach ($npcs as $n)
            <option value="{{ $n->id }}" @selected(request()->get('npc') == $n->id)>
                {{ $n->id }}: {{ $n->clean_name }}
            </option>
        @endforeach
    </select>
</div>
