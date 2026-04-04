@props([
    'placeholder' => null,
    'keyInOption' => false,
    'wrapperClass' => '',
])
<div
    x-data="{
        value: [],
        tom: null
    }"
    x-init="
        tom = new TomSelect($refs.select, {
            plugins: ['remove_button'],
            create: false,
            maxOptions: 2000,
            placeholder: '{{ $placeholder }}',
        });

        // x-model → Tom Select
        $watch('value', v => {
            tom.setValue(v || [], true);
        });
    "
    x-modelable="value"
    {{ $attributes->whereStartsWith('x-model') }}
    class="{{ $wrapperClass }}"
>
    @if ($label)
        <label class="label">
            <span class="label-text">{{ $label }}</span>
        </label>
    @endif

    <select
        x-ref="select"
        name="{{ $name }}[]"
        class="w-full"
        multiple
        {{ $attributes->except(['x-model', 'x-init', 'x-data']) }}
    >
        @foreach ($options as $key => $optionLabel)
            <option value="{{ (string) $key }}">
                @if($keyInOption) {{ $key }}: @endif {{ $optionLabel }}
            </option>
        @endforeach
    </select>
</div>
