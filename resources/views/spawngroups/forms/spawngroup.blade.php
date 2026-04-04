<div class="space-y-6" x-data="formTracker">
    <div class="card bg-base-200 card-sm shadow-sm">
        <div class="card-body">
            <div class="grid grid-cols-8 gap-4">
                <x-form.input
                    name="name"
                    label="Name"
                    :value="$spawngroup->name"
                    required
                    wrapper-class="col-span-4"
                />
                <x-form.input
                    name="spawn_limit"
                    label="Spawn Limit"
                    type="number"
                    min="0"
                    :value="$spawngroup->spawn_limit"
                />
                <x-form.input
                    name="dist"
                    label="Distance"
                    type="number"
                    min="0"
                    step="any"
                    :value="$spawngroup->dist"
                />
                <x-form.input
                    name="delay"
                    label="Delay"
                    type="number"
                    min="0"
                    :value="$spawngroup->delay"
                />
                <x-form.input
                    name="mindelay"
                    label="Min Delay"
                    type="number"
                    min="0"
                    :value="$spawngroup->mindelay"
                />
                <x-form.input
                    name="min_x"
                    label="Min X"
                    type="number"
                    step="any"
                    :value="$spawngroup->min_x"
                />
                <x-form.input
                    name="max_x"
                    label="Max X"
                    type="number"
                    step="any"
                    :value="$spawngroup->max_x"
                />
                <x-form.input
                    name="min_y"
                    label="Min Y"
                    type="number"
                    step="any"
                    :value="$spawngroup->min_y"
                />
                <x-form.input
                    name="max_y"
                    label="Max Y"
                    type="number"
                    step="any"
                    :value="$spawngroup->max_y"
                />
                <x-form.select
                    name="despawn"
                    label="Despawn Type"
                    :options="[
                        0 => 'None',
                        1 => 'Repop',
                        2 => 'Repop on Timer',
                        3 => 'Depop',
                        4 => 'Depop on Timer',
                    ]"
                    :selected="$spawngroup->despawn"
                />
                <div x-data="durationHelper()">
                    <x-form.input
                        name="despawn_timer"
                        label="Despawn Timer"
                        type="number"
                        min="0"
                        :value="$spawngroup->despawn_timer"
                        x-bind:label-suffix="true"
                    />
                </div>
            </div>
        </div>
    </div>

    <div class="card bg-neutral shadow-sm">
        <div class="card-body">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <x-form.checkbox
                        name="wp_spawns"
                        label="Waypoint Spawns"
                        :checked="$spawngroup->wp_spawns"
                    />
                </div>
                <div>
                    <button type="submit" class="btn btn-soft btn-success">Save Spawn Group</button>
                </div>
            </div>
        </div>
    </div>
</div>
