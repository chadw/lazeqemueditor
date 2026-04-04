@extends('layouts.app')
@section('page-title', 'Pet Equipment Sets')

@section('content')
    <div x-data @keydown.window.escape="if(!$store.modalForm.saving) $store.modalForm.close()">

        <x-top-links>
            <button type="button" class="btn btn-soft btn-success float-end"
                @click="$store.modalForm.openCreate({
                modal: 'pet-equipment',
                baseUrl: '{{ route('pets.equipment.store') }}',
                resourceName: 'Equipment Set'
            })">
                <x-ui.icon name="add" /> New Pet Equipment Set
            </button>
        </x-top-links>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            @foreach ($petEquip as $set)
                <x-ui.card>
                    <x-slot:header>
                        <h3 class="card-title text-lg">{{ $set->setname }}</h3>
                        <div class="join">
                            <button type="button" class="join-item btn btn-sm btn-soft tooltip"
                                data-tip="Edit"
                                @click="$store.modalForm.openEdit(
                                {{ $set->toJson() }},
                                '{{ route('pets.equipment.update', $set) }}',
                                {
                                    modal: 'pet-equipment',
                                    resourceName: 'Edit Pet Equipment Set'
                                }
                            )">
                                <x-ui.icon name="edit" />
                            </button>
                            <form action="{{ route('pets.equipment.destroy', $set) }}" method="POST">
                                @csrf @method('DELETE')
                                <button class="join-item btn btn-sm btn-soft btn-error tooltip"
                                    data-tip="Delete"
                                    onclick="return confirm('Delete this equipment set?')">
                                    <x-ui.icon name="delete" />
                                </button>
                            </form>
                        </div>
                    </x-slot:header>
                    <div>
                        <table class="table table-auto table-zebra md:table-fixed w-full">
                            <thead class="text-xs uppercase bg-neutral">
                                <tr>
                                    <th>Slot</th>
                                    <th>Item</th>
                                    <th class="text-right">-</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($set->petEquipmentsetEntries as $entry)
                                    <tr>
                                        <td>
                                            {{ config('everquest.slots_inv')[$entry->slot] ?? 'Unknown' }}
                                            <span class="badge badge-sm badge-soft">{{ $entry->slot }}</span>
                                        </td>
                                        <td>
                                            <x-item-link
                                                :item_id="$entry->item->id"
                                                :item_name="$entry->item->Name"
                                                :item_icon="$entry->item->icon"
                                                item_class="flex"
                                            />
                                        </td>
                                        <td class="text-right">
                                            <div class="join">
                                                <button type="button" class="join-item btn btn-sm btn-soft tooltip"
                                                    data-tip="Edit"
                                                    @click="$store.modalForm.openEdit(
                                                        {{ $entry->toJson() }},
                                                        '{{ route('pets.equipment-items.update', [
                                                            'set' => $entry->set_id,
                                                            'slot' => $entry->slot,
                                                        ]) }}',
                                                        {
                                                            modal: 'pet-equipment-item',
                                                            resourceName: 'Edit Entry'
                                                        }
                                                    )">
                                                    <x-ui.icon name="edit" />
                                                </button>

                                                <form action="{{ route('pets.equipment-items.destroy', [
                                                    'set' => $entry->set_id,
                                                    'slot' => $entry->slot,
                                                ]) }}"
                                                    method="POST">
                                                    @csrf @method('DELETE')
                                                    <button class="join-item btn btn-sm btn-soft btn-error tooltip"
                                                        data-tip="Delete"
                                                        onclick="return confirm('Remove this item?')">
                                                        <x-ui.icon name="delete" />
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-sm opacity-60">
                                            No equipment assigned
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <x-slot:footer>
                        <div></div>
                        <button type="button" class="btn btn-xs btn-soft btn-success"
                            @click="$store.modalForm.openCreate({
                                modal: 'pet-equipment-item',
                                baseUrl: '{{ route('pets.equipment-items.store') }}',
                                resourceName: 'Add Equipment',
                                defaults: {
                                    set_id: {{ $set->set_id }}
                                }
                            })">
                            <x-ui.icon name="add" /> Add Entry
                        </button>
                    </x-slot:footer>
                </x-ui.card>
            @endforeach
        </div>

        <div class="mt-4">{{ $petEquip->links() }}</div>

        <x-modal-form x-show="$store.modalForm.isOpen">
            <template x-if="$store.modalForm.activeModal === 'pet-equipment'">
                @include('pets.equipment.forms.form')
            </template>
            <template x-if="$store.modalForm.activeModal === 'pet-equipment-item'">
                @include('pets.equipment.forms.form-entry')
            </template>
        </x-modal-form>

    </div>
@endsection
