<div class="tabs tabs-lift">
    <input type="radio" name="spell_tabs" class="tab" aria-label="General" checked="checked" />
    <div class="tab-content bg-base-100 border-base-300 p-6">
        @include('spells.tabs.general')
    </div>
    <input type="radio" name="spell_tabs" class="tab" aria-label="Effects" />
    <div class="tab-content bg-base-100 border-base-300 p-6">
        @include('spells.tabs.effects')
    </div>
    <input type="radio" name="spell_tabs" class="tab" aria-label="Restrictions" />
    <div class="tab-content bg-base-100 border-base-300 p-6">
        @include('spells.tabs.restrictions')
    </div>
    <input type="radio" name="spell_tabs" class="tab" aria-label="Range" />
    <div class="tab-content bg-base-100 border-base-300 p-6">
        @include('spells.tabs.range')
    </div>
    <input type="radio" name="spell_tabs" class="tab" aria-label="Casting" />
    <div class="tab-content bg-base-100 border-base-300 p-6">
        @include('spells.tabs.casting')
    </div>
    <input type="radio" name="spell_tabs" class="tab" aria-label="Buffs" />
    <div class="tab-content bg-base-100 border-base-300 p-6">
        @include('spells.tabs.buffs')
    </div>
    <input type="radio" name="spell_tabs" class="tab" aria-label="Resist" />
    <div class="tab-content bg-base-100 border-base-300 p-6">
        @include('spells.tabs.resist')
    </div>
    <input type="radio" name="spell_tabs" class="tab" aria-label="Misc" />
    <div class="tab-content bg-base-100 border-base-300 p-6">
        @include('spells.tabs.misc')
    </div>
</div>
