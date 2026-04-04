@extends('layouts.app')
@section('title', 'Tradeskill Container Templates')
@section('page-title', 'Tradeskill Container Templates')

@section('content')
    <div class="space-y-6" x-data @keydown.window.escape="if(!$store.modalForm.saving) $store.modalForm.close()">

        <x-top-links>
            <button type="button" class="btn btn-soft btn-success"
                @click="$store.modalForm.openCreate({
                    baseUrl: '{{ route('tradeskills.container-templates.store') }}',
                    resourceName: 'Tradeskill Container Template',
                })">
                <x-ui.icon name="add" /> New Container Template
            </button>
        </x-top-links>

        <x-ui.table>
            <x-slot:head>
                <tr>
                    <th class="w-[20%]">Name</th>
                    <th class="w-[10%]">Skill</th>
                    <th>Object/Item</th>
                    <th class="w-[10%] text-right">Actions</th>
                </tr>
            </x-slot:head>
            <x-slot:body>
                @forelse ($templates as $template)
                    <tr x-data data-tctemplate='@json($template)'>
                        <td class="font-medium">{{ $template->name }}</td>
                        <td>{{ config('everquest.skills.tradeskill')[$template->skill] ?? 'Unknown' }}</td>
                        <td>
                            @if ($template->items->isNotEmpty())
                                <div class="grid grid-cols-4 gap-2">
                                    @foreach ($template->items as $item)
                                        @php
                                            $resolved = $item->resolved_item ?? null;
                                        @endphp

                                        @if ($resolved)
                                            <div>
                                                <x-item-link
                                                    :item_id="$resolved['id'] ?? null"
                                                    :item_name="$resolved['name'] ?? 'Unknown'"
                                                    :item_icon="$resolved['icon'] ?? null"
                                                />
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            @endif
                        </td>
                        <td class="text-right space-x-2">
                            <div class="join">
                                <button type="button" class="join-item btn btn-sm btn-soft tooltip" data-tip="Edit"
                                    @click="$store.modalForm.openEdit(
                                        $el.closest('tr').dataset.tctemplate,
                                        '{{ route('tradeskills.container-templates.update', $template) }}',
                                        { resourceName: 'Edit Tradeskill Container Template' }
                                    )">
                                    <x-ui.icon name="edit" />
                                </button>
                                <form action="{{ route('tradeskills.container-templates.destroy', $template) }}"
                                    method="POST" class="inline"
                                    onsubmit="return confirm('Delete this container template?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="join-item btn btn-sm btn-soft btn-error">
                                        <x-ui.icon name="delete" />
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center py-6 text-base-content/50">
                            No tradeskill container templates found.
                        </td>
                    </tr>
                @endforelse
            </x-slot:body>
        </x-ui.table>

        <x-modal-form>
            @include('tradeskills.container-templates.forms.form')
        </x-modal-form>

    </div>
@endsection
