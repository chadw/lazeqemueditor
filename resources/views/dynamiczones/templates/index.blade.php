@extends('layouts.app')
@section('title', 'Dynamic Zone Templates')
@section('page-title', 'Dynamic Zone Templates')

@section('content')
    <div x-data @keydown.window.escape="if(!$store.modalForm.saving) $store.modalForm.close()">

        <x-top-links>
            <a href="{{ route('dynamiczones.index') }}" class="btn btn-soft btn-accent">
                Dynamic Zones
            </a>
            <button type="button" class="btn btn-soft btn-success float-end"
                @click="$store.modalForm.openCreate({
                    baseUrl: '{{ route('dynamiczones.templates.store') }}',
                    resourceName: 'DZ Template'
                })">
                <x-ui.icon name="add" /> New DZ Template
            </button>
        </x-top-links>

        <x-ui.table>
            <x-slot:head>
                <tr>
                    <th scope="col">Name</th>
                    <th scope="col" class="w-[30%]">Zone</th>
                    <th scope="col" class="w-[10%]">Version</th>
                    <th scope="col" class="w-[10%] text-right">-</th>
                </tr>
            </x-slot:head>
            <x-slot:body>
                @forelse ($templates as $template)
                    <tr x-data data-template='@json($template)'>
                        <td>{{ $template->name }}</td>
                        <td>
                            {{ $template->zone->long_name }}
                            <span class="badge badge-sm badge-soft ml-1">
                                {{ $template->zone->short_name }}
                            </span>
                        </td>
                        <td>{{ $template->zone_version }}</td>
                        <td class="text-right">
                            <div class="join">
                                <button type="button" class="join-item btn btn-sm btn-soft tooltip"
                                    data-tip="Edit"
                                    @click="$store.modalForm.openEdit(
                                        $el.closest('tr').dataset.template,
                                        '{{ route('dynamiczones.templates.update', $template) }}',
                                        { resourceName: 'Edit DZ Template' }
                                    )">
                                    <x-ui.icon name="edit" />
                                </button>
                                <form action="{{ route('dynamiczones.templates.destroy', $template) }}"
                                    method="POST" class="inline">
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
                        <td colspan="4" class="text-center italic opacity-60">No Dynamic Zone Templates found.</td>
                    </tr>
                @endforelse
            </x-slot:body>
        </x-ui.table>

        <div class="mt-4">{{ $templates->links() }}</div>

        <x-modal-form>
            @include('dynamiczones.templates.forms.form')
        </x-modal-form>
    </div>
@endsection
