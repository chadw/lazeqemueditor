@extends('layouts.app')
@section('title', 'Character Base Data')
@section('page-title', 'Character Base Data')

@section('content')
    <div x-data @keydown.window.escape="if(!$store.modalForm.saving) $store.modalForm.close()">
        <x-top-links>
            <x-slot name="left">
                @include('characters.base-data.partials.filters')
            </x-slot>
            <button type="button" class="btn btn-soft btn-success float-end"
                @click="$store.modalForm.openCreate({
                    baseUrl: '{{ route('characters.base-data.store') }}',
                    resourceName: 'Character Base Data',
                    defaults: {
                        level: 1,
                        class: 0,
                        hp: 0,
                        mana: 0,
                        end: 0,
                        hp_regen: 0,
                        end_regen: 0,
                        hp_fac: 1,
                        mana_fac: 1,
                        end_fac: 1,
                    }
                })">
                <x-ui.icon name="add" /> Add Base Data
            </button>
        </x-top-links>

        <x-search-results :items="$rows" title="Base Data Records">
            <x-ui.table>
                <x-slot:head>
                    <tr>
                        <th class="w-[6%]">Level</th>
                        <th class="w-[12%]">Class</th>
                        <th>HP</th>
                        <th>Mana</th>
                        <th>End</th>
                        <th>HP Regen</th>
                        <th>End Regen</th>
                        <th>HP Fac</th>
                        <th>Mana Fac</th>
                        <th>End Fac</th>
                        <th class="w-[10%] text-right">Actions</th>
                    </tr>
                </x-slot:head>
                <x-slot:body>
                    @forelse ($rows as $row)
                        <tr x-data data-base='@json($row)'>
                            <td>{{ $row->level }}</td>
                            <td>
                                {{ config('everquest.classes')[$row->class] ?? 'Unknown' }}
                                <span class="text-xs opacity-60">({{ $row->class }})</span>
                            </td>
                            <td>{{ $row->hp }}</td>
                            <td>{{ $row->mana }}</td>
                            <td>{{ $row->end }}</td>
                            <td>{{ $row->hp_regen }}</td>
                            <td>{{ $row->end_regen }}</td>
                            <td>{{ $row->hp_fac }}</td>
                            <td>{{ $row->mana_fac }}</td>
                            <td>{{ $row->end_fac }}</td>
                            <td class="text-right">
                                <div class="join">
                                    <button type="button" class="join-item btn btn-sm btn-soft tooltip" data-tip="Edit"
                                        @click="$store.modalForm.openEdit(
                                            $el.closest('tr').dataset.base,
                                            '{{ route('characters.base-data.update', [$row->level, $row->class]) }}',
                                            {
                                                routeKey: 'composite',
                                                resourceName: 'Edit Character Base Data'
                                            }
                                        )">
                                        <x-ui.icon name="edit" />
                                    </button>

                                    <form method="POST"
                                        action="{{ route('characters.base-data.destroy', [$row->level, $row->class]) }}"
                                        onsubmit="return confirm('Delete base data for level {{ $row->level }}, class {{ $row->class }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="join-item btn btn-sm btn-error btn-soft tooltip"
                                            data-tip="Delete">
                                            <x-ui.icon name="delete" />
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="text-center py-6 opacity-60">
                                No base data records found.
                            </td>
                        </tr>
                    @endforelse
                </x-slot:body>
            </x-ui.table>
        </x-search-results>

        @if ($rows->hasPages())
            <div class="mt-4">{{ $rows->links() }}</div>
        @endif

        <x-modal-form>
            @include('characters.base-data.forms.form')
        </x-modal-form>
    </div>
@endsection
