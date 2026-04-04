@props([
    'height' => '',
    'theadsticky' => '-top-6 z-10',
    'tbodyAttributes' => [],
])

<div class="card bg-base-100 shadow">
    <div class="border border-base-content/5 {{ $height }}">
        <table class="table table-auto table-zebra md:table-fixed w-full">
            <thead class="text-xs uppercase bg-neutral sticky {{ $theadsticky }}">
                {{ $head }}
            </thead>
            <tbody {{ $attributes->merge($tbodyAttributes) }}>
                {{ $body }}
            </tbody>
        </table>
    </div>
</div>
