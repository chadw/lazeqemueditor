@php
    $id = $attributes->get('id', $name);
    $tooltipTxt = $attributes->get('tooltip', '');
    $labelClass = $attributes->get('label-class', '');
    $wrapperClass = $attributes->get('wrapper-class', '');
    $isReadonly = $attributes->has('readonly');
    $inputAttrs = $attributes->except([
        'tooltip',
        'tooltip-neutral',
        'wrapper-class',
        'label-suffix',
        'x-bind:label-suffix',
        ':label-suffix',
    ]);

    $attrKeys = collect($attributes->getAttributes())->keys();
    $hasLabelSuffix = $attrKeys->intersect(['label-suffix','x-bind:label-suffix',':label-suffix'])->isNotEmpty();
    $xInit = $hasLabelSuffix
        ? "(() => {
                const root = \$el;
                const update = () => { labelSuffix = root.getAttribute('label-suffix') || ''; };
                update();
                const mo = new MutationObserver(update);
                mo.observe(root, { attributes: true, attributeFilter: ['label-suffix'] });
           })()"
        : '';

    $baseClasses = 'input w-full validator';

    if ($type === 'number') {
        $baseClasses .= ' text-right tabular-nums validator';
    }

    if ($isReadonly) {
        $baseClasses .= ' bg-base-200 opacity-90 italic';
    }

    $alpineErrorClass = 'x-bind:class="{\'input-error\': ($store.modalForm && $store.modalForm.errors && $store.modalForm.errors[\'' . $name . '\']) }"';
    $alpineAria = 'x-bind:aria-invalid="($store.modalForm && $store.modalForm.errors && $store.modalForm.errors[\'' . $name . '\']) ? \'true\' : \'false\'"';
@endphp

<div class="form-control w-full {{ $wrapperClass }}"
     x-data="{ labelSuffix: '' }"
     {!! $xInit ? "x-init=\"$xInit\"" : '' !!}
>
    @if ($label)
        <label for="{{ $id }}" class="label">
            <span class="label-text {{ $labelClass }}">
                {{ $label }}
                <span x-text="labelSuffix" class="text-info"></span>
                @if ($required)
                    <span class="text-error">*</span>
                @endif
            </span>
        </label>
    @endif

    @if ($tooltipTxt)
        <div class="tooltip tooltip-neutral w-full block" data-tip="{{ $tooltipTxt }}">
            <input id="{{ $id }}" name="{{ $name }}" type="{{ $type }}" autocomplete="off"
                value="{{ old($name, $value) }}" @required($required)
                {{ $inputAttrs->merge(['class' => $baseClasses]) }} {!! $alpineErrorClass !!} {!! $alpineAria !!} />
        </div>
    @else
        <input id="{{ $id }}" name="{{ $name }}" type="{{ $type }}" autocomplete="off"
            value="{{ old($name, $value) }}" @required($required)
            {{ $inputAttrs->merge(['class' => $baseClasses]) }} {!! $alpineErrorClass !!} {!! $alpineAria !!} />
    @endif

    @error($name)
        <span class="text-error text-sm mt-1">{{ $message }}</span>
    @enderror

    <template x-if="$store.modalForm && $store.modalForm.errors && $store.modalForm.errors['{{ $name }}']">
        <div class="text-error text-sm mt-1" x-cloak>
            <template x-for="msg in $store.modalForm.errors['{{ $name }}']" :key="msg">
                <div x-text="msg"></div>
            </template>
        </div>
    </template>
</div>
