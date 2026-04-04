@extends('layouts.app')
@section('title', 'Spawn Groups')
@section('page-title', 'Spawn Groups')

@section('content')
    <div x-data>

        <x-top-links>
            <x-slot name="left">
                -
            </x-slot>
            <button type="button" class="btn btn-soft btn-success float-end"
                @click="$store.modalForm.openCreate({
                    baseUrl: '{{ route('spawngroups.store') }}',
                    resourceName: 'Spawn Group'
                })">
                <x-ui.icon name="add" /> New Spawn Group
            </button>
        </x-top-links>

        <x-ui.table>
            <x-slot:head>
                <tr>
                    <th scope="col" class="w-[5%]">ID</th>
                    <th scope="col">Name</th>
                    <th scope="col" class="w-[10%]">Spawn Limit</th>
                    <th scope="col" class="w-[10%]">Entries</th>
                    <th scope="col" class="w-[10%]">Spawn Points</th>
                    <th scope="col" class="w-[10%] text-right">-</th>
                </tr>
            </x-slot:head>
            <x-slot:body>
                @forelse($spawngroups as $sg)
                    <tr>
                        <td>{{ $sg->id }}</td>
                        <td class="font-medium">{{ $sg->name }}</td>
                        <td>{{ $sg->spawn_limit ?? '-' }}</td>
                        <td>{{ $sg->spawnentries_count ?? 0 }}</td>
                        <td>{{ $sg->spawn2_count ?? 0 }}</td>
                        <td class="text-right">
                            <div class="inline join">
                                <a href="{{ route('spawngroups.edit', $sg) }}" class="join-item btn btn-sm btn-soft">
                                    <x-ui.icon name="edit" />
                                </a>
                                <form action="{{ route('spawngroups.destroy', $sg) }}" method="POST" class="inline">
                                    @csrf @method('DELETE')
                                    <button class="join-item btn btn-sm btn-soft btn-error" onclick="return confirm('Delete?')">
                                        <x-ui.icon name="delete" />
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-gray-500 py-6">No spawn groups found.</td>
                    </tr>
                @endforelse
            </x-slot:body>
        </x-ui.table>

        <div class="mt-4">{{ $spawngroups->links() }}</div>

    </div>
@endsection
