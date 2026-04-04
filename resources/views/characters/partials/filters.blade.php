<form method="GET" action="{{ url()->current() }}" class="flex gap-2 items-end flex-wrap">
    <div class="w-44">
        <label class="label label-text">Name or ID</label>
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Name or ID" class="input w-full" />
    </div>

    <div class="w-44">
        <label class="label label-text">Account</label>
        <input type="text" name="account" value="{{ request('account') }}" placeholder="Account name or id" class="input w-full" />
    </div>

    <div class="w-44">
        <label class="label label-text">Guild</label>
        <input type="text" name="guild" value="{{ request('guild') }}" placeholder="Guild name" class="input w-full" />
    </div>

    <div class="w-20">
        <label class="label label-text">Show Deleted</label>
        <select name="deleted" class="select w-full">
            <option value="" {{ request('deleted') === null ? 'selected' : '' }}>No</option>
            <option value="1" {{ request('deleted') === '1' ? 'selected' : '' }}>Yes</option>
        </select>
    </div>

    <div class="flex gap-2 items-center">
        <button type="submit" class="btn btn-soft btn-success">Filter</button>
        @if (request()->hasAny(['q', 'account', 'guild', 'deleted']))
            <a href="{{ url()->current() }}" class="btn btn-soft btn-error">Reset</a>
        @endif
    </div>
</form>
