@extends('layouts.app')
@section('title', 'Alt Currency')
@section('page-title', 'Alt Currency')

@section('content')
    <div x-data @keydown.window.escape="if(!$store.modalForm.saving) $store.modalForm.close()">

        <x-top-links>
            <a href="{{ route('alt-currency.characters.index') }}" class="btn btn-soft btn-accent">
                Characters
            </a>
            <a href="{{ route('alt-currency.npcs.index') }}" class="btn btn-soft btn-accent">
                NPCs
            </a>
            <button type="button" class="btn btn-soft btn-success"
                @click="$store.modalForm.openCreate({
                    baseUrl: '{{ route('alt-currency.store') }}',
                    resourceName: 'Alt Currency'
                })">
                <x-ui.icon name="add" /> New Alt Currency
            </button>
        </x-top-links>

        <x-ui.table>
            <x-slot:head>
                <tr>
                    <th scope="col" class="w-[5%]">@sortablelink('id', 'ID')</th>
                    <th scope="col" class="w-[5%]">@sortablelink('item_id', 'Item Id')</th>
                    <th scope="col">@sortablelink('item', 'Item')</th>
                    <th scope="col" class="w-[10%] text-right">-</th>
                </tr>
            </x-slot:head>
            <x-slot:body>
                @foreach ($altcurrency as $ac)
                    <tr x-data data-altcurrency='@json($ac)'>
                        <td>{{ $ac->id }}</td>
                        <td>{{ $ac->item_id }}</td>
                        <td>
                            @if ($ac && $ac->item)
                                <x-item-link
                                    :item_id="$ac->item->id"
                                    :item_name="'(' . $ac->item->id . ') ' . $ac->item->Name"
                                    :item_icon="$ac->item->icon"
                                    item_class="flex"
                                />
                            @else
                                <span class="italic text-gray-500">Missing Alt Currency Item</span>
                            @endif
                        </td>
                        <td class="text-right items-end space-x-2">
                            <div class="inline join">
                                <button type="button" class="join-item btn btn-sm btn-soft tooltip" data-tip="Edit"
                                    @click="$store.modalForm.openEdit(
                                        $el.closest('tr').dataset.altcurrency,
                                        '{{ route('alt-currency.update', $ac) }}',
                                        { resourceName: 'Edit Alt Currency' }
                                    )">
                                    <x-ui.icon name="edit" />
                                </button>
                                <form action="{{ route('alt-currency.destroy', $ac) }}" method="POST"
                                    class="inline">
                                    @csrf @method('DELETE')
                                    <button class="join-item btn btn-sm btn-soft btn-error tooltip" data-tip="Delete"
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

        <div class="mt-4">{{ $altcurrency->links() }}</div>

        <x-modal-form width="max-w-3xl">
            @include('alt-currency.forms.form')
        </x-modal-form>
    </div>
@endsection
