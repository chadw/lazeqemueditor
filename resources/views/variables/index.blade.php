@extends('layouts.app')
@section('title', 'Variables')
@section('page-title', 'Variables')

@section('content')
    <div x-data @keydown.window.escape="if(!$store.modalForm.saving) $store.modalForm.close()">

        <x-ui.table>
            <x-slot:head>
                <tr>
                    <th scope="col" class="w-[15%]">Name</th>
                    <th scope="col" class="w-[30%]">Value</th>
                    <th scope="col">Description</th>
                    <th scope="col" class="w-[10%]">Last Update</th>
                    <th scope="col" class="w-[10%] text-right">-</th>
                </tr>
            </x-slot:head>
            <x-slot:body>
                @foreach ($variables as $variable)
                    <tr x-data data-variable='@json($variable)'>
                        <td>{{ $variable->varname ?? '' }}</td>
                        <td>{{ Str::limit($variable->value, 150, ' ...') }}</td>
                        <td>{{ $variable->information }}</td>
                        <td>
                            {{ $variable->ts
                                ? \Carbon\Carbon::parse($variable->ts)->format('Y-m-d H:i')
                                : '' }}
                        </td>
                        <td class="text-right">
                            <div class="inline join">
                                <button type="button" class="join-item btn btn-sm btn-soft"
                                    @click="$store.modalForm.openEdit(
                                        $el.closest('tr').dataset.variable,
                                        '{{ route('variables.update', $variable) }}',
                                        { resourceName: 'Edit Variable' }
                                    )">
                                    <x-ui.icon name="edit" />
                                </button>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </x-slot:body>
        </x-ui.table>

        <x-modal-form>
            @include('variables.forms.form')
        </x-modal-form>
    </div>
@endsection
