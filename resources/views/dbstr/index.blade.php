@extends('layouts.app')
@section('title', 'DBStr Editor')
@section('page-title', 'DBStr Editor')

@section('content')
    <div x-data="" @keydown.window.escape="if(!$store.modalForm.saving) $store.modalForm.close()">
        <x-top-links>
            <x-slot name="left">
                <form method="GET" action="{{ route('dbstr.index') }}">
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                        <x-form.select
                            name="dbtype"
                            label="Category"
                            tooltip=""
                            class="join-item"
                            :options="[-1 => 'None'] + $typeOptions"
                            keyInOption="true"
                            :selected="$type ?? -1"
                            onchange="this.form.submit()"
                        />
                    </div>
                </form>
            </x-slot>
            <button type="button" class="btn btn-soft btn-success"
                @click="$store.modalForm.openCreate({
                    baseUrl: '{{ route('dbstr.store') }}',
                    resourceName: 'DBStr',
                    defaults: {
                        type: '{{ $type ?? '' }}',
                        value: '',
                    }
                })">
                <x-ui.icon name="add" /> New DBStr
            </button>
        </x-top-links>

        <x-ui.table height="overflow-x-auto max-h-[75vh] overflow-y-auto" theadsticky="top-0 z-10">
            <x-slot:head>
                <tr>
                    <th class="w-[10%]">ID</th>
                    <th>Value</th>
                    <th class="w-[10%] text-right">-</th>
                </tr>
            </x-slot:head>
            <x-slot:body>
                @forelse ($dbstrs as $dbstr)
                    <tr x-data data-dbstr='@json($dbstr)'>
                        <td>{{ $dbstr->id }}</td>
                        <td class="whitespace-pre-wrap">{{ $dbstr->value }}</td>
                        <td class="text-right">
                            <div class="inline join">
                                <button type="button" class="join-item btn btn-sm btn-soft"
                                    @click="$store.modalForm.openEdit(
                                        $el.closest('tr').dataset.dbstr,
                                        '{{ route('dbstr.update', [$dbstr->type, $dbstr->id]) }}',
                                        { resourceName: 'Edit DBStr' }
                                    )">
                                    <x-ui.icon name="edit" />
                                </button>
                                <form action="{{ route('dbstr.destroy', [$dbstr->type, $dbstr->id]) }}" method="POST" class="inline">
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
                            Select a category above to view dbstr's.
                        </td>
                    </tr>
                @endforelse
            </x-slot:body>
        </x-ui.table>

        @if ($dbstrs->count())
            <div class="mt-4">{{ $dbstrs->links() }}</div>
        @endif

        <x-modal-form width="max-w-3xl">
            @include('dbstr.forms.form')
        </x-modal-form>

    </div>
@endsection
