<div class="w-full p-4 bg-base-200 rounded-lg border-1 border-base-content/20">
    @if ($pet->npc)
    <div class="flex justify-between items-start">
        <h1 class="text-2xl font-bold">{{ $pet->type }}</h1>
    </div>

    <div class="mt-2 space-y-1">
        <dl class="divide-y divide-gray-800">
            <div class="px-4 py-1 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                <dt class="text-sm/6 font-medium">Race</dt>
                <dd class="mt-1 text-sm/6 sm:col-span-2 sm:mt-0">{{ config('everquest.db_races.' . ($pet->npc?->race)) ?? 'Unknown' }}</dd>
            </div>
            <div class="px-4 py-1 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                <dt class="text-sm/6 font-medium">Class</dt>
                <dd class="mt-1 text-sm/6 sm:col-span-2 sm:mt-0">{{ config('everquest.classes.' . $pet->npc->class) }}</dd>
            </div>
            <div class="px-4 py-1 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                <dt class="text-sm/6 font-medium">Size</dt>
                <dd class="mt-1 text-sm/6 sm:col-span-2 sm:mt-0">{{ $pet->npc->size }}</dd>
            </div>
            <div class="px-4 py-1 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                <dt class="text-sm/6 font-medium">AC</dt>
                <dd class="mt-1 text-sm/6 sm:col-span-2 sm:mt-0">{{ number_format($pet->npc->AC) }}</dd>
            </div>
            <div class="px-4 py-1 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                <dt class="text-sm/6 font-medium">HP</dt>
                <dd class="mt-1 text-sm/6 sm:col-span-2 sm:mt-0">{{ number_format($pet->npc->hp) }}</dd>
            </div>
            <div class="px-4 py-1 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                <dt class="text-sm/6 font-medium">ATK</dt>
                <dd class="mt-1 text-sm/6 sm:col-span-2 sm:mt-0">{{ $pet->npc->ATK }}</dd>
            </div>
            <div class="px-4 py-1 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                <dt class="text-sm/6 font-medium">DMG</dt>
                <dd class="mt-2 text-sm sm:col-span-2 sm:mt-0">{{ $pet->npc->mindmg }}-{{ $pet->npc->maxdmg }}</dd>
            </div>
            @if ($pet->npc->special_abilities)
            <div class="px-4 py-1 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                <dt class="text-sm/6 font-medium">Special Abilities</dt>
                <dd class="mt-1 text-sm/6 sm:col-span-2 sm:mt-0">{{ implode(', ', $pet->npc->parsed_special_abilities) }}</dd>
            </div>
            @endif
            <div class="px-4 py-1 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                <dt class="text-sm/6 font-medium">Regen</dt>
                <dd class="mt-1 text-sm/6 sm:col-span-2 sm:mt-0">{{ $pet->npc->hp_regen_rate }}/tick</dd>
            </div>
        </dl>
        <div class="divider"></div>
        <div class="grid sm:grid-cols-2 gap-8">
            <div>
                <table class="w-full table-zebra">
                    <tr class="sm:hidden table-row">
                        <td colspan="2" class="border-b border-base-content/5 text-base-content">Stats</td>
                    </tr>
                    <x-item-stat name="STR" :stat="$pet->npc->STR" :stat2="null" />
                    <x-item-stat name="STA" :stat="$pet->npc->STA" :stat2="null" />
                    <x-item-stat name="INT" :stat="$pet->npc->_INT" :stat2="null" />
                    <x-item-stat name="WIS" :stat="$pet->npc->WIS" :stat2="null" />
                    <x-item-stat name="AGI" :stat="$pet->npc->AGI" :stat2="null" />
                    <x-item-stat name="DEX" :stat="$pet->npc->DEX" :stat2="null" />
                    <x-item-stat name="CHA" :stat="$pet->npc->CHA" :stat2="null" />
                </table>
            </div>
            <div>
                <table class="w-full table-zebra">
                    <tr class="sm:hidden table-row">
                        <td colspan="2" class="border-b border-base-content/5 text-base-content">Resists</td>
                    </tr>
                    <x-item-stat name="MR" :stat="$pet->npc->MR" :stat2="null" />
                    <x-item-stat name="FR" :stat="$pet->npc->FR" :stat2="null" />
                    <x-item-stat name="CR" :stat="$pet->npc->CR" :stat2="null" />
                    <x-item-stat name="DR" :stat="$pet->npc->DR" :stat2="null" />
                    <x-item-stat name="PR" :stat="$pet->npc->PR" :stat2="null" />
                </table>
            </div>
        </div>
    </div>
    @else
        <div role="alert" class="alert alert-warning alert-soft">
            No data available for this pet.
        </div>
    @endif
</div>
