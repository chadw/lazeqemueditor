@props([
    'value' => null,
    'platinum' => null,
    'gold' => null,
    'silver' => null,
    'copper' => null,
])

@php
    if (!is_null($value)) {
        $copper = (int) $value;

        $copper = max(0, (int) $copper);
        $p = floor($copper / 1000);
        $g = floor(($copper % 1000) / 100);
        $s = floor(($copper % 100) / 10);
        $c = floor($copper % 10);
    } else {
        $p = $platinum ?? 0;
        $g = $gold ?? 0;
        $s = $silver ?? 0;
        $c = $copper ?? 0;
    }
@endphp

<div {{ $attributes->merge(['class' => 'flex items-center gap-3']) }}>
    @if ($p > 0)
        <div class="flex items-center gap-1">
            <div class="w-3 h-3 rounded-full bg-slate-300 shadow-[inset_0_1px_rgba(255,255,255,0.5)] border border-slate-400"
                title="Platinum"></div>
            <span class="font-bold">{{ number_format($p, 0) }}</span>
        </div>
    @endif

    @if ($g > 0)
        <div class="flex items-center gap-1">
            <div class="w-3 h-3 rounded-full bg-yellow-400 shadow-[inset_0_1px_rgba(255,255,255,0.5)] border border-yellow-600"
                title="Gold"></div>
            <span class="font-bold">{{ number_format($g, 0) }}</span>
        </div>
    @endif

    @if ($s > 0)
        <div class="flex items-center gap-1">
            <div class="w-3 h-3 rounded-full bg-gray-400 shadow-[inset_0_1px_rgba(255,255,255,0.5)] border border-gray-500"
                title="Silver"></div>
            <span class="font-bold">{{ number_format($s, 0) }}</span>
        </div>
    @endif

    @if ($c > 0 || ($p == 0 && $g == 0 && $s == 0))
        <div class="flex items-center gap-1">
            <div class="w-3 h-3 rounded-full bg-orange-500 shadow-[inset_0_1px_rgba(255,255,255,0.5)] border border-orange-700"
                title="Copper"></div>
            <span class="font-bold">{{ number_format($c, 0) }}</span>
        </div>
    @endif
</div>
