<div {{ $attributes->merge(['class' => 'card bg-base-200 card-sm shadow-sm mb-4']) }}>
    <div class="card-body">
        <div class="flex items-center justify-between w-full">
            <div class="flex items-center gap-2">
                {{ $left ?? '' }}
            </div>
            <div class="flex items-center gap-2">
                {{ $right ?? $slot }}
            </div>
        </div>
    </div>
</div>
