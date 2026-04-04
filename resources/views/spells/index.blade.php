@extends('layouts.app')
@section('title', 'Spells')
@section('page-title', 'Spells')

@section('content')
    @php
        $classes = [
            'cleric' => 'Cleric',
            'druid' => 'Druid',
            'shaman' => 'Shaman',
            'wizard' => 'Wizard',
            'magician' => 'Magician',
            'necromancer' => 'Necromancer',
            'enchanter' => 'Enchanter',
            'paladin' => 'Paladin',
            'ranger' => 'Ranger',
            'shadowknight' => 'Shadowknight',
            'bard' => 'Bard',
            'monk' => 'Monk',
            'rogue' => 'Rogue',
            'berserker' => 'Berserker',
        ];
        $classes = collect($classes ?? [])->sort()->toArray();
    @endphp

    <x-top-links>
        <x-slot name="left">
            @include('spells.partials.filters')
        </x-slot>
    </x-top-links>

    <x-search-results :items="$spells" title="Spells">
        <x-ui.table>
            <x-slot:head>
                <tr>
                    <th scope="col" class="w-[5%]">ID</th>
                    <th scope="col">Spell</th>
                    <th scope="col" class="w-[5%]">Level</th>
                    <th scope="col">Effects</th>
                    <th scope="col" class="w-[5%]">Mana</th>
                    <th scope="col" class="w-[5%]">Cast</th>
                    <th scope="col" class="w-[5%]">Recast</th>
                    <th scope="col" class="w-[10%]">Duration</th>
                    <th scope="col" class="w-[10%]">Target</th>
                    <th scope="col" class="w-[15%] text-right">-</th>
                </tr>
            </x-slot:head>
            <x-slot:body>
                @forelse ($spells as $spell)
                    <tr>
                        <td>{{ $spell->id }}</td>
                        <td class="font-semibold">
                            <x-spell-link
                                :spell_id="$spell->id"
                                :spell_name="$spell->name"
                                :spell_icon="$spell->new_icon"
                                :spell_target_type="$spell->targettype"
                                spell_class="flex text-base"
                            />
                        </td>

                        <td class="text-xs">-
                            @foreach ($classes as $key => $label)
                                @if ($spell->$key > 0)
                                    <span class="badge badge-outline mr-1">
                                        {{ $label }} {{ $spell->$key }}
                                    </span>
                                @endif
                            @endforeach
                        </td>
                        <td class="truncate">
                            @for ($n = 1; $n <= 12; $n++)
                                <x-spell-effect
                                    :spell="$spell"
                                    :n="$n"
                                    :all-spells="$allSpells"
                                    :all-zones="$allZones"
                                />
                            @endfor
                        </td>
                        <td>{{ $spell->mana }}</td>
                        <td>{{ $spell->cast_time / 1000 }}s</td>
                        <td>{{ $spell->recast_time / 1000 }}s</td>
                        <td>
                            @php
                                $duration = getBuffDuration($spell);
                                $duration = $duration == 0 ? 'Instant' : seconds_to_human($duration * 6);
                            @endphp
                            {{ $duration }}
                        </td>
                        <td>{{ config('everquest.spell_targets.' . $spell->targettype) ?? '-' }}</td>
                        <td class="text-right whitespace-nowrap">
                            <div class="inline join">
                                <form method="POST" action="{{ route('spells.clone', $spell) }}"
                                    class="inline-block">
                                    @csrf
                                    <input type="hidden" name="redirect" value="index" />
                                    <button type="submit" class="join-item btn btn-sm btn-soft btn-info tooltip"
                                        data-tip="Clone">
                                        <x-ui.icon name="clone" />
                                    </button>
                                </form>
                                <a href="{{ route('spells.edit', $spell) }}"
                                    class="join-item btn btn-sm btn-soft tooltip"
                                    data-tip="Edit">
                                    <x-ui.icon name="edit" />
                                </a>
                                <form action="{{ route('spells.destroy', $spell) }}" method="POST"
                                    class="inline">
                                    @csrf @method('DELETE')
                                    <button class="join-item btn btn-sm btn-soft btn-error tooltip"
                                        data-tip="Delete"
                                        onclick="return confirm('Delete?')">
                                        <x-ui.icon name="delete" />
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="text-center py-6 text-base-content/50">
                            No spells found.
                        </td>
                    </tr>
                @endforelse
            </x-slot:body>
        </x-ui.table>
    </x-search-results>

    <div class="p-4">{{ $spells->links() }}</div>

@endsection
