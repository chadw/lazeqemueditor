<div class="tabs tabs-box">
    <input type="radio" name="npc_tabs" class="tab" aria-label="General" checked="checked" />
    <div class="tab-content bg-base-100 border-base-300 p-6">
        @include('npcs.tabs.general')
    </div>
    <input type="radio" name="npc_tabs" class="tab" aria-label="Stats" />
    <div class="tab-content bg-base-100 border-base-300 p-6">
        @include('npcs.tabs.stats')
    </div>
    <input type="radio" name="npc_tabs" class="tab" aria-label="Combat" />
    <div class="tab-content bg-base-100 border-base-300 p-6">
        @include('npcs.tabs.combat')
    </div>
    <input type="radio" name="npc_tabs" class="tab" aria-label="Appearance" />
    <div class="tab-content bg-base-100 border-base-300 p-6">
        @include('npcs.tabs.appearance')
    </div>
</div>

<div class="card bg-base-200 card-sm shadow-sm mt-4">
    <div class="card-body">
        <h2 class="card-title mb-2">Options</h2>
        <div class="flex flex-wrap items-center gap-4">
            <x-form.checkbox
                name="see_invis"
                label="See Invis"
                :checked="$npc->see_invis"
            />
            <x-form.checkbox
                name="see_invis_undead"
                label="See Invis Undead"
                :checked="$npc->see_invis_undead"
            />
            <x-form.checkbox
                name="see_hide"
                label="See Hide"
                :checked="$npc->see_hide"
            />
            <x-form.checkbox
                name="see_improved_hide"
                label="See Improved Hide"
                :checked="$npc->see_improved_hide"
            />
            <div class="divider divider-horizontal"></div>
            <x-form.checkbox
                name="npc_aggro"
                label="NPC Aggro"
                :checked="$npc->npc_aggro"
            />
            <x-form.checkbox
                name="always_aggro"
                label="Always Aggro"
                :checked="$npc->always_aggro"
            />
            <div class="divider divider-horizontal"></div>
            <x-form.checkbox
                name="show_name"
                label="Show Name"
                :checked="$npc->show_name"
            />
            <x-form.checkbox
                name="untargetable"
                label="Untargetable"
                :checked="$npc->untargetable"
            />
            <x-form.checkbox
                name="findable"
                label="Findable"
                :checked="$npc->findable"
            />
            <x-form.checkbox
                name="trackable"
                label="Trackable"
                :checked="$npc->trackable"
            />
            <x-form.checkbox
                name="unique_spawn_by_name"
                label="Unique Spawn By Name"
                :checked="$npc->unique_spawn_by_name"
            />
            <x-form.checkbox
                name="rare_spawn"
                label="Rare Spawn"
                :checked="$npc->rare_spawn"
            />
            <x-form.checkbox
                name="raid_target"
                label="Raid Target"
                :checked="$npc->raid_target"
            />
            <x-form.checkbox
                name="ignore_despawn"
                label="Ignore Despawn"
                tooltip="NPCs with this set will ignore the despawn value in spawngroup"
                :checked="$npc->ignore_despawn"
            />
            <div class="divider divider-horizontal"></div>
            <x-form.checkbox
                name="isquest"
                label="Is Quest NPC"
                :checked="$npc->isquest"
            />
            <x-form.checkbox
                name="private_corpse"
                label="Is Private Corpse"
                :checked="$npc->private_corpse"
            />
            <x-form.checkbox
                name="is_parcel_merchant"
                label="Is Parcel Merchant"
                :checked="$npc->is_parcel_merchant"
            />
            <x-form.checkbox
                name="underwater"
                label="Underwater NPC"
                :checked="$npc->underwater"
            />
            <div class="divider divider-horizontal"></div>
            <x-form.checkbox
                name="skip_global_loot"
                label="Skip Global Loot"
                :checked="$npc->skip_global_loot"
            />
            <x-form.checkbox
                name="keeps_sold_items"
                label="Keep Sold Items"
                :checked="$npc->keeps_sold_items"
            />
            <x-form.checkbox
                name="no_target_hotkey"
                label="No Target Hotkey"
                :checked="$npc->no_target_hotkey"
            />
            <x-form.checkbox
                name="isbot"
                label="Is Bot (?)"
                :checked="$npc->isbot"
            />
            <x-form.checkbox
                name="exclude"
                label="Exclude (?)"
                :checked="$npc->exclude"
            />
        </div>
    </div>
</div>
