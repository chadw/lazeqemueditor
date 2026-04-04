<th scope="col" {{ $attributes->merge(['class' => $class]) }}>
    <a href="{{ $url() }}" class="link-accent link-hover">
        {{ $label }}
        @if ($isActive())
            @if ($direction() === 'asc')
                <i aria-hidden="true" class="sort-ascending"></i>
            @else
                <i aria-hidden="true" class="sort-descending"></i>
            @endif
        @endif
    </a>
</th>
