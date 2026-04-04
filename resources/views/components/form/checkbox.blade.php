@props([
    'name',
    'label',
    'checked' => 0,
    'inverted' => false,
    'tooltip' => null
])

@php
    $id = $attributes->get('id', $name);
    $tooltipTxt = $attributes->get('tooltip', '');
    $xModel = $attributes->get('x-model');

    $isChecked = $inverted ? ($checked == 0) : ($checked == 1);
    $trueValue = $inverted ? '0' : '1';
    $falseValue = $inverted ? '1' : '0';
@endphp

<div class="form-control">
    <div @class(['tooltip tooltip-neutral inline-block' => $tooltip]) @if($tooltip) data-tip="{{ $tooltip }}" @endif>
        <label class="label cursor-pointer gap-2">
            @if($xModel)
                <input type="checkbox" id="{{ $id }}" x-model="{{ $xModel }}"
                    {{ $attributes->merge(['class' => 'checkbox checked:checkbox-success']) }} />
                <input type="hidden" name="{{ $name }}" :value="{{ $xModel }} ? {{ $trueValue }} : {{ $falseValue }}" />
            @else
                <input type="hidden" name="{{ $name }}" value="{{ $falseValue }}">
                <input type="checkbox" name="{{ $name }}" id="{{ $id }}" value="{{ $trueValue }}"
                    @checked(old($name, $isChecked))
                    {{ $attributes->merge(['class' => 'checkbox checked:checkbox-success']) }} />
            @endif
            <span class="label-text">{{ $label }}</span>
        </label>
    </div>

    @error($name)
        <span class="text-error text-sm mt-1">{{ $message }}</span>
    @enderror
</div>