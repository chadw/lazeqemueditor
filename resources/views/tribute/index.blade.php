@extends('layouts.app')
@section('title', 'Tribute')
@section('page-title', 'Tribute')

@section('content')
    <div x-data @keydown.window.escape="if(!$store.modalForm.saving) $store.modalForm.close()">

        <x-top-links>
            <button type="button" class="btn btn-soft btn-success"
                @click="$store.modalForm.openCreate({
                modal: 'tribute',
                baseUrl: '{{ route('tribute.store') }}',
                resourceName: 'Tribute',
                refreshOnSuccess: true
            })">
                <x-ui.icon name="add" /> New Tribute
            </button>
        </x-top-links>

        <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
            @foreach ($tributes as $tribute)
                <div class="card bg-base-100 shadow">
                    <div class="card-body p-4">
                        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">
                            <div class="min-w-0">
                                <h2 class="card-title text-lg font-bold truncate">
                                    {{ $tribute->name }}
                                    @if ($tribute->isguild == 1)
                                        <span class="badge badge-success badge-soft">GUILD</span>
                                    @else
                                        <span class="badge badge-info badge-soft">PERSONAL</span>
                                    @endif
                                </h2>
                                <p class="text-sm text-neutral-500 font-normal mt-0.5 mr-1 leading-tight truncate">
                                    {{ $tribute->descr }}
                                </p>
                            </div>
                            <div class="join shrink-0">
                                <button type="button" class="join-item btn btn-sm btn-soft tooltip" data-tip="Edit"
                                    @click="$store.modalForm.openEdit(
                                        {{ $tribute->toJson() }},
                                        '{{ route('tribute.update', ['id' => $tribute->id, 'isguild' => (int) $tribute->isguild]) }}',
                                        {
                                            modal: 'tribute',
                                            resourceName: 'Edit Tribute',
                                            refreshOnSuccess: true
                                        }
                                )">
                                    <x-ui.icon name="edit" />
                                </button>
                                <form
                                    action="{{ route('tribute.destroy', ['id' => $tribute->id, 'isguild' => (int) $tribute->isguild]) }}"
                                    method="POST">
                                    @csrf @method('DELETE')
                                    <button class="join-item btn btn-sm btn-soft btn-error tooltip" data-tip="Delete"
                                        onclick="return confirm('Delete this Tribute?')">
                                        <x-ui.icon name="delete" />
                                    </button>
                                </form>
                            </div>
                        </div>

                        <div class="divider my-2"></div>

                        <table class="table table-auto table-zebra md:table-fixed w-full">
                            <thead class="text-xs uppercase bg-neutral">
                                <tr>
                                    <th scope="col" class="w-[10%]">Level</th>
                                    <th scope="col" class="w-[10%]">Cost</th>
                                    <th scope="col">Item</th>
                                    <th scope="col" class="w-[20%] text-right">-</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($tribute->levels as $entry)
                                    <tr>
                                        <td>{{ $entry->level }}</td>
                                        <td>{{ $entry->cost }}</td>
                                        <td class="truncate">
                                            @if ($entry->item)
                                                <x-item-link
                                                    :item_id="$entry->item->id"
                                                    :item_name="$entry->item->Name"
                                                    :item_icon="$entry->item->icon"
                                                    item_class="flex"
                                                />
                                            @else
                                                <span class="text-error">Item #{{ $entry->item_id }} not found</span>
                                            @endif
                                        </td>
                                        <td class="text-right">
                                            <div class="join">
                                                <button type="button" class="join-item btn btn-sm btn-soft tooltip"
                                                    data-tip="Edit"
                                                    @click="$store.modalForm.openEdit(
                                                        {{ $entry->toJson() }},
                                                        '{{ route('tribute.levels.update', [
                                                            'tribute_id' => $entry->tribute_id,
                                                            'level' => $entry->level
                                                        ]) }}',
                                                        {
                                                            modal: 'tribute-entry',
                                                            resourceName: 'Edit Tribute Entry',
                                                            refreshOnSuccess: true
                                                        }
                                                    )">
                                                    <x-ui.icon name="edit" />
                                                </button>
                                                <form
                                                    action="{{ route('tribute.levels.destroy', [
                                                        'tribute_id' => $entry->tribute_id,
                                                        'level' => $entry->level
                                                    ]) }}"
                                                    method="POST">
                                                    @csrf @method('DELETE')
                                                    <button class="join-item btn btn-sm btn-soft btn-error tooltip"
                                                        data-tip="Delete"
                                                        onclick="return confirm('Remove this Tribute entry?')">
                                                        <x-ui.icon name="delete" />
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-sm opacity-60">
                                            No tribute entries assigned
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <div class="mt-3 text-right">
                            <button type="button" class="btn btn-xs btn-soft btn-success"
                                @click="$store.modalForm.openCreate({
                                    modal: 'tribute-entry',
                                    baseUrl: '{{ route('tribute.levels.store', ['tribute_id' => $tribute->id]) }}',
                                    resourceName: 'Add Tribute Entry',
                                    refreshOnSuccess: true,
                                    defaults: {
                                        tribute_id: {{ $tribute->id }},
                                    }
                                })">
                                <x-ui.icon name="add" /> Add Entry
                            </button>
                        </div>

                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-4">{{ $tributes->links() }}</div>

        <x-modal-form x-show="$store.modalForm.isOpen">
            <template x-if="$store.modalForm.activeModal === 'tribute'">
                @include('tribute.forms.form')
            </template>

            <template x-if="$store.modalForm.activeModal === 'tribute-entry'">
                @include('tribute.forms.form-entry')
            </template>
        </x-modal-form>

    </div>
@endsection
