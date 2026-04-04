<div class="tabs tabs-lift">
    <input type="radio" name="item_tabs" class="tab" aria-label="General" checked="checked" />
    <div class="tab-content bg-base-100 border-base-300 p-6">
        @include('items.tabs.general')
    </div>
    <input type="radio" name="item_tabs" class="tab" aria-label="Stats" />
    <div class="tab-content bg-base-100 border-base-300 p-6">
        @include('items.tabs.stats')
    </div>
    <input type="radio" name="item_tabs" class="tab" aria-label="Effects" />
    <div class="tab-content bg-base-100 border-base-300 p-6">
        @include('items.tabs.effects')
    </div>
    <input type="radio" name="item_tabs" class="tab" aria-label="Augs" />
    <div class="tab-content bg-base-100 border-base-300 p-6">
        @include('items.tabs.augs')
    </div>
    <input type="radio" name="item_tabs" class="tab" aria-label="Pricing" />
    <div class="tab-content bg-base-100 border-base-300 p-6">
        @include('items.tabs.pricing')
    </div>
    <input type="radio" name="item_tabs" class="tab" aria-label="Faction" />
    <div class="tab-content bg-base-100 border-base-300 p-6">
        @include('items.tabs.faction')
    </div>
    <input type="radio" name="item_tabs" class="tab" aria-label="Evolving" />
    <div class="tab-content bg-base-100 border-base-300 p-6">
        @include('items.tabs.evolving')
    </div>
    <input type="radio" name="item_tabs" class="tab" aria-label="Misc" />
    <div class="tab-content bg-base-100 border-base-300 p-6">
        @include('items.tabs.misc')
    </div>
</div>
