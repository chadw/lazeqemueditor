@php
    $classes = collect(config('everquest.classes_bit') ?? [])->sort()->toArray();
@endphp

<form method="GET" action="{{ route('aa.index') }}" class="flex gap-2 items-end">
    <div class="w-72">
        <label class="label label-text">Ability</label>
        <select id="ability-filter" name="ability" class="w-full"
            onchange="if (this.value) { window.location.href = '{{ url('/aa') }}/' + this.value + '/edit'; }"
        >
            <option value="">Any</option>
            @foreach($allAbilities as $a)
                <option value="{{ $a->id }}"
                    @selected(
                        (string) request('ability') === (string) $a->id
                        || (!request()->has('ability') && isset($ability) && (string) ($ability->id ?? '') === (string) $a->id)
                    )
                >
                    {{ $a->id }}: {{ $a->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="w-48">
        <label class="label label-text">Classes</label>
        @php $selectedClass = (string) request('classes', ''); @endphp
        <select name="classes" class="select w-full" onchange="this.form.submit()">
            <option value="" @selected($selectedClass === '')>Any</option>
            @foreach($classes as $bit => $label)
                <option value="{{ $bit }}" @selected((string)$bit === $selectedClass)>
                    {{ $label }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="flex gap-2">
        <button type="submit" class="btn btn-soft btn-success">Filter</button>

        @if (request()->hasAny(['ability', 'classes']))
            <a href="{{ url()->current() }}" class="btn btn-soft btn-error">Reset</a>
        @endif
    </div>
</form>
