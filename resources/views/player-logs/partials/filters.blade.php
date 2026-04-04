<form method="GET" action="{{ url()->current() }}" class="flex gap-2 items-end">
    <div class="flex flex-wrap items-end gap-3">
        <div class="w-44">
            <label class="label label-text">
                Account
            </label>
            <input
                type="text"
                name="account"
                value="{{ request('account') }}"
                placeholder="ID or name"
                class="input w-full"
            />
        </div>
        <div class="w-44">
            <label class="label label-text">
                Character
            </label>
            <input
                type="text"
                name="character"
                value="{{ request('character') }}"
                placeholder="ID or name"
                class="input w-full"
            />
        </div>
        <div class="w-44">
            <label class="label label-text">
                Zone
            </label>
            <input
                type="text"
                name="zone"
                value="{{ request('zone') }}"
                placeholder="ID or name"
                class="input w-full"
            />
        </div>
        <div class="w-52">
            <label class="label label-text">
                Event Type
            </label>
            <select
                name="event_type_id"
                class="select w-full"
            >
                <option value="">All Events</option>
                @foreach(config('everquest.pel_events') as $id => $label)
                    <option
                        value="{{ $id }}"
                        @selected(request('event_type_id') == $id)
                    >
                        {{ $label }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="flex gap-2">
            <button type="submit" class="btn btn-soft btn-success">
                Filter
            </button>

            @if(request()->hasAny([
                'account', 'character', 'zone', 'event_type_id'
            ]))
                <a href="{{ url()->current() }}" class="btn btn-soft btn-error">
                    Reset
                </a>
            @endif
        </div>
    </div>
</form>
