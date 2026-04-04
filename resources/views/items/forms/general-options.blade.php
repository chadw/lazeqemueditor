<div class="flex flex-wrap items-center gap-4">
    <x-form.checkbox
        name="magic"
        label="Magic"
        tooltip="Sets item to be magical, you like your items magical, don't you?"
        :checked="$item->magic"
    />
    <x-form.checkbox
        name="nodrop"
        label="No Drop"
        tooltip="Sets item to not be droppable."
        :checked="$item->nodrop"
        inverted
    />
    <x-form.checkbox
        name="fvnodrop"
        label="FV No Drop"
        tooltip="Sets item to NO DROP under the FV ruleset"
        :checked="$item->fvnodrop"
    />
    <x-form.checkbox
        name="questitemflag"
        label="Quest"
        tooltip="Item flag that simply signifies its use in quests."
        :checked="$item->questitemflag"
    />
    <x-form.checkbox
        name="norent"
        label="No Rent"
        tooltip="Sets item to no rent."
        :checked="$item->norent"
        inverted
    />
    <x-form.checkbox
        name="tradeskills"
        label="Tradeskill Item"
        tooltip=""
        :checked="$item->tradeskills"
    />
    <x-form.checkbox
        name="stackable"
        label="Stackable"
        tooltip="If enabled, this item will be stackable. Stack size must be set to the desired maximum stack size."
        :checked="$item->stackable"
    />
    <x-form.checkbox
        name="book"
        label="Book"
        tooltip=""
        :checked="$item->book"
    />
    <x-form.checkbox
        name="notransfer"
        label="No Transfer"
        tooltip=""
        :checked="$item->notransfer"
    />
    <x-form.checkbox
        name="summonedflag"
        label="Summoned"
        tooltip=""
        :checked="$item->summonedflag"
    />
    <x-form.checkbox
        name="artifactflag"
        label="Artifact"
        tooltip=""
        :checked="$item->artifactflag"
    />
    <x-form.checkbox
        name="nopet"
        label="No Pet"
        tooltip="If enabled, this item will not be able to be equipped by pets."
        :checked="$item->nopet"
    />
    <x-form.checkbox
        name="attuneable"
        label="Attuneable"
        tooltip="Once enabled, item becomes NO DROP when equipped for the first time."
        :checked="$item->attuneable"
    />
    <x-form.checkbox
        name="potionbelt"
        label="Potion Belt"
        tooltip="If set, this item can be used from the potion belt."
        :checked="$item->potionbelt"
    />
    <x-form.checkbox
        name="placeable"
        label="Placeable"
        tooltip=""
        :checked="$item->placeable"
    />
    <x-form.checkbox
        name="epicitem"
        label="Epic Item"
        tooltip=""
        :checked="$item->epicitem"
    />
    <x-form.checkbox
        name="expendablearrow"
        label="Arrow Expend"
        tooltip=""
        :checked="$item->expendablearrow"
    />
    <x-form.checkbox
        name="heirloom"
        label="Heirloom"
        tooltip=""
        :checked="$item->heirloom"
    />
</div>
