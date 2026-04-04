@extends('layouts.app')
@section('title', 'Global Loot Tables')
@section('page-title', 'Global Loot Tables')

@section('content')
    <div x-data @keydown.window.escape="if(!$store.modalForm.saving) $store.modalForm.close()" class="flex flex-col flex-1 min-h-0 gap-4">

        <x-top-links>
            <a href="{{ route('loot.drops.index') }}" class="btn btn-soft btn-accent">
                Loot Drops
            </a>
            <button type="button" class="btn btn-soft btn-success"
                @click="$store.modalForm.openCreate({
                    baseUrl: '{{ route('global-loot.store') }}',
                    resourceName: 'Global Loot',
                    defaults: {
                        loottable_id: {{ $loottableId }},
                        enabled: true,
                        min_level: 0,
                        max_level: 0,
                    }
                })">
                <x-ui.icon name="add" /> New Global Loot
            </button>
        </x-top-links>

        <x-ui.table>
            <x-slot:head>
                <tr>
                    <th scope="col" class="w-[5%]">ID</th>
                    <th scope="col">Name</th>
                    <th scope="col" class="w-[10%]">Loot Table ID</th>
                    <th scope="col" class="w-[10%] text-center">Enabled</th>
                    <th scope="col" class="w-[10%] text-right">-</th>
                </tr>
            </x-slot:head>
            <x-slot:body>
                @foreach ($globalLoot as $gloot)
                    <tr x-data data-globalloot='@json($gloot)'>
                        <td>{{ $gloot->id }}</td>
                        <td>
                            {{ $gloot->description }}
                        </td>
                        <td>
                            {{ $gloot->loottable_id ?? 'N/A' }}
                        </td>
                        <td class="text-center">
                            <x-status :ok="$gloot->enabled" />
                        </td>
                        <td class="text-right space-x-2">
                            <div class="inline join">
                                <a href="{{ route('global-loot.edit', $gloot) }}"
                                    class="join-item btn btn-sm btn-soft tooltip" data-tip="Edit">
                                    <x-ui.icon name="edit" />
                                </a>
                                <form action="{{ route('global-loot.destroy', $gloot) }}"
                                    method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button class="join-item btn btn-sm btn-soft btn-error tooltip"
                                        data-tip="Delete"
                                        onclick="return confirm('Delete Global Loot?')">
                                        <x-ui.icon name="delete" />
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </x-slot:body>
        </x-ui.table>

        <div class="mt-4 shrink-0">
            {{ $globalLoot->links() }}
        </div>

        <x-modal-form>
            @include('global-loot.forms.form')
        </x-modal-form>
    </div>
@endsection
