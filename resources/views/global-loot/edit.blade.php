@extends('layouts.app')
@section('title', 'Edit Global Loot Table')
@section('page-title', 'Edit Global Loot Table')

@section('content')
    <div x-data @keydown.window.escape="if(!$store.modalForm.saving) $store.modalForm.close()">

        <x-top-links>
            <a href="{{ route('global-loot.index') }}" class="btn btn-soft btn-accent">
                Back to Global Loot
            </a>
        </x-top-links>

        @include('global-loot.partials.show')

        <div class="space-y-6">
            @if ($globalLoot->loottable)
                @php
                    $extraGlobal = ($globalLoot->loottable->global_loot_count ?? 1) - 1;
                    $npcCount = $globalLoot->loottable->npcs_count ?? 0;
                @endphp

                @if ($extraGlobal > 0 || $npcCount > 0)
                    <div class="alert alert-soft alert-info">
                        <svg xmlns="http://www.w3.org" fill="none" viewBox="0 0 24 24"
                            class="stroke-current shrink-0 w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div>
                            <h3 class="font-bold">Shared Loot Table</h3>
                            <div>
                                This loot table [<strong>{{ $globalLoot->loottable->name ?: 'Unknown' }}</strong>]
                                is also used by:
                                @if ($extraGlobal > 0)
                                    <span class="ml-2 badge badge-sm badge-ghost">
                                        {{ $extraGlobal }} other Global Loot entries
                                    </span>
                                @endif
                                @if ($npcCount > 0)
                                    <span class="badge badge-sm badge-ghost">{{ $npcCount }} NPCs</span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif

                <div class="card bg-base-200 border border-base-300 shadow-sm mx-auto w-full max-w-3xl">
                    <div class="card-body">
                        <h2 class="card-title">Loot Drop Management</h2>
                        <div class="flex flex-col md:flex-row items-stretch gap-2">
                            <div class="flex-3" x-data="ajaxSelect({
                                searchUrl: '/loot/drops/search',
                                placeholder: 'Search existing Loot Drops...',
                                multiple: false,
                                required: true,
                            })" x-init="init()">
                                <label class="label"><span class="label-text font-bold">Link Existing Drop</span></label>

                                <form action="{{ route('loot.drops.link', $globalLoot->loottable->id) }}" method="POST"
                                    class="flex join w-full">
                                    @csrf
                                    <input type="hidden" name="loottable_id" value="{{ $globalLoot->loottable->id }}">
                                    <select x-ref="select" name="lootdrop_id" class="join-item w-full" required></select>
                                    <button type="submit" class="btn btn-soft btn-primary join-item">Link</button>
                                </form>
                            </div>
                            <div class="divider md:divider-horizontal">OR</div>
                            <div class="flex-1 flex flex-col justify-end">
                                <button type="button" class="btn btn-soft btn-success w-full"
                                    onclick="new_lootdrop_modal.showModal()">
                                    <x-ui.icon name="add" /> New Loot Drop
                                </button>
                            </div>

                        </div>
                    </div>
                </div>
                <dialog id="new_lootdrop_modal" class="modal">
                    <div class="modal-box max-w-2xl">
                        <form method="dialog">
                            <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                        </form>
                        <h3 class="text-lg font-bold">Create New Loot Drop</h3>
                        <p class="text-xs opacity-60 mb-4 italic">This drop will be automatically added to
                            "{{ $globalLoot->description }}"</p>

                        <form action="{{ route('loot.drops.store', $globalLoot->loottable->id) }}" method="POST" class="space-y-4">
                            @csrf
                            <input type="hidden" name="loottable_id" value="{{ $globalLoot->loottable->id }}">
                            <x-form.input
                                name="name"
                                label="Drop Name"
                                placeholder="Ex: Global Rare Spells"
                                required
                            />

                            <div class="grid grid-cols-2 gap-4">
                                <x-form.input
                                    name="mindrops"
                                    label="Min Drops"
                                    type="number"
                                    value="0"
                                />
                                <x-form.input
                                    name="maxdrops"
                                    label="Max Drops"
                                    type="number"
                                    value="1"
                                />
                            </div>

                            <div class="modal-action">
                                <button type="button" class="btn" onclick="new_lootdrop_modal.close()">Cancel</button>
                                <button type="submit" class="btn btn-primary">Create & Attach</button>
                            </div>
                        </form>
                    </div>
                </dialog>

                @foreach ($globalLoot->loottable->loottableEntries as $entry)
                    <div x-data data-loot='@json([
                        'entry' => $entry->getAttributes(),
                        'lootdrop' => $entry->lootdrop,
                    ])'>
                        <x-ui.card>
                            <x-slot:header>
                                <div class="flex items-center gap-3">
                                    <div class="badge badge-soft badge-info font-mono text-xs">
                                        {{ $entry->lootdrop?->name ?? 'LootDrop #' . $entry->lootdrop_id }}
                                    </div>
                                    <h3 class="font-bold text-sm">
                                        <div class="text-xs opacity-60 font-mono">
                                            LootDrop ID: {{ $entry->lootdrop_id }}
                                            · Prob: {{ $entry->probability }}%
                                            · Limit: {{ $entry->droplimit ?? '—' }}
                                            · Min: {{ $entry->mindrop ?? '—' }}
                                        </div>
                                    </h3>
                                </div>
                                <div class="flex gap-4 items-center text-xs uppercase tracking-wider font-bold opacity-80">
                                    <div class="join">
                                        <button type="button" class="join-item btn btn-sm btn-soft tooltip"
                                            data-tip="Edit Loot Drop"
                                            @click="
                                                $store.modalForm.openEdit(
                                                    $el.closest('[data-loot]').dataset.loot,
                                                    '{{ route('loot.entries.update', [
                                                        'loottable' => $globalLoot->loottable->id,
                                                        'lootdrop' => $entry->lootdrop_id,
                                                    ]) }}',
                                                    {
                                                        resourceName: 'Edit Loot Drop',
                                                        modal: 'lootdrop'
                                                    }
                                            )">
                                            <x-ui.icon name="edit" /> Edit Loot Drop
                                        </button>
                                        <form method="POST"
                                            action="{{ route('loot.entries.destroy', [
                                                'loottable' => $globalLoot->loottable->id,
                                                'lootdrop' => $entry->lootdrop_id,
                                            ]) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button class="join-item btn btn-sm btn-soft btn-error tooltip"
                                                data-tip="Delete"
                                                onclick="return confirm('Remove this loot drop from the table?')">
                                                <x-ui.icon name="delete" />
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </x-slot:header>
                            <div class="overflow-x-auto">
                                <table class="table table-zebra table-sm w-full">
                                    <thead class="bg-base-300/50">
                                        <tr>
                                            <th class="w-[5%]">Item ID</th>
                                            <th>Item Name</th>
                                            <th class="w-[10%]">Chance</th>
                                            <th class="w-[10%]">Charges</th>
                                            <th class="w-[10%]">Equip</th>
                                            <th class="w-[15%] text-right">-</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($entry->lootdrop->entries as $dropEntry)
                                            <tr x-data data-dropentry='@json($dropEntry)'>
                                                <td>{{ $dropEntry->item->id }}</td>
                                                <td>
                                                    <x-item-link
                                                        :item_id="$dropEntry->item->id"
                                                        :item_name="$dropEntry->item->Name"
                                                        :item_icon="$dropEntry->item->icon"
                                                        item_class="flex"
                                                    />
                                                </td>
                                                <td>{{ $dropEntry->chance }}%</td>
                                                <td>{{ $dropEntry->item_charges ?? '—' }}</td>
                                                <td>
                                                    @if ($dropEntry->equip_item)
                                                        <span class="badge badge-sm badge-soft badge-success">Yes</span>
                                                    @else
                                                        <span class="badge badge-sm badge-soft badge-error">No</span>
                                                    @endif
                                                </td>

                                                <td class="text-right">
                                                    <div class="join">
                                                        <button type="button" class="join-item btn btn-sm btn-soft tooltip"
                                                            data-tip="Edit"
                                                            @click="$store.modalForm.openEdit(
                                                            $el.closest('tr').dataset.dropentry,
                                                            '{{ route('loot.drops.entries.update', [
                                                                'drop' => $entry->lootdrop_id,
                                                                'item' => $dropEntry->item_id,
                                                            ]) }}',
                                                            {
                                                                modal: 'lootdrop-items',
                                                                resourceName: 'Edit Loot Drop Item'
                                                            }
                                                        )">
                                                            <x-ui.icon name="edit" />
                                                        </button>
                                                        <form method="POST"
                                                            action="{{ route('loot.drops.entries.destroy', [
                                                                'drop' => $entry->lootdrop_id,
                                                                'item' => $dropEntry->item_id,
                                                            ]) }}">
                                                            @csrf
                                                            @method('DELETE')

                                                            <button class="join-item btn btn-sm btn-soft btn-error tooltip"
                                                                data-tip="Delete"
                                                                onclick="return confirm('Remove item from loot drop?')">
                                                                <x-ui.icon name="delete" />
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center opacity-60">
                                                    No items in this loot drop
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
                                    baseUrl: '{{ route('loot.drops.entries.store', $entry->lootdrop_id) }}',
                                    modal: 'lootdrop-items',
                                    resourceName: 'Loot Item',
                                    defaults: {
                                        chance: 1,
                                        item_charges: 1,
                                        equip_item: 0,
                                        disabled_chance: 0,
                                        multiplier: 1,
                                    }
                                })">
                                    <x-ui.icon name="add" /> Add Item
                                </button>
                            </x-slot:footer>
                        </x-ui.card>
                    </div>
                @endforeach
            @else
                <x-ui.alert-warning>
                    Warning: No Loot Table associated with this Global Loot entry (ID: {{ $globalLoot->loottable_id }}).
                </x-ui.alert-warning>
            @endif

            @if ($globalLoot->loottable?->loottableEntries->isEmpty())
                <div class="text-center opacity-60 py-10">
                    This loot table has no loot drops.
                </div>
            @endif
        </div>

        <x-modal-form x-show="$store.modalForm.isOpen">
            <template x-if="$store.modalForm.activeModal === 'global-loot'">
                @include('global-loot.forms.form')
            </template>
            <template x-if="$store.modalForm.activeModal === 'loottable-entry'">
                @include('loot.forms.loottable-entry-form')
            </template>
            <template x-if="$store.modalForm.activeModal === 'lootdrop'">
                @include('loot.forms.lootdrop-form')
            </template>
            <template x-if="$store.modalForm.activeModal === 'lootdrop-items'">
                @include('loot.forms.lootdrop-item-form')
            </template>
        </x-modal-form>
    </div>
@endsection
