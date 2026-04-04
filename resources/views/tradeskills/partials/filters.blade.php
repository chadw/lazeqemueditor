<form method="GET" action="{{ url()->current() }}" class="flex gap-2 items-end">
    <div class="w-64">
        <label class="label label-text">Recipe</label>
        <input type="text" name="recipe_name" value="{{ request('recipe_name') }}" placeholder="Recipe name"
            class="input w-full" />
    </div>
    <div class="w-44">
        <label class="label label-text">Item (id or name)</label>
        <input type="text" name="item" value="{{ request('item') }}" placeholder="Item id or name"
            class="input w-full" />
    </div>
    <div class="w-44">
        <label class="label label-text">Tradeskills</label>
        <select name="ts" class="select w-full">
            <option value="">All Tradeskills</option>
            @foreach ($tradeskills as $value => $label)
                <option value="{{ $value }}" @selected(request('ts') == $value)>
                    {{ $label }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="flex gap-2">
        <button type="submit" class="btn btn-soft btn-success">
            Filter
        </button>

        @if (request()->hasAny(['recipe_name', 'ts', 'item']))
            <a href="{{ url()->current() }}" class="btn btn-soft btn-error">
                Reset
            </a>
        @endif
    </div>
</form>
