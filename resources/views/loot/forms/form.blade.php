<div class="card bg-base-200 card-sm shadow-sm mb-6">
    <div class="card-body">
        <div class="grid grid-cols-4 gap-4 mb-4">
            <x-form.input
                name="name"
                label="Name"
                :value="$table->name"
                required
                wrapper-class="col-span-2"
            />
            <div x-data="currencyHelper({{ $table->mincash ?? 0 }})">
                <x-form.input
                    name="mincash"
                    label="Min Cash"
                    type="number"
                    min="0"
                    :value="$table->mincash ?? 0"
                    x-model.number="amount"
                    x-bind:label-suffix="true"
                />
            </div>
            <div x-data="currencyHelper({{ $table->maxcash ?? 0 }})">
                <x-form.input
                    name="maxcash"
                    label="Max Cash"
                    type="number"
                    min="0"
                    :value="$table->maxcash ?? 0"
                    x-model.number="amount"
                    x-bind:label-suffix="true"
                />
            </div>
        </div>
        <div class="grid grid-cols-4 gap-4">
            <x-form.select
                name="min_expansion"
                label="Min Expansion"
                :options="[-1 => 'Any'] + config('everquest.expansions')"
                :selected="$table->min_expansion"
            />
            <x-form.select
                name="max_expansion"
                label="Max Expansion"
                :options="[-1 => 'Any'] + config('everquest.expansions')"
                :selected="$table->max_expansion"
            />
            <x-form.content-flag-select
                name="content_flags"
                label="Content Flags"
                :selected="$table->content_flags"
            />
            <x-form.content-flag-select
                name="content_flags_disabled"
                label="Content Flags Disabled"
                :selected="$table->content_flags_disabled"
            />
        </div>
    </div>
</div>

<div class="gap-4 text-right">
    <button type="submit" class="btn btn-sm btn-soft btn-success">Save Loot Table</button>
</div>
