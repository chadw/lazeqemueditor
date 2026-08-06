@php
    $id = $attributes->get('id', $name);
    $tooltipTxt = $attributes->get('tooltip', '');
    $helpTxt = $attributes->get('help', '');
    $wrapperClass = $attributes->get('wrapper-class', '');
    $rows = $attributes->get('rows', 4);
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
        <div class="tooltip tooltip-neutral w-full block mt-1" data-tip="{{ $tooltipTxt }}">
            <textarea id="{{ $id }}" name="{{ $name }}"
                {{ $attributes->except(['tooltip', 'tooltip-neutral', 'help'])->merge(['class' => 'textarea w-full']) }}
            >{{ old($name, $value) }}</textarea>
        </div>
    @else
        <textarea
            id="{{ $id }}"
            name="{{ $name }}"
            {{ $attributes->except(['help'])->merge(['class' => 'textarea w-full']) }}
        >{{ old($name, $value) }}</textarea>
    @endif

    @error($name)
        <span class="text-error text-sm mt-1">{{ $message }}</span>
    @enderror
</div>
