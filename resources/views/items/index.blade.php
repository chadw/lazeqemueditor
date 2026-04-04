@extends('layouts.app')
@section('title', 'Items')
@section('page-title', 'Items')

@section('content')
    <div>
        <x-top-links>
            <x-slot name="left">
                @include('items.partials.filters')
            </x-slot>
            {{-- <a href="{{ route('items.create') }}" class="btn btn-soft btn-success">
                <x-ui.icon name="add" /> New Item
            </a> --}}
        </x-top-links>
    </div>

    @if ($items->isNotEmpty())
        <h2 class="card-title mb-4">Found {{ $items->total() }} Items</h2>
        @include('items.partials.index-results', [
            'items' => $items
        ])
    @else
        @if (request()->query->count() > 0)
            <div class="bg-base-100 border border-base-content/10 text-center py-6 text-base-content/50 mb-4">
                No items found using the search filters provided.
            </div>
        @endif

        <h2 class="card-title mb-4">Recently Updated Items</h2>
        @include('items.partials.index-results', [
            'items' => $lastUpdated,
            'title' => '',
        ])
    @endif

@endsection
