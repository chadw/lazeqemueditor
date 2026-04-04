@extends('layouts.app')
@section('title', 'Loot Drops')
@section('page-title', 'Loot Drops')

@section('content')
    <div x-data @keydown.window.escape="if(!$store.modalForm.saving) $store.modalForm.close()">
        <x-top-links>
            <x-slot name="left">
                @include('loot.drops.partials.filters')
            </x-slot>
        </x-top-links>

        @if (request()->boolean('orphan'))
            <div class="mb-4">
                <x-ui.alert-warning>
                    You are currently viewing <strong>orphaned</strong> loot drops. These are drops that are not currently attached to any loot tables and may be safe to delete.
                </x-ui.alert-warning>
            </div>
        @endif

        <x-search-results :items="$drops" title="Loot Drops">
            <x-ui.table>
                <x-slot:head>
                    <tr>
                        <th scope="col" class="w-[5%]">ID</th>
                        <th scope="col">Name</th>
                        <th scope="col" class="w-[5%]"># Drops</th>
                        <th scope="col" class="tooltip w-[5%]" title="# of Loot Tables this Loot Drop is attached to">
                            # Tables
                        </th>
                        <th scope="col" class="w-[15%] text-right">-</th>
                    </tr>
                </x-slot:head>
                <x-slot:body>
                    @foreach ($drops as $drop)
                        <tr>
                            <td>{{ $drop->id }}</td>
                            <td>
                                <a href="{{ route('loot.drops.edit', $drop) }}" class="text-base link-accent link-hover">{{ $drop->name }}</a>
                            </td>
                            <td>{{ $drop->entries_count ?? 0 }}</td>
                            <td>{{ $drop->loottable_entries_count ?? 0 }}</td>
                            <td class="text-right space-x-2">
                                <div class="inline join">
                                    <form action="{{ route('loot.drops.clone', $drop) }}" method="POST" class="inline">
                                        @csrf
                                        <button class="join-item btn btn-sm btn-soft btn-info tooltip"
                                            data-tip="Clone"
                                            onclick="return confirm('Clone this Loot Drop and all associated entries?')">
                                            <x-ui.icon name="clone" />
                                        </button>
                                    </form>
                                    <a href="{{ route('loot.drops.edit', $drop) }}"
                                        class="join-item btn btn-sm btn-soft tooltip"
                                        data-tip="Edit">
                                        <x-ui.icon name="edit" />
                                    </a>
                                    <form action="{{ route('loot.drops.unlink', $drop) }}" method="POST" class="inline">
                                        @csrf
                                        <button class="join-item btn btn-sm btn-soft btn-warning tooltip"
                                            data-tip="Unlink"
                                            onclick="return confirm('Unlink this Loot Drop from ALL Loot Tables?')">
                                            <x-ui.icon name="unlink" />
                                        </button>
                                    </form>
                                    <form action="{{ route('loot.drops.destroy', $drop) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button class="join-item btn btn-sm btn-soft btn-error tooltip"
                                            data-tip="Delete"
                                            onclick="return confirm('Delete Loot Drop?')">
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
            {{ $drops->links() }}
        </div>
    </div>
@endsection
