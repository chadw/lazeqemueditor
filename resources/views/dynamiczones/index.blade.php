@extends('layouts.app')
@section('title', 'Dynamic Zones')
@section('page-title', 'Dynamic Zones')

@section('content')
    <div x-data @keydown.window.escape="if(!$store.modalForm.saving) $store.modalForm.close()">

        <x-top-links>
            <x-slot name="left">
                @include('dynamiczones.partials.filters')
            </x-slot>
            {{-- <button type="button" class="btn btn-soft btn-success float-end"
                @click="$store.modalForm.openCreate({
                    baseUrl: '{{ route('dynamiczones.store') }}',
                    resourceName: 'Dynamic Zone'
                })">
                <x-ui.icon name="add" /> New Dynamic Zone
            </button> --}}
        </x-top-links>

        <x-search-results :items="$dzs" title="Dynamic Zones">
            <x-ui.table :tbody-attributes="['x-data' => '{ openRows: {} }']">
                <x-slot:head>
                    <tr>
                        <th scope="col">@sortablelink('name', 'Name')</th>
                        <th scope="col" class="w-[20%]">@sortablelink('leader_name', 'Leader')</th>
                        <th scope="col" class="w-[10%]">Total Chars</th>
                        <th scope="col" class="w-[20%]">Type</th>
                        <th scope="col" class="w-[10%]">Instance ID</th>
                        <th scope="col" class="w-[10%] text-right">-</th>
                    </tr>
                </x-slot:head>
                <x-slot:body>
                    @forelse ($dzs as $dz)
                        <tr class="{{ $loop->odd ? 'bg-base-200' : '' }}" data-dz='@json($dz)'>
                            <td>{{ $dz->name }}</td>
                            <td>
                                <a href="{{ route('characters.show', $dz->leader->id) }}"
                                    class="text-base link-accent link-hover">
                                    {{ $dz->leader->name ?? '-' }}
                                </a>
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-soft"
                                    @click="openRows['dz-{{ $dz->id }}'] = !openRows['dz-{{ $dz->id }}']"
                                    :aria-expanded="openRows['dz-{{ $dz->id }}'] ? 'true' : 'false'"
                                >
                                    <span class="font-medium">{{ $dz->members_count }}</span>
                                    <span class="ml-2 text-sm opacity-60"
                                        x-text="openRows['dz-{{ $dz->id }}'] ? 'Hide' : 'Members'"></span>
                                </button>
                            </td>
                            <td>{{ config('everquest.dynamic_zone_type')[$dz->type] ?? 'Unknown' }}</td>
                            <td>{{ $dz->instance_id ?? '-' }}</td>
                            <td class="text-right">
                                <div class="inline join">
                                    <button type="button" class="join-item btn btn-sm btn-soft tooltip"
                                        data-tip="Edit"
                                        @click="$store.modalForm.openEdit(
                                            $el.closest('tr').dataset.dz,
                                            '{{ route('dynamiczones.update', $dz) }}',
                                            { resourceName: 'Edit Dynamic Zone' }
                                        )">
                                        <x-ui.icon name="edit" />
                                    </button>
                                    <form action="{{ route('dynamiczones.destroy', $dz) }}" method="POST"
                                        class="inline">
                                        @csrf @method('DELETE')
                                        <button class="join-item btn btn-sm btn-soft btn-error"
                                            data-tip="Delete"
                                            onclick="return confirm('Delete?')">
                                            <x-ui.icon name="delete" />
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>

                        <tr x-show="openRows['dz-{{ $dz->id }}']" x-cloak class="{{ $loop->odd ? 'bg-base-200' : '' }}">
                            <td colspan="6" class="p-4">
                                <div class="flex flex-wrap items-center gap-2 text-sm">
                                    @if(optional($dz->members) && $dz->members->isNotEmpty())
                                        @foreach($dz->members as $member)
                                            <a href="{{ route('characters.show', $member->character_id) }}"
                                                class="inline-flex items-center gap-2 px-2 py-1 btn btn-xs btn-soft rounded-md transition max-w-[min(100%,18rem)] truncate">
                                                <span class="font-medium truncate">{{ optional($member->character)->name ?? 'Unknown' }}</span>
                                                <span class="badge badge-sm badge-soft badge-info">#{{ $member->character_id }}</span>
                                            </a>
                                        @endforeach
                                    @else
                                        <div class="italic opacity-60">No members for this Dynamic Zone.</div>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center italic opacity-60">No Dynamic Zones found.</td>
                        </tr>
                    @endforelse
                </x-slot:body>
            </x-ui.table>
        </x-search-results>

        <div class="mt-4">{{ $dzs->links() }}</div>

        <x-modal-form>
            @include('dynamiczones.forms.form')
        </x-modal-form>
    </div>
@endsection
