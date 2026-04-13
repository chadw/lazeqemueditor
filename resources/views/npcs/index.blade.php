@extends('layouts.app')
@section('title', 'NPCs')
@section('page-title', 'NPCs')

@section('content')
    <x-top-links>
        <x-slot name="left">
            @include('npcs.partials.filters')
        </x-slot>
    </x-top-links>

    <x-search-results :items="$npcs" title="NPCs">
        <x-ui.table>
            <x-slot:head>
                <tr>
                    <th scope="col" class="w-[5%]">ID</th>
                    <th scope="col">Name</th>
                    <th scope="col" class="w-[20%]">Zone</th>
                    <th scope="col" class="w-[10%]">HP</th>
                    <th scope="col" class="w-[10%]">Class</th>
                    <th scope="col" class="w-[5%]">Level</th>
                    <th scope="col" class="w-[10%] text-right">-</th>
                </tr>
            </x-slot:head>
            <x-slot:body>
                @forelse ($npcs as $npc)
                    @php
                        $zone = $zoneMap[$npc->id] ?? '';
                    @endphp
                    <tr>
                        <td>{{ $npc->id }}</td>
                        <td>
                            <a href="{{ route('npcs.edit', $npc->id) }}"
                                class="text-base link-info link-hover">
                                {{ $npc->name ?: ('empty' ?? $npc->id) }}
                            </a>
                        </td>
                        <td>
                            @if(!empty($zone))
                                {{ $zone }}
                            @else
                                <span class="text-error font-semibold">Unknown</span>
                            @endif
                        </td>
                        <td>{{ number_format($npc->hp, 0) }}</td>
                        <td>{{ config('everquest.npc_class')[$npc->class] ?? 'Unknown' }}</td>
                        <td>{{ $npc->level }}</td>
                        <td class="text-right">
                            <div class="join">
                                <form method="POST" action="{{ route('npcs.clone', $npc) }}" class="inline">
                                    @csrf
                                    <input type="hidden" name="redirect" value="edit" />
                                    <button type="submit" class="btn btn-sm btn-soft btn-info join-item tooltip"
                                        data-tip="Clone">
                                        <x-ui.icon name="clone" />
                                    </button>
                                </form>
                                <a class="btn btn-sm join-item btn-soft" href="{{ route('npcs.edit', $npc->id) }}">
                                    <x-ui.icon name="edit" />
                                </a>
                                <form action="{{ route('npcs.destroy', $npc) }}" method="POST" class="inline">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-soft btn-error join-item tooltip" data-tip="Delete"
                                        onclick="return confirm('Delete?')">
                                        <x-ui.icon name="delete" />
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-6 text-base-content/50">
                            No NPCs found.
                        </td>
                    </tr>
                @endforelse
            </x-slot:body>
        </x-ui.table>
    </x-search-results>

    <div class="mt-4">{{ $npcs->links() }}</div>

@endsection
