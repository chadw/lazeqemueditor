<form method="GET" action="{{ route('merchants.index') }}" class="flex gap-2 items-end">
    <div class="w-72">
        <label class="label label-text">Merchant</label>
        <input name="merchant" value="{{ request()->get('merchant') }}" class="input w-full"
            placeholder="Merchant id or name" />
    </div>
    <div class="w-54">
        <label class="label label-text">Item</label>
        <input name="item" value="{{ request()->get('item') }}" class="input w-full"
            placeholder="Item id or name" />
    </div>
    <div class="flex gap-2">
        <button type="submit" class="btn btn-soft btn-success">
            Filter
        </button>
        @if (request()->hasAny(['merchant', 'item']))
            <a href="{{ url()->current() }}" class="btn btn-soft btn-error">
                Reset
            </a>
        @endif
    </div>
</form>

<div x-data="merchantSelector({
    zone: @js(request()->get('zone')),
    version: @js(request()->get('v')),
    npc: @js(request()->get('npc')),
})" class="flex gap-2 items-end">
    <div class="w-72">
        <label class="label label-text">Zone</label>
        <select class="select" @change="changeZone($event.target.value)">
            <option value="">Select Zone...</option>
            @foreach ($zones as $z)
                <option value="{{ $z->zoneidnumber }}" @selected(request()->get('zone') == $z->zoneidnumber)>
                    {{ $z->zoneidnumber }}: {{ $z->short_name }} - {{ $z->long_name }}
                </option>
            @endforeach
        </select>
    </div>
    @if (request()->get('zone'))
        <div class="w-24">
            <label class="label label-text">Version</label>
            <select class="select" x-show="zone" @change="changeVersion($event.target.value)">
                @foreach ($versions as $v)
                    <option value="{{ $v->version }}" @selected(request()->get('v') == $v->version)>
                        v{{ $v->version }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="w-72">
            <label class="label label-text">NPC</label>
            <select class="select" x-show="zone" @change="changeNpc($event.target.value)">
                <option value="">Select NPC...</option>
                @foreach ($npcs as $n)
                    <option value="{{ $n->id }}" @selected(request()->get('npc') == $n->id)>
                        {{ $n->id }}: {{ $n->clean_name }}
                    </option>
                @endforeach
            </select>
        </div>
    @endif
</div>
