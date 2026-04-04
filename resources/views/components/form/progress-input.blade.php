@php
    $id = $attributes->get('id', $name);
    $tooltipTxt = $attributes->get('tooltip', '');
    $required = $attributes->has('required');
    $class = $attributes->get('class', '');
    $inputClass = trim('input w-36 text-right tabular-nums ' . $class);

    // preserve numeric precision but only allow decimals when explicitly requested
    $raw = (float) old($name, $value ?? $attributes->get('value', 0));

    $unit = strtolower($attributes->get('unit', 'ms'));
    $isSeconds = in_array($unit, ['s', 'sec', 'secs', 'second', 'seconds']);
    // pass the raw value (seconds or milliseconds) to Alpine — Alpine will handle
    // converting seconds -> ms internally. Avoid double-multiplying here.
    $initial = $raw;

    // decimals control: allow decimals only if `allow-decimals` attribute is present
    // or a `decimals` count is provided. Default is no decimals.
    $allowDecimals = $attributes->has('allow-decimals') || $attributes->has('allow_decimals');
    $decimals = $attributes->get('decimals') !== null ? (int) $attributes->get('decimals') : ($allowDecimals ? 2 : 0);

    if ($decimals > 0) {
        $inputStep = '0.' . str_repeat('0', max(0, $decimals - 1)) . '1';
    } else {
        $inputStep = '1';
    }
@endphp

<div class="form-control w-full">
    @if ($label)
        <label for="{{ $id }}" class="label">
            <span class="label-text">{{ $label }}</span>
        </label>
    @endif

    <div
        x-data="progressInput({{ json_encode($initial) }}, '{{ $isSeconds ? 's' : 'ms' }}', {{ $decimals > 0 ? 'true' : 'false' }})"
        class="w-full block"
    >
        <div class="flex items-center gap-3">
            <input
                id="{{ $id }}"
                name="{{ $name }}"
                type="number"
                inputmode="numeric"
                x-model.number="display"
                min="0"
                step="{{ $inputStep }}"
                {{ $attributes->except(['value','tooltip','class'])->merge(['class' => $inputClass]) }}
                @input="
                    let val = Number($event.target.value);
                    if (!isFinite(val)) val = 0;
                    val = Math.max(0, val);
                    @if($decimals > 0)
                        const _f = Math.pow(10, {{ $decimals }});
                        display = Math.round(val * _f) / _f;
                    @else
                        display = Math.round(val);
                    @endif
                "
            />

            <div class="flex-1">
                <div class="relative w-full h-6 bg-neutral/40 rounded overflow-hidden">
                    <div
                        x-ref="fill"
                        class="progress-fill bg-accent"
                        aria-hidden="true"
                    ></div>
                    <div class="absolute inset-0 flex items-center justify-center pointer-events-none text-sm font-medium text-accent-content progress-overlay z-10">
                        <span x-text="secondsMode ? Math.round(valMs / 1000) + 's' : Math.round(valMs) + 'ms'"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @error($name)
        <span class="text-error text-sm mt-1">{{ $message }}</span>
    @enderror
</div>
