<div
    x-cloak
    x-data="{ open: false }"
    @ranks:toggle.window="open = $event.detail"
    class="aa-rank-card collapse collapse-arrow bg-base-200 rounded-box border"
    data-rank-id="{{ $rank['id'] }}"
    :class="open ? 'border-base-content/10' : 'border-base-100'"
>
    <input type="checkbox" id="aa-rank-{{ $rank['id'] }}-toggle" x-model="open" />
    <label for="aa-rank-{{ $rank['id'] }}-toggle" class="collapse-title flex items-center justify-between gap-4 border-b border-base-300 transition-colors"
        :class="open ? 'bg-accent/20' : 'bg-base-100'">
        <div class="flex items-center gap-4 truncate w-full">
            <span class="font-bold w-20 inline-block truncate">#{{ $rank['id'] }}</span>
            <span class="text-sm text-base-content/60 w-20 inline-block">
                <span class="badge badge-soft badge-accent">Rank {{ $index ?? '' }}</span>
            </span>
            <span class="text-sm text-base-content/60 w-20 inline-block truncate">
                Cost: <span class="text-info">{{ $rank['cost'] }}</span>
            </span>
            <span class="text-sm text-base-content/60 flex-1 min-w-0">
                Spell: <span class="font-medium truncate">{{ data_get($rank, 'spell_.name', 'None') }}</span>
                <span class="badge badge-sm badge-soft">{{ data_get($rank, 'spell_.id', $rank['spell']) }}</span>
            </span>
            <span class="text-sm text-base-content/60 w-20 inline-block truncate">
                Effects: <span class="font-medium">{{ count(data_get($rank, 'effects', [])) }}</span>
            </span>
            <span class="text-sm text-base-content/60 w-20 inline-block truncate">
                Prereqs: <span class="font-medium">{{ count(data_get($rank, 'prereqs', [])) }}</span>
            </span>
        </div>

        <div class="flex items-center gap-2">
            <button type="button" class="btn btn-sm btn-soft btn-success tooltip tooltip-left"
                data-tip="Save this Rank"
                @click.stop.prevent="$store.rankSaver.saveRank({{ $rank['id'] }})"
                :disabled="$store.rankSaver.isSaving('{{ $rank['id'] }}')"
            >
                <span x-show="!$store.rankSaver.isSaving('{{ $rank['id'] }}')"><x-ui.icon name="save" /></span>
                <span x-show="$store.rankSaver.isSaving('{{ $rank['id'] }}')">Saving...</span>
            </button>
            @if ($canDelete ?? false)
                <form action="{{ url("/aa/ranks/{$rank['id']}") }}" method="POST" class="inline"
                    onsubmit="return confirm('Delete rank?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-soft btn-error tooltip tooltip-left"
                        data-tip="Delete this Rank">
                        <x-ui.icon name="delete" />
                    </button>
                </form>
            @endif
        </div>
    </label>

    <div class="collapse-content">
        @include('aa.forms.ranks-edit')
        <div class="card bg-base-200 shadow">
            {{-- <div class="divider"></div> --}}
            <x-aa.rank-effects :rank="$rank" />
            <x-aa.rank-prereqs :rank="$rank" />
        </div>
    </div>
</div>
