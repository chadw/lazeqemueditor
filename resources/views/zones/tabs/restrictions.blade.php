<div class="space-y-6" x-data="formTracker">
    <div class="card bg-base-200 card-sm shadow-sm">
        <div class="card-body">
            <h2 class="card-title">Level & Capacity</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mt-2">
                <x-form.input
                    name="min_level"
                    label="Min Level"
                    type="number"
                    :value="$zone->min_level"
                />
                <x-form.input
                    name="max_level"
                    label="Max Level"
                    type="number"
                    :value="$zone->max_level"
                />
                <x-form.select
                    name="min_status"
                    label="Min Status"
                    :options="config('everquest.account_status')"
                    keyInOption="true"
                    :selected="$zone->min_status"
                />
                <x-form.input
                    name="maxclients"
                    label="Max Clients"
                    type="number"
                    :value="$zone->maxclients"
                />
            </div>
        </div>
    </div>

    <div class="card bg-base-200 card-sm shadow-sm">
        <div class="card-body">
            <h2 class="card-title">Content Flags & Expansions</h2>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-2">
                <x-form.select
                    name="min_expansion"
                    label="Min Expansion"
                    :options="[-1 => 'Any'] + config('everquest.expansions')"
                    :selected="$zone->min_expansion"
                />
                <x-form.select
                    name="max_expansion"
                    label="Max Expansion"
                    :options="[-1 => 'Any'] + config('everquest.expansions')"
                    :selected="$zone->max_expansion"
                />
                <x-form.content-flag-select
                    name="content_flags"
                    label="Content Flags"
                    :selected="$zone->content_flags"
                />
                <x-form.content-flag-select
                    name="content_flags_disabled"
                    label="Content Flags Disabled"
                    :selected="$zone->content_flags_disabled"
                />
            </div>
        </div>
    </div>

    <div class="card bg-base-200 card-sm shadow-sm">
        <div class="card-body">
            <h2 class="card-title">Misc Options</h2>
            <div class="flex flex-wrap items-center gap-4 mt-2">
                <x-form.input
                    name="flag_needed"
                    label="Flag Needed"
                    :value="$zone->flag_needed"
                    wrapper-class="mb-2"
                />
                <x-form.checkbox
                    name="canlevitate"
                    label="Can Levitate"
                    :checked="$zone->canlevitate"
                />
                <x-form.checkbox
                    name="castoutdoor"
                    label="Cast Outdoor"
                    :checked="$zone->castoutdoor"
                />
                <x-form.checkbox
                    name="cancombat"
                    label="Can Combat"
                    :checked="$zone->cancombat"
                />
                <x-form.checkbox
                    name="peqzone"
                    label="PEQ Zone"
                    :checked="$zone->peqzone"
                />
            </div>
        </div>
    </div>
</div>
