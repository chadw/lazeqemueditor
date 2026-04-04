<form method="GET" class="flex gap-2 items-end">
    <div>
        <input type="text" name="char" value="{{ request('char') }}" class="w-60 input"
            placeholder="Character ID or Name">
    </div>
    <div>
        <input type="text" name="item" value="{{ request('item') }}" class="w-60 input"
            placeholder="Item ID or Name">
    </div>
    <div>
        <button type="submit" class="btn btn-soft btn-primary">Search</button>
        <a href="{{ route('parcels.index') }}" class="btn btn-soft btn-error">Clear</a>
    </div>
</form>
