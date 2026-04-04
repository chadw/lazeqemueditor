@extends('layouts.app')
@section('title', 'Dynamic Zone Lockouts')
@section('page-title', 'Dynamic Zone Lockouts')

@section('content')
    <div x-data @keydown.window.escape="if(!$store.modalForm.saving) $store.modalForm.close()">

        <x-top-links>
            <x-slot name="left">
                @include('dynamiczones.lockouts.partials.filters')
            </x-slot>
            <button type="button" class="btn btn-soft btn-success float-end"
                @click="$store.modalForm.openCreate({
                    baseUrl: '{{ route('dynamiczones.lockouts.store') }}',
                    resourceName: 'DZ Lockout'
                })">
                <x-ui.icon name="add" /> New DZ Lockout
            </button>
        </x-top-links>

        <x-search-results :items="$lockouts" title="DZ Lockouts">
            <x-ui.table :tbody-attributes="['x-data' => '{ openRows: {} }']">
                <x-slot:head>
                    <tr>
                        <th scope="col">@sortablelink('dynamic_zone_name', 'DZ')</th>
                        <th scope="col" class="w-[10%]">Total Chars</th>
                        <th scope="col" class="w-[30%]">@sortablelink('event_name', 'Event')</th>
                        <th scope="col" class="w-[15%]">@sortablelink('expire_time', 'Expires')</th>
                        <th scope="col" class="w-[10%]">Duration</th>
                        <th scope="col" class="w-[10%] text-right">-</th>
                    </tr>
                </x-slot:head>
                <x-slot:body>
                    @forelse ($lockouts as $lockout)
                        @php
                            $lockout->_expires = Str::replace(' ', 'T', $lockout->expire_time);
                        @endphp
                        <tr class="{{ $loop->odd ? 'bg-base-200' : '' }}" data-lockout='@json($lockout)'>
                            <td>
                                {{ $lockout->dz?->name ?? 'Unknown' }}
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-soft"
                                    @click="openRows['dz-{{ $lockout->id }}'] = !openRows['dz-{{ $lockout->id }}']"
                                    :aria-expanded="openRows['dz-{{ $lockout->id }}'] ? 'true' : 'false'"
                                >
                                    <span class="font-medium">{{ $lockout->dz?->members_count ?? 0 }}</span>
                                    <span class="ml-2 text-sm opacity-60"
                                        x-text="openRows['dz-{{ $lockout->id }}'] ? 'Hide' : 'Members'"></span>
                                </button>
                            </td>
                            <td>{{ $lockout->event_name }}</td>
                            <td>{{ $lockout->expire_time->format('M d, Y H:i A') }}</td>
                            <td>{{ $lockout->clean_duration->forHumans(['short' => true]) }}</td>
                            <td class="text-right">
                                <div class="join">
                                    <button type="button" class="join-item btn btn-sm btn-soft tooltip"
                                        data-tip="Edit"
                                        @click="$store.modalForm.openEdit(
                                            $el.closest('tr').dataset.lockout,
                                            '{{ route('dynamiczones.lockouts.update', $lockout) }}',
                                            { resourceName: 'Edit DZ Lockout' }
                                        )">
                                        <x-ui.icon name="edit" />
                                    </button>
                                    <form action="{{ route('dynamiczones.lockouts.destroy', $lockout) }}"
                                        method="POST" class="inline">
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

                        <tr x-show="openRows['dz-{{ $lockout->id }}']" x-cloak class="{{ $loop->odd ? 'bg-base-200' : '' }}">
                            <td colspan="6" class="p-4">
                                <div class="flex flex-wrap items-center gap-2 text-sm">
                                    @if(optional($lockout->dz)->members && $lockout->dz->members->isNotEmpty())
                                        @foreach($lockout->dz->members as $member)
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
                            <td colspan="6" class="text-center italic opacity-60">No Dynamic Zone Lockouts found.</td>
                        </tr>
                    @endforelse
                </x-slot:body>
            </x-ui.table>
        </x-search-results>

        <div class="mt-4">{{ $lockouts->links() }}</div>

        <x-modal-form>
            @include('dynamiczones.lockouts.forms.form')
        </x-modal-form>
    </div>
@endsection
