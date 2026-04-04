@php
    $id = $attributes->get('id', $name);
    $tooltipTxt = $attributes->get('tooltip', '');
    $xModel = $attributes->get('x-model');
@endphp

<div class="form-control">
    @if ($tooltipTxt)
        <div class="tooltip tooltip-neutral inline-block" data-tip="{{ $tooltipTxt }}">
            <label class="label cursor-pointer gap-2">
                @if($xModel)
                    <input
                        type="checkbox"
                        id="{{ $id }}"
                        x-model="{{ $xModel }}"
                        {{ $attributes->except(['tooltip', 'tooltip-neutral', 'x-model'])
                            ->merge(['class' => 'checkbox checked:checkbox-success']) }}
                    />
                    <input
                        type="hidden"
                        name="{{ $name }}"
                        :value="{{ $xModel }} ? 1 : 0"
                    />
                @else
                    <input type="hidden" name="{{ $name }}" value="0">
                    <input
                        type="checkbox"
                        name="{{ $name }}"
                        id="{{ $id }}"
                        value="1"
                        @checked(old($name, $checked))
                        {{ $attributes->except(['tooltip', 'tooltip-neutral'])
                            ->merge(['class' => 'checkbox checked:checkbox-success']) }}
                    />
                @endif
                <span class="label-text">{{ $label }}</span>
            </label>
        </div>
    @else
        <label class="label cursor-pointer gap-2">
            @if($xModel)
                <input
                    type="checkbox"
                    id="{{ $id }}"
                    x-model="{{ $xModel }}"
                    {{ $attributes->except(['x-model'])
                        ->merge(['class' => 'checkbox checked:checkbox-success']) }}
                />
                <input
                    type="hidden"
                    name="{{ $name }}"
                    :value="{{ $xModel }} ? 1 : 0"
                />
            @else
                <input type="hidden" name="{{ $name }}" value="0">
                <input
                    type="checkbox"
                    name="{{ $name }}"
                    id="{{ $id }}"
                    value="1"
                    @checked(old($name, $checked))
                    {{ $attributes->merge(['class' => 'checkbox checked:checkbox-success']) }}
                />
            @endif
            <span class="label-text">{{ $label }}</span>
        </label>
    @endif

    @error($name)
        <span class="text-error text-sm mt-1">{{ $message }}</span>
    @enderror
</div>
