@extends('layouts.app')
@section('title', 'Chat Channels')
@section('page-title', 'Chat Channels')

@section('content')
    <div x-data @keydown.window.escape="if(!$store.modalForm.saving) $store.modalForm.close()">

        <x-top-links>
            <button type="button" class="btn btn-soft btn-success float-end"
                @click="$store.modalForm.openCreate({
                    baseUrl: '{{ route('chats.store') }}',
                    resourceName: 'Chat',
                    defaults: {
                        owner: '*System*',
                        minstatus: 0,
                    }
                })">
                <x-ui.icon name="add" /> New Chat Channel
            </button>
        </x-top-links>

        <x-ui.table>
            <x-slot:head>
                <tr>
                    <th scope="col" class="w-[40%]">Name</th>
                    <th scope="col" class="w-[30%]">Owner</th>
                    <th scope="col" class="w-[20%]">Status</th>
                    <th scope="col" class="w-[10%] text-right">-</th>
                </tr>
            </x-slot:head>
            <x-slot:body>
                @foreach ($chats as $c)
                    <tr x-data data-chat='@json($c)'>
                        <td>{{ $c->name }}</td>
                        <td>{{ $c->owner ?? '-' }}</td>
                        <td>
                            {{ $c->minstatus ?? '-' }}:
                            {{ config('everquest.account_status')[$c->minstatus] ?? '-' }}
                        </td>
                        <td class="text-right">
                            <div class="inline join">
                                <button type="button" class="join-item btn btn-sm btn-soft tooltip" data-tip="Edit"
                                    @click="$store.modalForm.openEdit(
                                        $el.closest('tr').dataset.chat,
                                        '{{ route('chats.update', $c) }}',
                                        { resourceName: 'Edit Chat Channel' }
                                    )">
                                    <x-ui.icon name="edit" />
                                </button>
                                <form action="{{ route('chats.destroy', $c) }}" method="POST" class="inline">
                                    @csrf @method('DELETE')
                                    <button class="join-item btn btn-sm btn-soft btn-error" data-tip="Delete"
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

        <x-modal-form>
            @include('chats.forms.form')
        </x-modal-form>
    </div>
@endsection
