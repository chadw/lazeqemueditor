@extends('layouts.app')
@section('title', "Faction: $faction->name - NPCs (" . ucfirst($type) . ')')
@section('page-title', "Faction: $faction->name - NPCs (" . ucfirst($type) . ')')

@section('content')
    <div>
        <x-top-links>
            <x-slot name="left">
                <a href="{{ route('factions.edit', ['faction' => $faction->id]) }}" class="btn btn-accent btn-soft">
                    Back to [{{ $faction->name }}] Faction
                </a>
            </x-slot>
            <div class="dropdown dropdown-bottom dropdown-hover">
                <div tabindex="0" class="btn btn-soft btn-info">NPC</div>
                <ul class="dropdown-content menu bg-base-100 rounded-box z-10 w-60 p-2 shadow-sm right-0">
                    <li>
                        <a href="{{ route('factions.npcs.primary', $faction) }}">NPCs on this faction</a>
                    </li>
                    <li>
                        <a href="{{ route('factions.npcs.effect', [$faction, 'increase']) }}">
                            NPCs that increase this faction
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('factions.npcs.effect', [$faction, 'decrease']) }}">
                            NPCs that decrease this faction
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('factions.npcs.effect', [$faction, 'nochange']) }}">
                            NPCs with no faction change
                        </a>
                    </li>
                </ul>
            </div>
        </x-top-links>

        <div class="card bg-base-100 card-sm shadow-sm mb-4">
            @if ($npcs->isNotEmpty())
                <div class="border border-base-content/5 overflow-x-auto">
                    <table class="table table-auto table-zebra md:table-fixed w-full">
                        <thead class="text-xs uppercase bg-neutral">
                            <tr>
                                <th scope="col" class="w-[5%]">ID</th>
                                <th scope="col">NPC</th>
                                <th scope="col">Zone</th>
                                @if (in_array($type, ['primary']))
                                    <th scope="col">Faction</th>
                                @endif
                                @if (in_array($type, ['increase', 'decrease', 'nochange']))
                                    <th>Faction Value</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($npcs as $npc)
                                @php
                                    $zone = $npc->firstSpawnEntries?->spawn2?->zoneData;
                                @endphp
                                <tr>
                                    <td>{{ $npc->id }}</td>
                                    <td>{{ $npc->clean_name }}</td>
                                    <td>
                                        <div class="flex flex-col">
                                            @if (is_object($zone))
                                                <a href="{{ route('zones.edit', ['zone' => $zone->zoneidnumber]) }}{{ $zone->version > 0 ? '?v=' . $zone->version : '' }}"
                                                    class="text-base link-info link-hover">
                                                    {{ $zone->long_name }}
                                                </a>
                                                <span class="text-xs uppercase text-gray-500">
                                                    @if ($zone->expansion !== null)
                                                        {{ config('everquest.expansions')[$zone->expansion] ?? 'Unknown Expansion' }}
                                                    @endif
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    @if (in_array($type, ['primary']))
                                        <td>
                                            {{ $npc->primaryFaction->name }}
                                        </td>
                                    @endif
                                    @if (in_array($type, ['increase', 'decrease', 'nochange']))
                                        <td>
                                            @if ($type === 'increase')
                                                <span class="text-success">+
                                                @elseif ($type === 'decrease')
                                                    <span class="text-error">
                                                    @else
                                                        <span>
                                            @endif
                                            {{ $npc->factionEntries->first()?->npc_value }}
                                            </span>
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div role="alert" class="alert alert-info alert-soft">
                    <span>No NPCs found.</span>
                </div>
            @endif
        </div>

        <div class="mt-4">
            {{ $npcs->links() }}
        </div>
    </div>
@endsection
