@extends('layouts.app')
@section('title', 'Loot Tables')
@section('page-title', 'Loot Tables')

@section('content')
    <div x-data @keydown.window.escape="if(!$store.modalForm.saving) $store.modalForm.close()">

        <x-top-links>
            <x-slot name="left">
                @include('loot.partials.filters')
            </x-slot>
            <button type="button" class="btn btn-soft btn-success"
                @click="$store.modalForm.openCreate({
                    baseUrl: '{{ route('loot.index') }}',
                    resourceName: 'Loot Table',
                    defaults: {
                        mincash: 0,
                        maxcash: 0,
                    }
                })">
                <x-ui.icon name="add" /> New Loot Table
            </button>
        </x-top-links>

        <x-search-results :items="$tables" title="Loot Tables">
            <x-ui.table>
                <x-slot:head>
                    <tr>
                        <th scope="col" class="w-[5%]">ID</th>
                        <th scope="col">Name</th>
                        <th scope="col" class="w-[5%]"># NPCs</th>
                        <th scope="col" class="w-[5%]">Drops</th>
                        <th scope="col" class="w-[30%]">Cash</th>
                        <th scope="col" class="w-[15%] text-right">-</th>
                    </tr>
                </x-slot:head>
                <x-slot:body>
                    @foreach ($tables as $table)
                        <tr>
                            <td>{{ $table->id }}</td>
                            <td>
                                <a href="{{ route('loot.edit', $table) }}"
                                    class="text-base link-accent link-hover">
                                    {{ $table->name }}
                                </a>
                            </td>
                            <td>{{ $table->npcs_count ?? 0 }}</td>
                            <td>{{ $table->loottable_entries_count }}</td>
                            <td>
                                <div class="inline-flex items-center gap-2">
                                    <span class="badge badge-sm badge-soft">
                                        <x-currency :value="$table->mincash" />
                                    </span>
                                    <span class="opacity-60">to</span>
                                    <span class="badge badge-sm badge-soft">
                                        <x-currency :value="$table->maxcash" />
                                    </span>
                                </div>
                            </td>
                            <td class="text-right space-x-2">
                                <div class="inline join">
                                    <form action="{{ route('loot.clone', $table) }}" method="POST" class="inline">
                                        @csrf
                                        <button class="join-item btn btn-sm btn-soft btn-info tooltip"
                                            data-tip="Clone"
                                            onclick="return confirm('Clone this Loot Table and all associated drops/entries?')">
                                            <x-ui.icon name="clone" />
                                        </button>
                                    </form>
                                    <a href="{{ route('loot.edit', $table) }}"
                                        class="join-item btn btn-sm btn-soft tooltip"
                                        data-tip="Edit">
                                        <x-ui.icon name="edit" />
                                    </a>
                                    <form action="{{ route('loot.unlink', $table) }}" method="POST" class="inline">
                                        @csrf
                                        <button class="join-item btn btn-sm btn-soft btn-warning tooltip"
                                            data-tip="Unlink"
                                            onclick="return confirm('Unlink this Loot Table from ALL NPCs?')">
                                            <x-ui.icon name="unlink" />
                                        </button>
                                    </form>
                                    <form action="{{ route('loot.destroy', $table) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button class="join-item btn btn-sm btn-soft btn-error tooltip"
                                            data-tip="Delete"
                                            onclick="return confirm('Delete Loot Table?')">
                                            <x-ui.icon name="delete" />
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </x-slot:body>
            </x-ui.table>
        </x-search-results>

        <div class="mt-4 shrink-0">
            {{ $tables->links() }}
        </div>

        <x-modal-form>
            @include('loot.forms.new-loottable')
        </x-modal-form>

    </div>
@endsection
