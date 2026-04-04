@extends('layouts.app')
@section('title', 'Content Flags')
@section('page-title', 'Content Flags')

@section('content')
    <div x-data @keydown.window.escape="if(!$store.modalForm.saving) $store.modalForm.close()">

        <x-top-links>
            <button type="button" class="btn btn-soft btn-success"
                @click="$store.modalForm.openCreate({
                baseUrl: '{{ route('content-flags.store') }}',
                resourceName: 'Content Flag',
                defaults: {
                    flag_name: '',
                    enabled: false,
                    notes: ''
                }
            })">
                <x-ui.icon name="add" /> New Content Flag
            </button>
        </x-top-links>

        <x-ui.table>
            <x-slot:head>
                <tr>
                    <th scope="col" class="w-[10%]">ID</th>
                    <th scope="col">Flag Name</th>
                    <th scope="col" class="w-[10%]">Enabled</th>
                    <th scope="col" class="w-[10%] text-right">-</th>
                </tr>
            </x-slot:head>
            <x-slot:body>
                @forelse ($contentFlags as $flag)
                    <tr x-data data-flag='@json($flag)'>
                        <td>{{ $flag->id }}</td>
                        <td>{{ $flag->flag_name }}</td>
                        <td>
                            <x-status :ok="$flag->enabled" />
                        </td>
                        <td class="text-right items-end space-x-2">
                            <div class="inline join">
                                <button type="button" class="join-item btn btn-sm btn-soft tooltip"
                                    data-tip="Edit"
                                    @click="$store.modalForm.openEdit(
                                        $el.closest('tr').dataset.flag,
                                        '{{ route('content-flags.update', $flag) }}',
                                        {
                                            resourceName: 'Edit Content Flag',
                                            booleanFields: ['enabled']
                                        }
                                    )">
                                    <x-ui.icon name="edit" />
                                </button>
                                <form action="{{ route('content-flags.destroy', $flag) }}" method="POST"
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
                        <td colspan="4" class="text-center py-6 text-base-content/50">
                            No content flags found.
                        </td>
                    </tr>
                @endforelse
            </x-slot:body>
        </x-ui.table>

        <x-modal-form>
            @include('content-flags.forms.form')
        </x-modal-form>
    </div>
@endsection
