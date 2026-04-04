@php
    $types = config('everquest.aug_type_descriptions', []);
    //$cols = (int) ($cols ?? 6);
    $name = $name ?? 'augtype';
    $initial = (int) ($selected ?? $initial ?? 0);
    //$chunks = array_chunk($types, (int) ceil(count($types) / $cols), true);
@endphp

<div x-data="augTypePicker({ initial: {{ $initial }}, fieldName: '{{ $name }}' })">
    {{-- Hidden input with dynamic name/value bound to the computed bitmask --}}
    <input type="hidden" :name="fieldName" :value="value">

        @foreach($types as $k => $label)
        <div class="form-control">
            @php
                // bit value: 1 << (key-1)
                $bit = 1 << ((int)$k - 1);
            @endphp
            {{-- @if($label === '') @continue @endif --}}
            <label class="label cursor-pointer gap-2">
                <input
                    type="checkbox"
                    value="{{ $bit }}"
                    id="{{ $name }}_{{ $k }}"
                    x-model.number="checked"
                    class="checkbox"
                />
                <span class="label-text">{{ $k }} — {{ $label }}</span>
            </label>
        </div>
        @endforeach

    <div class="flex gap-2 mt-2">
        <button type="button" class="btn btn-sm" @click="setAll()">All</button>
        <button type="button" class="btn btn-sm btn-ghost" @click="setNone()">None</button>
    </div>
</div>
