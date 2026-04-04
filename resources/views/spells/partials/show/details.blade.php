<dl class="grid grid-cols-1 md:grid-cols-2 gap-x-12 gap-y-4 divide-y divide-base-content/5">
    <div class="grid grid-cols-3 gap-x-2">
        <dt class="text-sm font-medium col-span-1">Mana</dt>
        <dd class="text-sm col-span-2 text-right">{{ $spell->mana }}</dd>
    </div>
    @if ($spell->EndurCost)
        <div class="grid grid-cols-3 gap-x-2">
            <dt class="text-sm font-medium col-span-1">Endurance Cost</dt>
            <dd class="text-sm col-span-2 text-right">{{ $spell->EndurCost }}</dd>
        </div>
    @endif
    @if ($spell->EndurUpkeep)
        <div class="grid grid-cols-3 gap-x-2">
            <dt class="text-sm font-medium col-span-1">Endurance Upkeep</dt>
            <dd class="text-sm col-span-2 text-right">{{ $spell->EndurUpkeep }}</dd>
        </div>
    @endif
    <div class="grid grid-cols-3 gap-x-2">
        <dt class="text-sm font-medium col-span-1">Skill</dt>
        <dd class="text-sm col-span-2 text-right">
            {{ config('everquest.db_skills.' . $spell->skill) ?? null }}</dd>
    </div>
    <div class="grid grid-cols-3 gap-x-2">
        <dt class="text-sm font-medium col-span-1">Type</dt>
        <dd class="text-sm col-span-2 text-right">
            @if ($spell->goodEffect == 0)
                Detrimental
            @elseif ($spell->goodEffect == 1)
                Beneficial
            @elseif ($spell->goodEffect == 2)
                Beneficial, Group Only
            @endif
        </dd>
    </div>
    <div class="grid grid-cols-3 gap-x-2">
        <dt class="text-sm font-medium col-span-1">Target Type</dt>
        <dd class="text-sm col-span-2 text-right">{{ $targetType }}</dd>
    </div>
    <div class="grid grid-cols-3 gap-x-2">
        <dt class="text-sm font-medium col-span-1">Duration</dt>
        <dd class="text-sm col-span-2 text-right">{{ $duration }}</dd>
    </div>
    <div class="grid grid-cols-3 gap-x-2">
        <dt class="text-sm font-medium col-span-1">Cast Time</dt>
        <dd class="text-sm col-span-2 text-right">{{ $spell->cast_time / 1000 }}s</dd>
    </div>
    <div class="grid grid-cols-3 gap-x-2">
        <dt class="text-sm font-medium col-span-1">Recovery Time</dt>
        <dd class="text-sm col-span-2 text-right">{{ $spell->recovery_time / 1000 }}s</dd>
    </div>
    <div class="grid grid-cols-3 gap-x-2">
        <dt class="text-sm font-medium col-span-1">Recast Time</dt>
        <dd class="text-sm col-span-2 text-right">{{ $spell->recast_time / 1000 }}s</dd>
    </div>
    <div class="grid grid-cols-3 gap-x-2">
        <dt class="text-sm font-medium col-span-1">Resist</dt>
        <dd class="text-sm col-span-2 text-right">
            {{ config('everquest.db_elements.' . $spell->resisttype) }}
            @if ($spell->ResistDiff)
                (adjust: {{ $spell->ResistDiff }})
            @endif
        </dd>
    </div>
    <div class="grid grid-cols-3 gap-x-2">
        <dt class="text-sm font-medium col-span-1">Range</dt>
        <dd class="text-sm col-span-2 text-right">{{ $spell->range }}</dd>
    </div>
    @if ($spell->aoerange > 1)
        <div class="grid grid-cols-3 gap-x-2">
            <dt class="text-sm font-medium col-span-1">AE Range</dt>
            <dd class="text-sm col-span-2 text-right">{{ $spell->aoerange }}</dd>
        </div>
    @endif
    @if ($spell->aemaxtargets > 1)
        <div class="grid grid-cols-3 gap-x-2">
            <dt class="text-sm font-medium col-span-1">AE Max Tgts</dt>
            <dd class="text-sm col-span-2 text-right">{{ $spell->aemaxtargets }}</dd>
        </div>
    @endif
    @if ($spell->AEDuration >= 1000)
        <div class="grid grid-cols-3 gap-x-2">
            <dt class="text-sm font-medium col-span-1">AE Duration</dt>
            <dd class="text-sm col-span-2 text-right">{{ $spell->AEDuration / 1000 }}</dd>
        </div>
    @endif
</dl>

<div class="card bg-base-100 text-base-content shadow-sm my-6">
    <div class="card-body">
        <h2 class="card-title text-info/70">Effects</h2>
        @for ($n = 1; $n <= 12; $n++)
            <x-spell-effect :spell="$spell" :n="$n" :all-spells="$allSpells" :all-zones="$allZones" />
        @endfor
    </div>
</div>
<div class="card bg-base-100 text-base-content shadow-sm my-6">
    <div class="card-body">
        <h2 class="card-title text-info/70">Cast Messages</h2>
        @if ($spell->you_cast)
            <div class="col-span-3 flex justify-between">
                <span>When you cast</span>
                <span>{{ $spell->you_cast }}</span>
            </div>
        @endif
        @if ($spell->other_casts)
            <div class="col-span-3 flex justify-between">
                <span>When others cast</span>
                <span>{{ $spell->other_casts }}</span>
            </div>
        @endif
        @if ($spell->cast_on_you)
            <div class="col-span-3 flex justify-between">
                <span>When cast on you</span>
                <span>{{ $spell->cast_on_you }}</span>
            </div>
        @endif
        @if ($spell->cast_on_other)
            <div class="col-span-3 flex justify-between">
                <span>When cast on others</span>
                <span>{{ $spell->cast_on_other }}</span>
            </div>
        @endif
        @if ($spell->spell_fades)
            <div class="col-span-3 flex justify-between">
                <span>When fading</span>
                <span>{{ $spell->spell_fades }}</span>
            </div>
        @endif
    </div>
</div>
