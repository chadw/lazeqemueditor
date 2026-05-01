@extends('layouts.app')
@section('title', isset($npc) && $npc->name ? 'NPCs - ' . $npc->clean_name : 'NPCs')
@section('page-title', isset($npc) && $npc->name ? 'NPCs - ' . $npc->clean_name : 'NPCs')

@section('content')
    @if ($npc)
        <div x-data class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="col-span-4">
                <x-top-links>
                    <x-slot name="left">
                        @include('npcs.partials.filters')
                    </x-slot>
                    <form method="POST" action="{{ route('npcs.clone', $npc) }}" class="inline-block">
                        @csrf
                        <input type="hidden" name="redirect" value="edit" />
                        <button type="submit" class="btn btn-soft btn-info tooltip" data-tip="Clone">
                            <x-ui.icon name="clone" /> Clone
                        </button>
                    </form>
                    <form action="{{ route('npcs.destroy', $npc) }}" method="POST"
                        class="inline">
                        @csrf @method('DELETE')
                        <button class="join-item btn btn-soft btn-error tooltip" data-tip="Delete"
                            onclick="return confirm('Delete?')">
                            <x-ui.icon name="delete" /> Delete
                        </button>
                    </form>
                </x-top-links>
                @if(session('id_conflict'))
                    @php $c = session('id_conflict'); @endphp
                    <div x-data="{ open: true }" x-cloak>
                        <div x-show="open" class="fixed inset-0 z-50 flex items-center justify-center p-6" style="background: rgba(0,0,0,.5);">
                            <div class="bg-base-100 w-full max-w-lg rounded shadow-lg overflow-hidden">
                                <div class="p-4">
                                    <h3 class="font-semibold">ID Conflict</h3>
                                    <p class="text-sm mt-2">The ID <strong>{{ $c['id'] }}</strong> is already in use by <strong>{{ $c['name'] }}</strong>.</p>
                                    <p class="text-sm mt-2">You can open the existing spell to inspect it, or confirm to overwrite it with your changes.</p>
                                    <div class="mt-4 flex justify-end gap-2">
                                        <a href="{{ request()->fullUrlWithQuery(['npc' => $c['id']]) }}" class="btn btn-soft">Open Existing</a>
                                        <button type="button" class="btn btn-soft btn-error" @click="open=false">Cancel</button>
                                        <button type="button" class="btn btn-soft btn-success" @click.prevent="(function(){
                                            let f = document.querySelector('form[action="{{ route('npcs.update', $npc) }}"]');
                                            if (!f) f = document.querySelector('form');
                                            if (f) {
                                                let inpt = document.createElement('input');
                                                inpt.type = 'hidden'; inpt.name = 'confirm_id_replace'; inpt.value = '1';
                                                f.appendChild(inpt);
                                                f.submit();
                                            }
                                        })()">Confirm Overwrite</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
            <div class="col-span-4 xl:col-span-3">
                @include('npcs.partials.tabs')
            </div>
            <div class="col-span-4 xl:col-span-1">
                @include('npcs.partials.preview', ['npc' => $npc])
            </div>

            <x-modal-form x-show="$store.modalForm.isOpen">
                <template x-if="$store.modalForm.activeModal === 'spawn-group'">
                    @include('npcs.forms.spawngroup')
                </template>
                <template x-if="$store.modalForm.activeModal === 'spawn-entry'">
                    @include('npcs.forms.spawnentry')
                </template>
                <template x-if="$store.modalForm.activeModal === 'spawn-point'">
                    @include('npcs.forms.spawnpoint')
                </template>
                <template x-if="$store.modalForm.activeModal === 'primary-faction'">
                    @include('npcs.forms.primary-faction')
                </template>
                <template x-if="$store.modalForm.activeModal === 'loottable-entry'">
                    @include('loot.forms.loottable-entry-form')
                </template>
                <template x-if="$store.modalForm.activeModal === 'lootdrop'">
                    @include('loot.forms.lootdrop-form')
                </template>
                <template x-if="$store.modalForm.activeModal === 'lootdrop-items'">
                    @include('loot.forms.lootdrop-item-form')
                </template>
                <template x-if="$store.modalForm.activeModal === 'new-set'">
                    @include('npc-spells.forms.new-spellset')
                </template>
                <template x-if="$store.modalForm.activeModal === 'main-set'">
                    @include('npc-spells.forms.form-entry')
                </template>
                {{-- <template x-if="$store.modalForm.activeModal === 'parent-set'">
                    @include('npc-spells.forms.form-entry')
                </template> --}}
            </x-modal-form>
        </div>

        @include('partials.modal-idpicker')
        @include('partials.modal-objects')
        @include('npcs.partials.race-model-picker')

    @endif
@endsection
