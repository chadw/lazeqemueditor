<div
    x-data
    x-init="$nextTick(() => { $store.specialAbilities.init($el) })"
    data-initial="{{ old('special_abilities', $npc->special_abilities ?? '') }}"
    class="space-y-6"
>
    <div class="card bg-base-200 card-sm shadow-sm">
        <div class="card-body">
            <h2 class="card-title">Special Abilities</h2>
            <div class="grid grid-cols-8 gap-4">
                <div class="card bg-base-100 card-sm p-4 col-span-2">
                    <div class="space-y-6">
                        <div class="grid grid-cols-2 md:grid-cols-2 lg:grid-cols-1 gap-4 items-center">
                            @include('npcs.partials.special.summon')
                            @include('npcs.partials.special.enrage')
                            @include('npcs.partials.special.rampage')
                            @include('npcs.partials.special.aerampage')
                            @include('npcs.partials.special.flurry')
                            @include('npcs.partials.special.rangedatk')
                            @include('npcs.partials.special.tunnelv')
                            @include('npcs.partials.special.leashed')
                            @include('npcs.partials.special.tethered')
                            @include('npcs.partials.special.fleepct')
                            @include('npcs.partials.special.chasedist')
                            @include('npcs.partials.special.allowtank')
                            @include('npcs.partials.special.castingresdiff')
                            @include('npcs.partials.special.counteravoid')
                            @include('npcs.partials.special.modifyavoid')
                        </div>
                    </div>
                </div>
                <div class="card bg-base-100 card-sm p-4 col-span-6">
                    @include('npcs.partials.special._checkboxes')
                </div>
                <div class="card bg-base-100 card-sm p-4 col-span-8">
                    <label class="label">Computed Special Abilities</label>
                    <div class="tooltip" data-tip="Readonly field showing the computed special abilities based on the above inputs">
                        <textarea class="textarea w-full resize-none" rows="2" readonly
                            x-bind:value="$store.specialAbilities.output()"></textarea>
                    </div>
                    <input type="hidden" name="special_abilities" x-bind:value="$store.specialAbilities.output()" />
                </div>
            </div>
        </div>
    </div>
</div>
