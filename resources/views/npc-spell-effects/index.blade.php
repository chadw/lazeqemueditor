@extends('layouts.app')
@section('title', "NPC Spell Effects")
@section('page-title', "NPC Spell Effects")

@section('content')
    <x-ui.table>
        <x-slot:head>
            <tr>
                <th scope="col" class="w-[80%]">Name</th>
                <th scope="col">Parent</th>
                <th scope="col" class="w-[10%] text-right">-</th>
            </tr>
        </x-slot:head>
        <x-slot:body>
            @forelse ($npc_spell_effects as $se)
                <tr>
                    <td>{{ $se->name }}</td>
                    <td>{{ $se->parent_list }}</td>
                    <td class="text-right">
                        <div class="inline join">
                            <a href="{{ route('npc-spell-effects.edit', $se) }}"
                                class="join-item btn btn-sm btn-soft">
                                <x-ui.icon name="edit" />
                            </a>
                            <form action="{{ route('npc-spell-effects.destroy', $se) }}" method="POST"
                                class="inline">
                                @csrf @method('DELETE')
                                <button class="join-item btn btn-sm btn-soft btn-error"
                                    onclick="return confirm('Delete?')">
                                    <x-ui.icon name="delete" />
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="text-center py-6 text-base-content/50">
                        No npc spell effects found.
                    </td>
                </tr>
            @endforelse
        </x-slot:body>
    </x-ui.table>
@endsection
