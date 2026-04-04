@extends('layouts.app')
@section('title', 'Discord Webhooks')
@section('page-title', 'Discord Webhooks')

@section('content')
    <div x-data @keydown.window.escape="if(!$store.modalForm.saving) $store.modalForm.close()">

        <x-top-links>
            <button type="button" class="btn btn-soft btn-success float-end"
                @click="$store.modalForm.openCreate({
                    baseUrl: '{{ route('discord-webhooks.store') }}',
                    resourceName: 'Discord Webhooks'
                })">
                <x-ui.icon name="add" /> New Discord Webhook
            </button>
        </x-top-links>

        <x-ui.table>
            <x-slot:head>
                <tr>
                    <th scope="col" class="w-[10%]">Name</th>
                    <th scope="col">URL</th>
                    <th scope="col" class="w-[10%]">Created</th>
                    <th scope="col" class="w-[10%] text-right">-</th>
                </tr>
            </x-slot:head>
            <x-slot:body>
                @foreach ($hooks as $hook)
                    <tr x-data data-hook='@json($hook)'>
                        <td>{{ $hook->webhook_name }}</td>
                        <td class="truncate">{{ $hook->webhook_url }}</td>
                        <td>{{ $hook->created_at?->format('M d, Y H:i A') }}</td>
                        <td class="text-right">
                            <div class="join">
                                <button type="button" class="join-item btn btn-sm btn-soft tooltip" data-tip="Edit"
                                    @click="$store.modalForm.openEdit(
                                        $el.closest('tr').dataset.hook,
                                        '{{ route('discord-webhooks.update', $hook) }}',
                                        { resourceName: 'Edit Discord Webhook' }
                                    )">
                                    <x-ui.icon name="edit" />
                                </button>
                                <form action="{{ route('discord-webhooks.destroy', $hook) }}" method="POST"
                                    class="inline">
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
            @include('discord-webhooks.forms.form')
        </x-modal-form>
    </div>
@endsection
