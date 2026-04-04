<div class="space-y-6" x-data="formTracker">
    <div class="grid grid-cols-1 gap-4 mb-2">
        <x-form.input
            name="name"
            label="Name"
            :value="$drop->name"
        />
    </div>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <x-form.select
            name="min_expansion"
            label="Min Expansion"
            :options="[-1 => 'Any'] + config('everquest.expansions')"
            :selected="$drop->min_expansion"
        />
        <x-form.select
            name="max_expansion"
            label="Max Expansion"
            :options="[-1 => 'Any'] + config('everquest.expansions')"
            :selected="$drop->max_expansion"
        />
        <x-form.content-flag-select
            name="content_flags"
            label="Content Flags"
            :selected="$drop->content_flags"
        />
        <x-form.content-flag-select
            name="content_flags_disabled"
            label="Content Flags Disabled"
            :selected="$drop->content_flags_disabled"
        />
    </div>

    <div class="gap-4 text-right">
        <button type="submit" class="btn btn-sm btn-soft btn-success">Save Loot Drop</button>
    </div>
</div>
