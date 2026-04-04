@extends('layouts.app')
@section('title', 'NPC Emotes')
@section('page-title', 'NPC Emotes')

@section('content')
    <div x-data @keydown.window.escape="if(!$store.modalForm.saving) $store.modalForm.close()">

        <x-top-links>
            <button type="button" class="btn btn-soft btn-success float-end"
                @click="$store.modalForm.openCreate({
                    baseUrl: '{{ route('npc-emotes.store') }}',
                    resourceName: 'Emote'
                })">
                <x-ui.icon name="add" /> New Emote
            </button>
        </x-top-links>

        <x-ui.table>
            <x-slot:head>
                <tr>
                    <th scope="col" class="w-[5%]">Emote ID</th>
                    <th scope="col" class="w-[10%]">Event</th>
                    <th scope="col" class="w-[10%]">Type</th>
                    <th scope="col">Text</th>
                    <th scope="col" class="w-[10%]">-</th>
                </tr>
            </x-slot:head>
            <x-slot:body>
                @foreach ($npcEmotes as $emote)
                    <tr x-data data-emote='@json($emote)'>
                        <td>{{ $emote->emoteid }}</td>
                        <td>{{ config('everquest.emote_event.' . $emote->event_) }}</td>
                        <td>{{ config('everquest.emote_type.' . $emote->type) }} </td>
                        <td class="truncate">{{ Str::limit($emote->text, 150, ' ...') }}</td>
                        <td class="text-right">
                            <div class="inline join">
                                <button type="button" class="join-item btn btn-sm btn-soft tooltip"
                                    data-tip="Edit"
                                    @click="$store.modalForm.openEdit(
                                        $el.closest('tr').dataset.emote,
                                        '{{ route('npc-emotes.update', $emote) }}',
                                        { resourceName: 'Edit Emote' }
                                    )">
                                    <x-ui.icon name="edit" />
                                </button>
                                <form action="{{ route('npc-emotes.destroy', $emote) }}" method="POST"
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
                @endforeach
            </x-slot:body>
        </x-ui.table>

        <div class="mt-4 shrink-0">{{ $npcEmotes->links() }}</div>

        <x-modal-form>
            @include('npc-emotes.forms.form')
        </x-modal-form>
    </div>
@endsection
