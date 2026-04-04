@props([
    'xData' => null,
])

<div {{ $attributes->merge([
        'class' => 'card bg-base-100 shadow-sm border border-base-100 overflow-hidden'
    ]) }}
    @if($xData)
        x-data="{{ $xData }}"
    @endif
>
    @isset($header)
        <div class="bg-neutral text-neutral-content px-4 py-3 flex flex-wrap justify-between items-center gap-4 border-b border-base-300">
            {{ $header }}
        </div>
    @endisset

    <div>
        {{ $slot }}
    </div>

    @isset($footer)
        <div class="card-actions justify-between p-2 bg-base-300/20 border-t border-base-300">
            {{ $footer }}
        </div>
    @endisset
</div>
