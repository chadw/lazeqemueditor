<div class="tabs tabs-box" x-data="formTracker">
    <input type="radio" name="zone_tabs" class="tab" aria-label="General" checked="checked" />
    <div class="tab-content bg-base-100 border-base-300 p-6">
        @include('zones.tabs.general')
    </div>
    <input type="radio" name="zone_tabs" class="tab" aria-label="Restrictions" />
    <div class="tab-content bg-base-100 border-base-300 p-6">
        @include('zones.tabs.restrictions')
    </div>
    <input type="radio" name="zone_tabs" class="tab" aria-label="Sky/Weather" />
    <div class="tab-content bg-base-100 border-base-300 p-6">
        @include('zones.tabs.sky-weather')
    </div>
</div>
