<div x-data @keydown.window.escape="if(!$store.modalForm.saving) $store.modalForm.close()">
    <div class="card bg-base-300 card-sm shadow-sm mt-6">
        <div class="card-body">
            <h2 class="card-title flex items-center">
                Faction Mods
                <button type="button" class="btn btn-soft btn-sm btn-success ml-auto"
                    @click="$store.modalForm.openCreate({
                        baseUrl: '{{ route('factions.mods.store', $faction) }}',
                        resourceName: 'Faction Mod',
                        modal: 'mod',
                        defaults: {
                            _mod_type: 'class',
                            _mod_value: 1,
                            mod_name: 'c1',
                            mod: 0,
                        }
                    })">
                    New Faction Mod
                </button>
            </h2>
            @if ($faction->mod->isNotEmpty())
            <div class="border border-base-content/5 overflow-x-auto">
                <table class="table table-auto table-zebra md:table-fixed w-full">
                    <thead class="text-xs uppercase bg-neutral">
                        <tr>
                            <th scope="col" class="w-[10%]">Type</th>
                            <th scope="col">Name</th>
                            <th scope="col" class="w-[10%]">Mod</th>
                            <th scope="col" class="w-[10%]">Effective Faction</th>
                            <th scope="col" class="w-[15%] text-right">-</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($faction->mod as $mod)
                            <tr x-data data-mod='@json($mod)'>
                                <td>{{ $mod->mod_type['type'] }}</td>
                                <td>{{ $mod->mod_type['name'] }}</td>
                                <td>{{ $mod->mod }}</td>
                                <td>{{ ($faction->base + $mod->mod) }}</td>
                                <td class="text-right">
                                    <div class="inline join">
                                        <button type="button" class="join-item btn btn-sm btn-soft"
                                            @click="$store.modalForm.openEdit(
                                                $el.closest('tr').dataset.mod,
                                                '{{ route('factions.mods.update', [$faction, $mod]) }}',
                                                {
                                                    resourceName: 'Edit Faction Mod',
                                                    modal: 'mod',
                                                }
                                        )">
                                            <x-ui.icon name="edit" />
                                        </button>
                                        <form action="{{ route('factions.mods.destroy', [$faction, $mod]) }}"
                                            method="POST" class="inline">
                                            @csrf @method('DELETE')
                                            <button class="join-item btn btn-sm btn-soft btn-error"
                                                onclick="return confirm('Delete?')">
                                                <x-ui.icon name="delete" />
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
                <div role="alert" class="alert alert-info alert-soft">
                    <span>No faction mods found.</span>
                </div>
            @endif
        </div>
    </div>
</div>
<script>
    window.eqFactionModOptions = {
        class: @js(config('everquest.classes')),
        race: @js(config('everquest.db_races')),
        deity: @js(config('everquest.deity')),
    }
</script>
