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

<div class="flex justify-between items-start">
    <h1 class="text-2xl font-bold text-secondary">
        <span data-preview-key="name">{{ $spell->name }}</span>
        <span class="block text-xs text-gray-600" data-preview-key="id">ID: {{ $spell->id }}</span>
    </h1>
    <div
        class="spell-icon spell-{{ $spell->new_icon }} rounded-lg {{ config('everquest.spell_target_colors.' . $spell->targettype, '') }}"
        data-preview-key="new_icon">
    </div>
</div>

{{--
Duration 2m 42s
Cast Time 3s
Recovery Time 1.5s
Recast Time 6s
Range 100

Spell ID	12675
Classes
 WIZ (254)
When you cast	You unleash an enormous blast of magical energies.
When cast on you	You are struck by an enormous blast of magical energies.
When cast on other	is hit by an enormous blast of magical energies.
Skill	Melee
Type	Detrimental
Casting Time	5 sec
Recovery Time	0 sec
Recast Time	0 sec
Dispelable	No
Range	200'
Target	Single
Resist Type	Magic  (-1000)
Focusable	No
Recourse	 Volatile Mana Recourse
--}}
<div class="mt-2 space-y-1">
    <dl class="grid grid-cols-3 gap-1 divide-y divide-base-content/10 [&>:not(:last-child)]:pb-2">
        <dt class="font-medium">Classes</dt>
        <dd class="col-span-2">{{ $clsOutput }}</dd>
        @if ($spell->mana > 0)
            <dt class="font-medium">Mana</dt>
            <dd class="col-span-2" data-preview-key="mana">{{ $spell->mana }}</dd>
        @endif
        @if ($spell->EndurCost)
            <dt class="font-medium">Endurance Cost</dt>
            <dd class="col-span-2" data-preview-key="EndurCost">{{ $spell->EndurCost }}</dd>
        @endif
        @if ($spell->EndurUpkeep)
            <dt class="font-medium">Endurance Upkeep</dt>
            <dd class="col-span-2" data-preview-key="EndurUpkeep">{{ $spell->EndurUpkeep }}</dd>
        @endif
        @if ($spell->you_cast)
            <dt class="font-medium">You Cast</dt>
            <dd class="col-span-2" data-preview-key="you_cast">{{ $spell->you_cast }}</dd>
        @endif
        @if ($spell->other_casts)
            <dt class="font-medium">Others Cast</dt>
            <dd class="col-span-2" data-preview-key="other_casts">{{ $spell->other_casts }}</dd>
        @endif
        @if ($spell->cast_on_you)
            <dt class="font-medium">Cast on You</dt>
            <dd class="col-span-2" data-preview-key="cast_on_you">{{ $spell->cast_on_you }}</dd>
        @endif
        @if ($spell->cast_on_other)
            <dt class="font-medium">Cast on Other</dt>
            <dd class="col-span-2" data-preview-key="cast_on_other">{{ $spell->cast_on_other }}</dd>
        @endif
        <dt class="font-medium">Skill</dt>
        <dd class="col-span-2" data-preview-key="skill">
            {{ config('everquest.db_skills.' . $spell->skill) ?? null }}
        </dd>
        <dt class="font-medium">Target Type</dt>
        <dd class="col-span-2" data-preview-key="targettype">{{ $targetType }}</dd>
        <dt class="font-medium">Duration</dt>
        <dd class="col-span-2" data-preview-key="buffduration">{{ $duration }}</dd>
        <dt class="font-medium">Cast Time</dt>
        <dd class="col-span-2" data-preview-key="cast_time">{{ $spell->cast_time / 1000 }}s</dd>
        <dt class="font-medium">Recovery Time</dt>
        <dd class="col-span-2" data-preview-key="recovery_time">{{ $spell->recovery_time / 1000 }}s</dd>
        <dt class="font-medium">Recast Time</dt>
        <dd class="col-span-2" data-preview-key="recast_time">{{ $spell->recast_time / 1000 }}s</dd>
        <dt class="font-medium">Range</dt>
        <dd class="col-span-2" data-preview-key="range">{{ $spell->range }}</dd>
    </dl>

    <h2 class="col-span-3 text-lg text-accent font-bold mt-4">Effects</h2>
    <div class="col-span-3">
        @for ($n = 1; $n <= 12; $n++)
            <x-spell-effect
                :spell="$spell"
                :n="$n"
                :all-spells="$allSpells"
                :all-zones="$allZones"
            />
        @endfor
    </div>

    @if ($dbstr_desc)
    <h2 class="col-span-3 text-lg text-accent font-bold mt-4">Dbstr Description</h2>
    <div class="col-span-3">
        <div data-preview-key="dbstr_desc">{{ $dbstr_desc->value }}</div>
    </div>
    @endif
</div>
