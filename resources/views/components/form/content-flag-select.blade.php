<div class="form-control">
    @if(!empty($label))
        <label class="label"><span class="label-text">{{ $label }}</span></label>
    @endif

    <select {{ $attributes->merge(['name' => $name ?? 'content_flags', 'class' => 'select w-full']) }}>
        <option value="">None</option>
        @foreach($content_flags as $flag)
            <option value="{{ $flag->flag_name }}" @selected(old($name, $selected ?? null) == $flag->flag_name)>
                {{ $flag->flag_name ?? $flag->name ?? $flag->label ?? $flag->id }}
            </option>
        @endforeach
    </select>
</div>
