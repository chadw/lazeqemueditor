<button {{ $attributes->merge(['type' => 'submit', 'class' => 'btn btn-sm btn-soft btn-primary']) }}>
    {{ $slot }}
</button>
