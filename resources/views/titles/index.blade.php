@extends('layouts.app')
@section('title', 'Titles')
@section('page-title', 'Titles')

@section('content')
    <div x-data @keydown.window.escape="if(!$store.modalForm.saving) $store.modalForm.close()">

        <x-top-links>
            <button type="button" class="btn btn-soft btn-success float-end"
                @click="$store.modalForm.openCreate({
                    baseUrl: '{{ route('titles.store') }}',
                    resourceName: 'Title'
                })">
                <x-ui.icon name="add" /> New Title
            </button>
        </x-top-links>

        <x-ui.table>
            <x-slot:head>
                <tr>
                    <th scope="col" class="w-[15%]">Prefix</th>
                    <th scope="col" class="w-[15%]">Suffix</th>
                    <th scope="col">Skill</th>
                    <th scope="col">Character</th>
                    <th scope="col">Item</th>
                    <th scope="col" class="w-[10%] md:table-cell text-right">-</th>
                </tr>
            </x-slot:head>
            <x-slot:body>
                @forelse ($titles as $title)
                    @php
                        $skillId = $title->skill_id ?? -1;
                    @endphp
                    <tr x-data data-title='@json($title)'>
                        <td>{{ $title->prefix ?? '' }}</td>
                        <td>{{ $title->suffix ?? '' }}</td>
                        <td>
                            {{ $skillId !== -1 ? config('everquest.db_skills.' . $skillId, '') : '' }}
                            @if ($title->min_skill_value !== -1)
                                ({{ $title->min_skill_value }})
                            @endif
                        </td>
                        <td>{{ $title->character->name ?? '' }}</td>
                        <td>
                            @if ($title->item)
                                <x-item-link
                                    :item_id="$title->item->id"
                                    :item_name="$title->item->Name"
                                    :item_icon="$title->item->icon"
                                    item_class="flex"
                                />
                            @endif
                        </td>
                        <td class="text-right items-end space-x-2">
                            <div class="inline join">
                                <button type="button" class="join-item btn btn-sm btn-soft tooltip"
                                    data-tip="Edit"
                                    @click="$store.modalForm.openEdit(
                                        $el.closest('tr').dataset.title,
                                        '{{ route('titles.update', $title) }}',
                                        { resourceName: 'Edit Title' }
                                    )">
                                    <x-ui.icon name="edit" />
                                </button>
                                <form action="{{ route('titles.destroy', $title) }}"
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
                        <td colspan="6" class="text-center py-6 text-base-content/50">
                            No titles found.
                        </td>
                    </tr>
                @endforelse
            </x-slot:body>
        </x-ui.table>

        <x-modal-form>
            @include('titles.forms.form')
        </x-modal-form>
    </div>
@endsection
