<form method="GET" action="{{ route('items.index') }}" class="flex gap-2 items-end"
    x-data="{ type: '{{ request('type') }}' }">
    <div class="w-64">
        <label class="label label-text">Item</label>
        <input
            type="text"
            name="item"
            minlength="2"
            value="{{ old('item', $item->id ?? request('item')) }}"
            pattern="^[A-Za-z0-9\s\-\_\(\)\[\]\.\,\:\/\?\=\+\&\#\*'’&quot;%]+$"
            title="Allowed: letters, numbers, space and - _ ' ’ () [] . , : / ? = + & # * \" %"
            placeholder="Item id or name"
            class="input w-full"
        />
    </div>
    <div class="w-60">
        <label class="label label-text">Item Type</label>
        <select name="type" x-model="type" class="select w-full"
            @change="if(!['555','556','557'].includes(type)) $refs.bagslots.value=''">
            <option value="">-</option>
            @foreach (config('everquest.item_types_select') as $group => $types)
                <optgroup label="{{ $group }}">
                    @foreach ($types as $id => $name)
                        <option value="{{ $id }}"
                            {{ request('type') != '' && request('type') == $id ? 'selected' : '' }}>
                            {{ $name }}
                        </option>
                    @endforeach
                </optgroup>
            @endforeach
        </select>
    </div>
    <div class="w-28" x-show="['555','556','557'].includes(type)" x-transition x-cloak>
        <label class="label label-text">Min Bag Slots</label>
        <input
            x-ref="bagslots"
            type="number"
            name="bagslots"
            value="{{ request('bagslots') }}"
            min="1"
            max="200"
            class="input w-full"
            placeholder="Min bag slots"
        >
    </div>
    <div class="flex gap-2">
        <button type="submit" class="btn btn-soft btn-success">
            Filter
        </button>
        @if(request()->hasAny(['item', 'type', 'bagslots']))
            <a href="{{ url()->current() }}" class="btn btn-soft btn-error">
                Reset
            </a>
        @endif
    </div>
</form>
