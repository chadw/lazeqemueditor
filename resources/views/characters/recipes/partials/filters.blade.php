<form method="GET" action="{{ url()->current() }}" class="flex gap-2 items-end flex-wrap">
    <div class="w-44">
        <label class="label label-text">Character ID or Name</label>
        <input type="text" name="character" value="{{ request('character') }}" placeholder="Character ID or Name"
            class="input w-full" />
    </div>

    <div class="w-44">
        <label class="label label-text">Recipe ID or Name</label>
        <input type="text" name="recipe" value="{{ request('recipe') }}" placeholder="Recipe ID or Name"
            class="input w-full" />
    </div>

    <div class="flex gap-2 items-center">
        <button type="submit" class="btn btn-soft btn-success">Filter</button>
        @if (request()->hasAny(['character', 'recipe']))
            <a href="{{ url()->current() }}" class="btn btn-soft btn-error">Reset</a>
        @endif
    </div>
</form>
