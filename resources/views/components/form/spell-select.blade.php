@props([
    'name' => 'spell_id',
    'endpoint' => '/spells/search',
    'placeholder' => 'Search spell by ID or name…',
    'initial' => null,
])

<div
    x-data="spellSelect({
        endpoint: '{{ $endpoint }}',
        initial: @js($initial)
    })"
    x-init="
        $watch('$store.modalForm.isOpen', open => {
            if (open) init()
        })
    "
    class="relative w-full"
>
    <input
        type="text"
        class="input input-bordered w-full"
        placeholder="{{ $placeholder }}"
        x-model="search"
        @focus="open = true; fetch()"
    />
    <div
        x-show="open"
        @click.outside="open = false"
        class="absolute z-50 mt-1 w-full bg-base-100 border rounded shadow max-h-64 overflow-y-auto"
        @scroll="
            if ($el.scrollTop + $el.clientHeight >= $el.scrollHeight - 10) {
                fetch()
            }
        "
    >
        <template x-for="item in items" :key="item.id">
            <div
                class="px-3 py-2 cursor-pointer hover:bg-base-200 flex gap-2"
                @click="select(item)"
            >
                <span class="font-mono text-xs opacity-70" x-text="item.id"></span>
                <span x-text="item.name"></span>
            </div>
        </template>

        <div x-show="loading" class="p-2 text-center text-sm opacity-60">
            Loading…
        </div>
    </div>
    <input type="hidden" name="{{ $name }}" :value="selected?.id">
</div>
