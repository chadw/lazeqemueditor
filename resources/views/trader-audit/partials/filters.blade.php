<form method="GET" action="{{ url()->current() }}" class="flex gap-2 items-end">
    <div class="w-44">
        <label class="label label-text">Seller</label>
        <input
            type="text"
            name="seller"
            value="{{ request('seller') }}"
            placeholder="Seller id or name"
            class="input w-full"
        />
    </div>
    <div class="w-44">
        <label class="label label-text">Buyer</label>
        <input
            type="text"
            name="buyer"
            value="{{ request('buyer') }}"
            placeholder="Buyer id or name"
            class="input w-full"
        />
    </div>
    <div class="w-52">
        <label class="label label-text">Item</label>
        <input
            type="text"
            name="item"
            value="{{ request('item') }}"
            placeholder="Item id or name"
            class="input w-full"
        />
    </div>
    <div class="w-44">
        <label class="label label-text">From</label>
        <input
            type="datetime-local"
            name="from"
            value="{{ request('from') }}"
            class="input w-full"
        />
    </div>
    <div class="w-44">
        <label class="label label-text">To</label>
        <input
            type="datetime-local"
            name="to"
            value="{{ request('to') }}"
            class="input w-full"
        />
    </div>
    <div class="flex gap-2">
        <button type="submit" class="btn btn-soft btn-success">
            Filter
        </button>

        @if(request()->hasAny([
            'seller','buyer','item','from','to'
        ]))
            <a href="{{ url()->current() }}" class="btn btn-soft btn-error">
                Reset
            </a>
        @endif
    </div>
</form>
