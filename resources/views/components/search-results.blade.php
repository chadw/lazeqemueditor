@props(['items', 'title' => 'Results'])

@php
    $total = 0;
    if ($items instanceof \Illuminate\Contracts\Pagination\Paginator || $items instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator) {
        $total = $items->total();
    } elseif (is_countable($items)) {
        $total = count($items);
    }
@endphp

@if($total > 0)
    <h2 class="card-title mb-4">Found {{ number_format($total) }} {{ $title }}</h2>
    {{ $slot }}
@elseif(request()->query->count() > 0)
    <div class="bg-base-100 border border-base-content/10 text-center py-6 text-base-content/50 mb-4">
        Nothing found using the search filters provided.
    </div>
@else
    {{ $slot }}
@endif
