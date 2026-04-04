@extends('layouts.app')
@section('page-title', 'Graveyards')

@section('content')
    <div x-data @keydown.window.escape="if(!$store.modalForm.saving) $store.modalForm.close()">

        <x-top-links>
            <button type="button" class="btn btn-soft btn-success"
                @click="$store.modalForm.openCreate({
                baseUrl: '{{ route('zones.graveyards.store') }}',
                resourceName: 'Graveyard',
                defaults: {
                    x: 0,
                    y: 0,
                    z: 0,
                    heading: 0
                }
            })">
                <x-ui.icon name="add" /> New Graveyard
            </button>
        </x-top-links>

        <x-ui.table>
            <x-slot:head>
                <tr>
                    <th scope="col">Zone</th>
                    <th scope="col" class="w-[20%]">Coords</th>
                    <th scope="col" class="w-[10%] text-right">-</th>
                </tr>
            </x-slot:head>
            <x-slot:body>
                @forelse ($graveyards as $graveyard)
                    <tr x-data data-graveyard='@json($graveyard)'>
                        <td scope="row">
                            {{ $graveyard->zone?->short_name ?? 'Unknown' }}
                        </td>
                        <td>
                            x: {{ floor($graveyard->x) }},
                            y: {{ floor($graveyard->y) }},
                            z: {{ floor($graveyard->z) }},
                            heading: {{ floor($graveyard->heading) }}
                        </td>
                        <td class="text-right">
                            <div class="inline join">
                                <button type="button" class="join-item btn btn-sm btn-soft tooltip"
                                    data-tip="Edit"
                                    @click="$store.modalForm.openEdit(
                                        $el.closest('tr').dataset.graveyard,
                                        '{{ route('zones.graveyards.update', $graveyard) }}',
                                        { resourceName: 'Edit Graveyard' }
                                    )">
                                    <x-ui.icon name="edit" />
                                </button>
                                <form action="{{ route('zones.graveyards.destroy', $graveyard) }}"
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
                        <td colspan="3" class="text-center py-6 text-base-content/50">
                            No graveyards found.
                        </td>
                    </tr>
                @endforelse
            </x-slot:body>
        </x-ui.table>

        <div class="mt-4">{{ $graveyards->links() }}</div>

        <x-modal-form>
            @include('zones.graveyards.partials.form')
        </x-modal-form>
    </div>
@endsection
