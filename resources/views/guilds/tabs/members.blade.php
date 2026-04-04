<div x-data @keydown.window.escape="if(!$store.modalForm.saving) $store.modalForm.close()">
    <x-top-links>
        <button
            type="button"
            class="btn btn-soft btn-success ml-auto"
            @click="$store.modalForm.openCreate({
                baseUrl: '{{ route('guilds.members.store', $guild) }}',
                resourceName: 'Guild Member'
            })"
        >
            <x-ui.icon name="add" /> Add Member
        </button>
    </x-top-links>

    <x-ui.table>
        <x-slot:head>
            <tr>
                <th scope="col">@sortablelink('name', 'Name')</th>
                <th scope="col">Rank</th>
                <th scope="col">@sortablelink('class', 'Class')</th>
                <th scope="col">@sortablelink('race', 'Race')</th>
                <th scope="col">@sortablelink('level', 'Level')</th>
                <th scope="col">Public Note</th>
                <th scope="col" class="w-[5%]">Alt</th>
                <th scope="col" class="w-[5%]">Online</th>
                <th scope="col" class="text-right">-</th>
            </tr>
        </x-slot:head>
        <x-slot:body>
            @forelse ($members as $member)
                <tr x-data data-guildmember='@json($member)'>
                    <td scope="row">
                        <a class="text-base link-accent link-hover"
                            href="{{ route('characters.show', $member->character?->id) }}">
                            {{ $member->character?->name }}
                        </a>
                    </td>
                    <td>{{ $member->guildRank?->title }}</td>
                    <td class="hidden md:table-cell">
                        {{ config('everquest.classes.' . $member->character->class) ?? null }}</td>
                    <td class="hidden md:table-cell">{{ config('everquest.races.' . $member->character->race) ?? null }}
                    </td>
                    <td>{{ $member->character->level }}</td>
                    <td class="truncate">{{ $member->public_note }}</td>
                    <td><x-status :ok="$member->alt" /></td>
                    <td><x-status :ok="$member->online" /></td>
                    <td class="text-right">
                        <button
                            type="button"
                            class="btn btn-sm btn-soft btn-error tooltip"
                            data-tip="Delete"
                            @click="$store.ajaxRemover.remove({
                                url: '{{ route('guilds.members.destroy', [$guild, $member]) }}',
                                removeEl: $el.closest('tr'),
                                confirmMessage: 'Remove this member from the guild?'
                            })"
                        >
                            <x-ui.icon name="delete" />
                        </button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center text-gray-500">
                        No guild members found.
                    </td>
                </tr>
            @endforelse
        </x-slot:body>
    </x-ui.table>

    <div class="mt-4">{{ $members->links() }}</div>

    <x-modal-form width="max-w-xl">
        @include('guilds.forms.form')
    </x-modal-form>
</div>
