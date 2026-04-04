@extends('layouts.app')
@section('title', 'Dynamic Zone Lockouts')
@section('page-title', 'Dynamic Zone Lockouts')

@section('content')
    <div
        x-data="{
            selected: [],
            allIds: {{ $lockouts->pluck('id') }},
            toggleAll() {
                this.selected = this.selected.length === this.allIds.length ? [] : [...this.allIds];
            }
        }"
        @keydown.window.escape="if(!$store.modalForm.saving) $store.modalForm.close()"
    >
        <x-top-links>
            <x-slot name="left">
                <div x-show="selected.length > 0" class="inline-block mr-2" x-cloak>
                    <form action="{{ route('dynamiczones.character-lockouts.bulk-destroy') }}" method="POST"
                        @submit.prevent="if(confirm('Delete ' + selected.length + ' items?')) $el.submit()">
                        @csrf @method('DELETE')
                        <template x-for="id in selected" :key="id">
                            <input type="hidden" name="ids[]" :value="id">
                        </template>
                        <button class="btn btn-error btn-soft">
                            <x-ui.icon name="delete" /> Delete Selected (<span x-text="selected.length"></span>)
                        </button>
                    </form>
                </div>
                @include('dynamiczones.character-lockouts.partials.filters')
            </x-slot>
            <button type="button" class="btn btn-soft btn-success"
                @click="$store.modalForm.openCreate({
                    baseUrl: '{{ route('dynamiczones.character-lockouts.store') }}',
                    resourceName: 'Character DZ Lockout'
                })">
                <x-ui.icon name="add" /> New Character DZ Lockout
            </button>
        </x-top-links>

        <x-search-results :items="$lockouts" title="Character Lockouts">
            <x-ui.table>
                <x-slot:head>
                    <tr>
                        <th scope="col" class="w-10">
                            <input type="checkbox" class="checkbox checkbox-sm" @click="toggleAll"
                                :checked="selected.length === allIds.length && allIds.length > 0">
                        </th>
                        <th scope="col">@sortablelink('character_name', 'Character')</th>
                        <th scope="col">@sortablelink('expedition_name', 'Expedition')</th>
                        <th scope="col" class="w-[30%]">@sortablelink('event_name', 'Event')</th>
                        <th scope="col" class="w-[15%]">@sortablelink('expire_time', 'Expires')</th>
                        <th scope="col" class="w-[10%] text-right">-</th>
                    </tr>
                </x-slot:head>
                <x-slot:body>
                    @forelse ($lockouts as $lockout)
                        @php
                            $lockout->_expires = Str::replace(' ', 'T', $lockout->expire_time);
                        @endphp
                        <tr x-data data-lockout='@json($lockout)'>
                            <td>
                                <input
                                    type="checkbox"
                                    value="{{ $lockout->id }}"
                                    x-model.number="selected"
                                    class="checkbox checkbox-sm"
                                >
                            </td>
                            <td>
                                {{ $lockout->character->name }}
                                <span class="badge badge-sm badge-soft ml-1">
                                    {{ $lockout->character_id }}
                                </span>
                            </td>
                            <td>
                                {{ $lockout->expedition_name }}
                                @if($lockout->from_expedition_uuid)
                                    <div class="text-sm text-gray-500 mt-1">{{ $lockout->from_expedition_uuid }}</div>
                                @endif
                            </td>
                            <td>{{ $lockout->event_name }}</td>
                            <td>{{ $lockout->expire_time->format('M d, Y H:i A') }}</td>
                            <td class="text-right">
                                <div class="join">
                                    <button type="button" class="join-item btn btn-sm btn-soft tooltip"
                                        data-tip="Edit"
                                        @click="$store.modalForm.openEdit(
                                            $el.closest('tr').dataset.lockout,
                                            '{{ route('dynamiczones.character-lockouts.update', $lockout) }}',
                                            { resourceName: 'Edit Character DZ Lockout' }
                                        )">
                                        <x-ui.icon name="edit" />
                                    </button>
                                    <form action="{{ route('dynamiczones.character-lockouts.destroy', $lockout) }}"
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
                            <td colspan="6" class="text-center italic opacity-60">
                                No Dynamic Zone Character Lockouts found.
                            </td>
                        </tr>
                    @endforelse
                </x-slot:body>
            </x-ui.table>
        </x-search-results>

        <div class="mt-4">{{ $lockouts->links() }}</div>

        <x-modal-form>
            @include('dynamiczones.character-lockouts.forms.form')
        </x-modal-form>
    </div>
@endsection
