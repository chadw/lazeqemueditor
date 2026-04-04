@extends('layouts.app')
@section('title', "Edit Item: {$item->Name}")
@section('page-title', "Edit Item: {$item->Name}")

@section('content')
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="col-span-4">
            <x-top-links>
                <x-slot name="left">
                    @include('items.partials.filters')
                </x-slot>
                <a href="{{ route('items.evolving-items.index') }}" class="btn btn-soft btn-accent">
                    Evolving Item Details
                </a>
                <form method="POST" action="{{ route('items.clone', $item) }}" class="inline-block">
                    @csrf
                    <input type="hidden" name="redirect" value="edit" />
                    <button type="submit" class="btn btn-soft btn-info tooltip" data-tip="Clone">
                        <x-ui.icon name="clone" /> Clone
                    </button>
                </form>
                <form action="{{ route('items.destroy', $item) }}" method="POST"
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
                                    <a href="{{ route('items.edit', $c['id']) }}" class="btn btn-soft">Open Existing</a>
                                    <button type="button" class="btn btn-soft btn-error" @click="open=false">Cancel</button>
                                    <button type="button" class="btn btn-soft btn-success" @click.prevent="(function(){
                                        let f = document.querySelector('form[action="{{ route('items.update', $item) }}"]');
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
        <div class="md:col-span-3" x-data="formTracker">
            <form id="item-edit-form" method="POST" action="{{ route('items.update', $item) }}">
                @csrf
                @method('PUT')

                @include('items.partials.tabs', ['item' => $item])

                <div class="mt-6 flex justify-end gap-2">
                    <button type="submit" class="btn btn-soft btn-success">
                        Save Item
                    </button>
                </div>
            </form>
        </div>

        <div class="md:col-span-1">
            @include('items.partials.preview', ['item' => $item])
        </div>
    </div>

    @include('partials.modal-idpicker')
    @include('items.partials.modal-icons')
    @include('items.partials.modal-evolving-picker')
    @include('partials.modal-objects')

@endsection
