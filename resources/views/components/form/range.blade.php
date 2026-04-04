@php
    $id = $attributes->get('id', $name);
    $tooltipTxt = $attributes->get('tooltip', '');
    $required = $attributes->has('required');

    // numeric attrs with sane defaults
    $min = $attributes->get('min', 0);
    $max = $attributes->get('max', 100);
    $step = $attributes->get('step', 1);

    // how many digits the small input should allow (defaults to 3)
    $digits = (int) $attributes->get('digits', 3);
    $digits = max(1, min(9, $digits)); // clamp to reasonable range
    $numberMax = (int) (min($max, pow(10, $digits) - 1));

    // show current numeric value next to slider
    $showValue = filter_var($attributes->get('show-value') ?? $attributes->get('showValue') ?? false, FILTER_VALIDATE_BOOLEAN);

    // initial value (old() fallback to passed value)
    $initial = old($name, $value ?? $attributes->get('value', $min));
@endphp

<div class="form-control w-full">
    @if ($label)
        <label for="{{ $id }}" class="label">
            <span class="label-text">
                {{ $label }}
                @if ($required)
                    <span class="text-error">*</span>
                @endif
            </span>
        </label>
    @endif

    <div
        x-data="{ val: {{ json_encode($initial) }} }"
        class="w-full block mt-1 tooltip"
        data-tip="{{ $tooltipTxt }}"
    >
        <div class="flex items-center gap-3">
            <input
                type="number"
                inputmode="numeric"
                class="input w-20 text-right"
                x-model.number="val"
                :min="{{ $min }}"
                :max="{{ $max > $numberMax ? $numberMax : $max }}"
                :step="{{ $step }}"
                placeholder="—"
                @input="val = Math.max({{ $min }}, Math.min({{ $numberMax }}, isNaN(Number($event.target.value)) ? {{ $min }} : Number($event.target.value)))"
                @blur="val = Math.max({{ $min }}, Math.min({{ $numberMax }}, Number(val) || {{ $min }}))"
                {{ $attributes->except(['tooltip','min','max','step','show-value','showValue','value','digits'])->merge([]) }}
            />
            <input
                id="{{ $id }}"
                name="{{ $name }}"
                type="range"
                x-model.number="val"
                min="{{ $min }}"
                max="{{ $max }}"
                step="{{ $step }}"
                class="range range-accent flex-1"
                {{ $attributes->except(['tooltip','min','max','step','show-value','showValue','value','digits'])->merge([]) }}
            />
            @if($showValue)
                <div class="w-16 text-right text-sm text-gray-600" x-text="val"></div>
            @endif
        </div>
    </div>

    @error($name)
        <span class="text-error text-sm mt-1">{{ $message }}</span>
    @enderror
</div>
