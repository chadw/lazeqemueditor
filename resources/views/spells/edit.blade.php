@extends('layouts.app')

@section('title', "Edit Spell: {$spell->name}")
@section('page-title', "Edit Spell: {$spell->name}")

@section('content')
    <div class="grid grid-cols-4 gap-6">
        <div class="col-span-4">
            <x-top-links>
                <form method="POST" action="{{ route('spells.clone', $spell) }}" class="inline-block">
                    @csrf
                    <input type="hidden" name="redirect" value="edit" />
                    <button type="submit" class="btn btn-soft btn-info tooltip" data-tip="Clone">
                        <x-ui.icon name="clone" />
                    </button>
                </form>
                <form action="{{ route('spells.destroy', $spell) }}" method="POST"
                    class="inline">
                    @csrf @method('DELETE')
                    <button class="join-item btn btn-soft btn-error tooltip"
                        data-tip="Delete"
                        onclick="return confirm('Delete?')">
                        <x-ui.icon name="delete" />
                    </button>
                </form>
            </x-top-links>
            @if(session('id_conflict'))
                @php $c = session('id_conflict'); @endphp
                <div id="id-conflict-modal-wrapper">
                    <div id="id-conflict-modal" x-data x-cloak x-init="$store.idConflict.show()" x-show="$store.idConflict.open" class="fixed inset-0 z-50 flex items-center justify-center p-6" style="background: rgba(0,0,0,.5);" data-update-url="{{ route('spells.update', $spell) }}">
                        <div class="bg-base-100 w-full max-w-lg rounded shadow-lg overflow-hidden">
                            <div class="p-4">
                                <h3 class="font-semibold">ID Conflict</h3>
                                <p class="text-sm mt-2">The ID <strong>{{ $c['id'] }}</strong> is already in use by <strong>{{ $c['name'] }}</strong>.</p>
                                <p class="text-sm mt-2">You can open the existing spell to inspect it, or confirm to overwrite it with your changes.</p>
                                <div class="mt-4 flex justify-end gap-2">
                                    <a href="{{ route('spells.edit', $c['id']) }}" class="btn btn-soft">Open Existing</a>
                                    <button type="button" @click="$store.idConflict.hide()" class="btn btn-soft btn-error">Cancel</button>
                                    <button type="button" @click="$store.idConflict.confirm()" class="btn btn-soft btn-success">Confirm Overwrite</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
        <div class="col-span-3" x-data="formTracker">
            <form method="POST" id="spell-edit-form" action="{{ route('spells.update', $spell) }}">
                @csrf
                @method('PUT')
                <input type="hidden" name="id" value="{{ $spell->id }}" />
                @include('spells.partials.form', ['spell' => $spell])

                <div class="mt-6 flex justify-end gap-2">
                    <button type="submit" class="btn btn-soft btn-success">
                        Save Spell
                    </button>
                </div>
            </form>
        </div>
        {{--
        data-preview="live"
        data-preview="blur"
        data-preview="blur-post"
        --}}
        <div class="col-span-1">
            @include('spells.partials.preview', ['spell' => $spell])
        </div>
    </div>

    @include('partials.modal-idpicker')
    @include('partials.modal-dbstr-picker')
    @include('spells.partials.modal-formula')
    @include('spells.partials.modal-icons')
    @include('spells.partials.modal-animations')
@endsection
