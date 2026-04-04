<div class="space-y-6">
    <div class="card bg-base-200 card-sm shadow-sm">
        <div class="card-body">
            <div class="grid grid-cols-8 gap-4 mb-2">
                <div class="form-control w-full">
                    <label class="label">
                        <span class="label-text">ID <span class="text-error">*</span></span>
                    </label>
                    <div x-data class="flex gap-2">
                        <input
                            id="item_id_field" name="id" type="number"
                            value="{{ old('id', $item->id) }}"
                            required
                            class="input w-full"
                        />
                        <button
                            type="button"
                            class="btn btn-soft btn-secondary"
                            @click='$store.idPicker.open({
                                selector: "#item_id_field",
                                type: "items"
                            })'>
                            <x-ui.icon name="search" />
                        </button>
                    </div>
                </div>
                <x-form.input
                    name="Name"
                    label="Name"
                    :value="$item->Name"
                    required
                    wrapper-class="col-span-3"
                />
                <x-form.input
                    name="lore"
                    label="Lore"
                    :value="$item->lore"
                    tooltip="This is the lore description that will show en an item inspect window when it is
                        identified."
                    wrapper-class="col-span-3"
                />
                <x-form.input
                    name="loregroup"
                    label="Lore Group"
                    tooltip="Characters can only have 1 item from any lore group that is set to something other than 0.
                        Epic Lore is defined as Lore Group 1, but you can create any number of other Lore Groups."
                    :value="$item->loregroup"
                    type="number"
                    min="-1"
                />
            </div>
            <div class="grid grid-cols-12 gap-4">
                <x-form.select
                    name="itemtype"
                    label="Type"
                    tooltip=""
                    :options="config('everquest.item_types')"
                    :selected="$item->itemtype"
                    wrapper-class="col-span-2"
                />
                <x-form.select
                    name="itemclass"
                    label="Item Class"
                    tooltip=""
                    :options="[
                        0 => 'Common Item',
                        1 => 'Container',
                        2 => 'Book',
                    ]"
                    :selected="$item->itemclass"
                    wrapper-class="col-span-2"
                />
                <x-form.input
                    name="reqlevel"
                    label="Req Level"
                    tooltip="This is the Required Level to use an item. No stats will be gained from this item if
                        below this level."
                    :value="$item->reqlevel"
                    type="number"
                    min="0"
                    wrapper-class="col-span-1"
                />
                <x-form.input
                    name="reclevel"
                    label="Rec Level"
                    tooltip="This is the Recommended Level to use an item. The item's stats will be scaled down if
                        below this level."
                    :value="$item->reclevel"
                    type="number"
                    min="0"
                    wrapper-class="col-span-1"
                />
                <x-form.input
                    name="recskill"
                    label="Rec Skill"
                    tooltip=""
                    :value="$item->recskill"
                    type="number"
                    min="0"
                    wrapper-class="col-span-1"
                />
                <x-form.input
                    name="stacksize"
                    label="Stack Size"
                    tooltip="This is the maximum stack size used for stackable items."
                    :value="$item->stacksize"
                    type="number"
                    min="0"
                    max="1000"
                    wrapper-class="col-span-1"
                />
                <x-form.input
                    name="weight"
                    label="Weight"
                    tooltip="Weight of the item (multiplied by 10) IE, Cloth Cap weighs 0.2, is stored as 2."
                    :value="$item->weight"
                    type="number"
                    step="any"
                    wrapper-class="col-span-1"
                />
                <x-form.input
                    name="charmfile"
                    label="Charmfile"
                    tooltip="This field corresponds to the quests/items/ folder. The name set in this field will be
                        the script loaded for this item such as 'CharmTest' would use the file 'CharmTest.pl'
                        or 'CharmTest.lua'"
                    :value="$item->charmfile"
                    wrapper-class="col-span-2"
                />
                <x-form.input
                    name="charmfileid"
                    label="Charmfile ID"
                    tooltip="Needs to be non-zero if item is to be scaled as a charm"
                    :value="$item->charmfileid"
                    type="number"
                    min="0"
                    wrapper-class="col-span-1"
                />
                <x-form.input
                    name="scriptfileid"
                    label="Script File ID"
                    tooltip="This is the ID (numeric value) of a script that is called from the 'quests/items/'
                        folder when this item is right clicked. The number set in this field will be the script loaded
                        for this item such as '123456' would use the file 'script_123456.pl' or 'script_123456.lua'"
                    :value="$item->scriptfileid"
                    wrapper-class="col-span-2"
                />
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-4">
        @include('items.forms.general-appearance')
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-1 md:grid-cols-1 lg:grid-cols-2 xl:grid-cols-2 gap-4">
        @include('items.forms.general-checkboxes1')
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-1 md:grid-cols-1 lg:grid-cols-2 xl:grid-cols-2 gap-4">
        @include('items.forms.general-bags-plus')
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-1 md:grid-cols-1 lg:grid-cols-2 xl:grid-cols-2 gap-4">
        @include('items.forms.general-food')
    </div>

    <div class="card bg-base-200 card-sm shadow-sm">
        <div class="card-body">
            <h2 class="card-title">Options</h2>
            @include('items.forms.general-options')
        </div>
    </div>
</div>
