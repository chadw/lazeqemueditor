@php
    $race = config('everquest.db_races')[$npc->race] ?? 'Unknown';
    $bodytype = config('everquest.db_bodytypes')[$npc->bodytype] ?? 'Unknown';
@endphp
<div class="flex justify-between items-start">
    <div class="flex-1">
        <h1 class="text-2xl font-bold text-secondary">
            <span data-preview-key="name">{{ $npc->clean_name }}</span>
            <span class="block text-xs text-gray-600" data-preview-key="id">ID: {{ $npc->id }}</span>
        </h1>

        <div class="mt-2 text-sm text-gray-200">
            <table class="w-full max-w-md">
                <x-item-stat name="Level" :stat="$npc->level" />
                <x-item-stat name="Class" :stat="$npc->class" />
                <x-item-stat name="Race" :stat="$race" />
                <x-item-stat name="Body Type" :stat="$bodytype" />
            </table>
        </div>
    </div>

    {{-- shadow-[0_0_12px_var(--color-info)] --}}
    <div class="w-25 h-auto ml-4 race-model race-model-{{ $npc->race }}-{{ $npc->gender }}-{{ $npc->texture }}-{{ $npc->helmtexture }}"></div>
</div>
<div class="divider"></div>
<div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm text-gray-100">
    <div>
        <table class="w-full">
            <x-item-stat name="HP" :stat="number_format($npc->hp)" />
            <x-item-stat name="Mana" :stat="$npc->mana" />
            <x-item-stat name="AC" :stat="$npc->AC" />
            <x-item-stat name="Avoidance" :stat="$npc->Avoidance" />
        </table>
    </div>
    <div>
        <table class="w-full">
            <x-item-stat name="Min Dmg" :stat="$npc->mindmg" />
            <x-item-stat name="Max Dmg" :stat="$npc->maxdmg" />
            <x-item-stat name="Attack" :stat="$npc->ATK" />
            <x-item-stat name="Accuracy" :stat="$npc->Accuracy" />
            <x-item-stat name="Attack Count" :stat="$npc->attack_count" />
            <x-item-stat name="Attack Delay" :stat="$npc->attack_delay" />
        </table>
    </div>
</div>

<div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm text-gray-100">
    <div>
        <table class="w-full">
            <tr class="sm:hidden table-row">
                <td colspan="2" class="border-b border-base-content/5 text-base-content">Stats</td>
            </tr>
            <x-item-stat name="STR" :stat="$npc->STR" />
            <x-item-stat name="STA" :stat="$npc->STA" />
            <x-item-stat name="AGI" :stat="$npc->AGI" />
            <x-item-stat name="DEX" :stat="$npc->DEX" />
            <x-item-stat name="INT" :stat="$npc->_INT" />
            <x-item-stat name="WIS" :stat="$npc->WIS" />
            <x-item-stat name="CHA" :stat="$npc->CHA" />
        </table>
    </div>
    <div>
        <table class="w-full">
            <tr class="sm:hidden table-row">
                <td colspan="2" class="border-b border-base-content/5 text-base-content">Resists</td>
            </tr>
            <x-item-stat name="Magic" name-class="text-violet-500!" :stat="$npc->MR" />
            <x-item-stat name="Fire" name-class="text-red-500!" :stat="$npc->FR" />
            <x-item-stat name="Cold" name-class="text-sky-500!" :stat="$npc->CR" />
            <x-item-stat name="Disease" name-class="text-lime-600!" :stat="$npc->DR" />
            <x-item-stat name="Poison" name-class="text-green-600!" :stat="$npc->PR" />
            <x-item-stat name="Corrupt" name-class="text-rose-600!" :stat="$npc->Corrup" />
            <x-item-stat name="Physical" name-class="text-yellow-600!" :stat="$npc->PhR" />
        </table>
    </div>
    <div>
        <table class="w-full">
            <x-item-stat name="HP Regen" :stat="$npc->hp_regen_rate" />
            <x-item-stat name="HP Regen/sec" :stat="$npc->hp_regen_per_second" />
            <x-item-stat name="Mana Regen" :stat="$npc->mana_regen_rate" />
        </table>
    </div>
    <div>
        <table class="w-full">
            <x-item-stat name="HP Regen" :stat="$npc->hp_regen_rate" />
            <x-item-stat name="HP Regen/sec" :stat="$npc->hp_regen_per_second" />
            <x-item-stat name="Mana Regen" :stat="$npc->mana_regen_rate" />
        </table>
    </div>
</div>
