@php
    $minlvl = 100;
    $spellClasses = [];
    for ($i = 1; $i <= 16; $i++) {
        $cls = $spell->{'classes' . $i};

        if ($cls > 0 && $cls < 255) {
            $spellClasses[] = config('everquest.classes.' . $i) . ' (' . $cls . ')';

            if ($cls < $minlvl) {
                $minlvl = $cls;
            }
        }
    }

    $clsOutput = implode(', ', $spellClasses);

    $targetType = config('everquest.spell_targets.' . $spell->targettype) ?? null;

    $duration = getBuffDuration($spell);
    $duration = $duration == 0 ? 'Instant' : seconds_to_human($duration * 6);
@endphp

<div class="w-full p-4 bg-base-200 rounded-lg border border-base-content/20">
    <div class="flex justify-between items-start">
        <h1 class="text-2xl font-bold">{{ $spell->name }}</h1>
        <div
            class="spell-icon spell-{{ $spell->new_icon }} rounded-lg {{ config('everquest.spell_target_colors.' . $spell->targettype, '') }}">
        </div>
    </div>

    @if ($effectsOnly === false)
        <div class="mt-2 space-y-1">
            <dl class="divide-y divide-gray-800">
                <div class="px-4 py-1 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                    <dt class="text-sm/6 font-medium">Classes</dt>
                    <dd class="mt-1 text-sm/6 sm:col-span-2 sm:mt-0">{{ $clsOutput }}</dd>
                </div>
                @if ($spell->mana > 0)
                    <div class="px-4 py-1 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                        <dt class="text-sm/6 font-medium">Mana</dt>
                        <dd class="mt-1 text-sm/6 sm:col-span-2 sm:mt-0">{{ $spell->mana }}</dd>
                    </div>
                @endif
                @if ($spell->EndurCost)
                    <div class="px-4 py-1 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                        <dt class="text-sm/6 font-medium">Endurance Cost</dt>
                        <dd class="mt-1 text-sm/6 sm:col-span-2 sm:mt-0">{{ $spell->EndurCost }}</dd>
                    </div>
                @endif
                @if ($spell->EndurUpkeep)
                    <div class="px-4 py-1 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                        <dt class="text-sm/6 font-medium">Endurance Upkeep</dt>
                        <dd class="mt-1 text-sm/6 sm:col-span-2 sm:mt-0">{{ $spell->EndurUpkeep }}</dd>
                    </div>
                @endif
                <div class="px-4 py-1 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                    <dt class="text-sm/6 font-medium">Skill</dt>
                    <dd class="mt-1 text-sm/6 sm:col-span-2 sm:mt-0">
                        {{ config('everquest.db_skills.' . $spell->skill) ?? null }}</dd>
                </div>
                <div class="px-4 py-1 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                    <dt class="text-sm/6 font-medium">Target Type</dt>
                    <dd class="mt-2 text-sm sm:col-span-2 sm:mt-0">{{ $targetType }}</dd>
                </div>
                <div class="px-4 py-1 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                    <dt class="text-sm/6 font-medium">Duration</dt>
                    <dd class="mt-1 text-sm/6 sm:col-span-2 sm:mt-0">{{ $duration }}</dd>
                </div>
                <div class="px-4 py-1 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                    <dt class="text-sm/6 font-medium">Cast Time</dt>
                    <dd class="mt-1 text-sm/6 sm:col-span-2 sm:mt-0">{{ $spell->cast_time / 1000 }}s</dd>
                </div>
                <div class="px-4 py-1 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                    <dt class="text-sm/6 font-medium">Recovery Time</dt>
                    <dd class="mt-1 text-sm/6 sm:col-span-2 sm:mt-0">{{ $spell->recovery_time / 1000 }}s</dd>
                </div>
                <div class="px-4 py-1 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                    <dt class="text-sm/6 font-medium">Recast Time</dt>
                    <dd class="mt-2 text-sm sm:col-span-2 sm:mt-0">{{ $spell->recast_time / 1000 }}s</dd>
                </div>
                <div class="px-4 py-1 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                    <dt class="text-sm/6 font-medium">Range</dt>
                    <dd class="mt-2 text-sm sm:col-span-2 sm:mt-0">{{ $spell->range }}</dd>
                </div>
            </dl>
        </div>
    @endif

    <div class="mt-4 px-4 py-1 sm:px-0">
        <h3 class="text-sm/6 font-medium">Effects</h3>
        <div class="mt-2 text-sm">
            @for ($n = 1; $n <= 12; $n++)
                <x-spell-effect
                    :spell="$spell"
                    :n="$n"
                    :all-spells="$allSpells"
                    :all-zones="$allZones"
                />
            @endfor
        </div>
    </div>
</div>
