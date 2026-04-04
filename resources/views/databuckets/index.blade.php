@extends('layouts.app')
@section('title', 'Data Buckets')
@section('page-title', 'Data Buckets')

@section('content')
    <div x-data @keydown.window.escape="if(!$store.modalForm.saving) $store.modalForm.close()">

        <x-top-links>
            <x-slot name="left">
                @include('databuckets.partials.filters')
            </x-slot>
            <button type="button" class="btn btn-soft btn-success"
                @click="$store.modalForm.openCreate({
                    baseUrl: '{{ route('databuckets.store') }}',
                    resourceName: 'DataBucket'
                })">
                <x-ui.icon name="add" /> New Data Bucket
            </button>
        </x-top-links>

        <x-search-results :items="$databuckets" title="Data Buckets">
            <x-ui.table>
                <x-slot:head>
                    <tr>
                        <th scope="col" class="w-[20%]">Key</th>
                        <th scope="col" class="w-[10%]">Value</th>
                        <th scope="col" class="w-[10%]">Expires</th>
                        <th scope="col" class="w-[10%]">Account</th>
                        <th scope="col" class="w-[10%]">Character</th>
                        <th scope="col" class="w-[10%]">NPC</th>
                        <th scope="col" class="w-[10%]">Bot</th>
                        <th scope="col" class="w-[10%]">Zone/Instance</th>
                        <th scope="col" class="w-[10%] text-right">-</th>
                    </tr>
                </x-slot:head>
                <x-slot:body>
                    @foreach ($databuckets as $databucket)
                        <tr x-data data-databucket='@json($databucket)'>
                            <td>{{ $databucket->key }}</td>
                            <td>{{ $databucket->value ?? '-' }}</td>
                            <td>
                                {{ $databucket->expires > 0
                                    ? \Carbon\Carbon::parse($databucket->expires)->format('Y-m-d H:i')
                                    : '-' }}
                            </td>
                            <td>{{ $databucket->account->name ?? '-' }}</td>
                            <td>{{ $databucket->character->name ?? '-' }}</td>
                            <td>{{ $databucket->npc->name ?? '-' }}</td>
                            <td>-</td>
                            <td>-</td>
                            <td class="text-right">
                                <div class="inline join">
                                    <button type="button" class="join-item btn btn-sm btn-soft tooltip"
                                        data-tip="Edit"
                                        @click="$store.modalForm.openEdit(
                                            $el.closest('tr').dataset.databucket,
                                            '{{ route('databuckets.update', $databucket) }}',
                                            { resourceName: 'Edit Data Bucket' }
                                        )">
                                        <x-ui.icon name="edit" />
                                    </button>
                                    <form action="{{ route('databuckets.destroy', $databucket) }}" method="POST"
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
        </x-search-results>

        <div class="mt-4 shrink-0">{{ $databuckets->links() }}</div>

        <x-modal-form>
            @include('databuckets.forms.form')
        </x-modal-form>
    </div>
@endsection
