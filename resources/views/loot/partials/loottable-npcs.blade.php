<div x-cloak x-data="{ open: false }" class="collapse collapse-arrow bg-base-200 rounded-box border mb-4"
    :class="open ? 'border-base-content/10' : 'border-base-100'">
    <input type="checkbox" id="loottable-{{ $table->id }}-toggle" x-model="open" />
    <label for="loottable-{{ $table->id }}-toggle"
        class="collapse-title flex items-center justify-between gap-4 border-b border-base-300 transition-colors"
        :class="open ? 'bg-accent/20' : 'bg-base-100'">
        <div class="flex items-center gap-4 truncate w-full">
            <span class="font-bold inline-block truncate">NPCs using this Loot Table</span>
            <span class="text-sm text-base-content/60 w-20 inline-block">
                <span class="badge badge-soft badge-accent">{{ $table->npcs->count() }} total</span>
            </span>
        </div>
    </label>

    <div class="collapse-content">
        @if ($table->npcs->isEmpty())
            <div class="text-sm text-muted pt-4">No NPCs are currently assigned to this loot table.</div>
        @else
            <div class="flex flex-wrap items-center gap-2 text-sm pt-4">
                @foreach ($table->npcs as $npc)
                    <a href="{{ route('npcs.edit', $npc->id) }}"
                        class="inline-flex items-center gap-2 px-2 py-1 btn btn-xs btn-soft rounded-md transition max-w-[min(100%,18rem)] truncate">
                        <span class="font-medium truncate">{{ $npc->clean_name ?? $npc->name }}</span>
                        <span class="badge badge-sm badge-soft badge-info">#{{ $npc->id }}</span>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</div>
