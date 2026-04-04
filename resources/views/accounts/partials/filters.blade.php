<form method="GET" action="{{ url()->current() }}" class="flex gap-2 items-end">
    <div class="w-44">
        <label class="label label-text">Name</label>
        <input
            type="text"
            name="name"
            value="{{ request('name') }}"
            placeholder="Account id or name"
            class="input w-full"
        />
    </div>
    <div class="w-44">
        <label class="label label-text">Status</label>
        <select name="status" class="select w-full">
            <option value="">All statuses</option>
            @foreach(config('everquest.account_status', []) as $statusId => $statusName)
                <option value="{{ $statusId }}" @selected((string) request('status') === (string) $statusId)>
                    {{ $statusName }} ({{ $statusId }})
                </option>
            @endforeach
        </select>
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
            'name','status','from','to'
        ]))
            <a href="{{ url()->current() }}" class="btn btn-soft btn-error">
                Reset
            </a>
        @endif
    </div>
</form>
