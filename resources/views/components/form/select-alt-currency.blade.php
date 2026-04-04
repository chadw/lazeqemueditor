@props([
    'label' => null,
    'name' => null,
    'options' => [],
    'selected' => null,
    'placeholder' => null,
    'keyInOption' => false,
    'wrapperClass' => '',
])
<div
    x-data="{
        value: {{ $selected !== null ? json_encode((string) $selected) : 'null' }},
        tom: null
    }"
    x-init="
        tom = new TomSelect($refs.select, {
            create: false,
            maxItems: 1,
            maxOptions: 100,
            placeholder: '{{ $placeholder }}',
            valueField: 'value',
            labelField: 'text',
            searchField: ['text'],
            hidePlaceholder: true,
            controlInput: null,

            render: {
                option(data, escape) {
                    const iconClass = data.icon
                        ? `item-${escape(data.icon)}`
                        : 'item-0';

                    return `
                        <div class='flex items-center gap-2 px-2 py-1'>
                            <span class='icon-wrap' aria-hidden='true'>
                                <span class='item-icon ${iconClass} item-icon-sm'></span>
                            </span>
                            <span class='text-sm whitespace-nowrap'>
                                ${escape(data.text)}
                            </span>
                        </div>
                    `;
                },

                item(data, escape) {
                    const iconClass = data.icon
                        ? `item-${escape(data.icon)}`
                        : 'item-0';

                    return `
                        <div class='ts-item-flex flex items-center gap-2'>
                            <span class='icon-wrap' aria-hidden='true'>
                                <span class='item-icon ${iconClass} item-icon-sm'></span>
                            </span>
                            <span class='text-sm whitespace-nowrap'>
                                ${escape(data.text)}
                            </span>
                        </div>
                    `;
                }
            }
        });

        if (value !== null) {
            tom.setValue(value, true);
        }

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
        name="{{ $name }}"
        class="w-full"
    >
        <option value="0" data-icon="4495">
            None
        </option>

        @foreach ($options as $key => $opt)
            <option
                value="{{ (string) $key }}"
                data-icon="{{ $opt['icon'] ?? '' }}"
            >
                {{ $opt['name'] }}
            </option>
        @endforeach
    </select>
</div>
