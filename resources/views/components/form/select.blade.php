@php
    $id = $attributes->get('id', $name);
    $tooltipTxt = $attributes->get('tooltip', '');
    $helpTxt = $attributes->get('help', '');
    $wrapperClass = $attributes->get('wrapper-class', '');
    $keyInOption = filter_var(
        $attributes->get('keyInOption') ?? $attributes->get('key-in-option') ?? false,
        FILTER_VALIDATE_BOOLEAN
    );
@endphp

<div class="form-control w-full {{ $wrapperClass }}">
    @if ($label)
        <label for="{{ $id }}" class="label">
            <span class="label-text inline-flex items-center gap-1">
                {{ $label }}
                @if ($helpTxt)
                    <x-ui.field-help :text="$helpTxt" />
                @endif
            </span>
        </label>
    @endif

    @if ($tooltipTxt)
        <div class="tooltip tooltip-neutral w-full block" data-tip="{{ $tooltipTxt }}">
            <select id="{{ $id }}" name="{{ $name }}"
                {{ $attributes->except(['help'])->merge(['class' => 'select w-full']) }}>
                @foreach ($options as $value => $text)
                    <option value="{{ $value }}" @selected(old($name, $selected) == $value)>
                        @if($keyInOption) {{ $value }}: @endif {{ $text }}
                    </option>
                @endforeach
            </select>
        </div>
    @else
        <select id="{{ $id }}" name="{{ $name }}"
            {{ $attributes->except(['help'])->merge(['class' => 'select w-full']) }}>
            @foreach ($options as $value => $text)
                <option value="{{ $value }}" @selected(old($name, $selected) == $value)>
                    @if($keyInOption) {{ $value }}: @endif {{ $text }}
                </option>
            @endforeach
        </select>
    @endif
    @error($name)
        <span class="text-error text-sm mt-1">{{ $message }}</span>
    @enderror
</div>
