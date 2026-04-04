@php
    $zoneTrapDefaults = [
        'zone' => $zone->short_name,
        'version' => $zone->version,
        'x' => 0,
        'y' => 0,
        'z' => 0,
        'chance' => 0,
        'maxzdiff' => 0,
        'radius' => 0,
        'effect' => 0,
        'effectvalue' => 0,
        'effectvalue2' => 0,
        'skill' => 0,
        'level' => 1,
        'respawn_time' => 60,
        'respawn_var' => 0,
        'triggered_number' => 0,
        'group' => 0,
        'despawn_when_triggered' => 1,
    ];
@endphp
@push('scripts')
<script>
    window.zoneTrapDefaults = @json($zoneTrapDefaults);
</script>
@endpush

<x-ui.card>
    <x-slot:header>
        <h3 class="card-title">Traps</h3>
        <button type="button" class="btn btn-sm btn-soft btn-success"
            @click="$store.modalForm.openCreate({
                baseUrl: '{{ route('zones.traps.store', ['zone' => $zone->short_name]) }}',
                resourceName: 'Trap',
                modal: 'trap',
                defaults: zoneTrapDefaults
        })">
            <x-ui.icon name="add" /> Add
        </button>
    </x-slot:header>
    <x-ui.table>
        <x-slot:head>
            <tr>
                <th scope="col" class="w-[10%]">Type</th>
                <th scope="col">Effect</th>
                <th scope="col">Effect 2</th>
                <th scope="col" class="w-[20%]">Coords</th>
                <th scope="col" class="w-[5%]">Radius</th>
                <th scope="col" class="w-[5%]">Chance</th>
                <th scope="col" class="w-[5%]">Respawn</th>
                <th scope="col" class="w-[5%]">Skill</th>
                <th scope="col" class="w-[5%]">Level</th>
                <th scope="col" class="w-[10%] text-right">-</th>
            </tr>
        </x-slot:head>
        <x-slot:body>
            @forelse ($zone->traps as $trap)
                <tr x-data data-trap='@json($trap)'>
                    <td>{{ config('everquest.trap_type.' . $trap->effect) }}</td>
                    <td>
                        @if ($trap->effectTarget)
                            @switch($trap->effectTarget['type'])
                                @case('spell')
                                    <x-spell-link
                                        :spell_id="$trap->effectTarget['model']->id"
                                        :spell_name="$trap->effectTarget['model']->name"
                                        :spell_icon="$trap->effectTarget['model']->new_icon"
                                        spell_class="inline-flex"
                                        :effects_only="1"
                                    />
                                    @break
                                @case('npc')
                                    <a href="{{ route('npcs.edit', $trap->effectTarget['model']->id) }}"
                                        class="link link-soft link-info"
                                    >
                                        {{ $trap->effectTarget['model']->clean_name }}
                                    </a>
                                    @break
                            @endswitch
                        @else
                            @if ($trap->effect === 4)
                                {{ $trap->effectvalue }}
                                <div class="badge badge-sm badge-soft badge-accent ml-1">Min Dmg</div>
                            @else
                                {{ $trap->effectvalue }}
                            @endif
                        @endif
                    </td>
                    <td>{!! $trap->effectValue2Label() !!}</td>
                    <td>
                        x: {{ floor($trap->x) }},
                        y: {{ floor($trap->y) }},
                        z: {{ floor($trap->z) }}
                    </td>
                    <td>{{ $trap->radius }}</td>
                    <td>{{ $trap->chance }}%</td>
                    <td>{{ $trap->respawn_time }}</td>
                    <td>{{ $trap->skill }}</td>
                    <td>{{ $trap->level }}</td>
                    <td class="text-right">
                        <div class="join">
                            <button type="button" class="join-item btn btn-sm btn-soft"
                                @click="$store.modalForm.openEdit(
                                    $el.closest('tr').dataset.trap,
                                    '{{ route('zones.traps.update', ['zone' => $zone->short_name, 'trap' => $trap->id]) }}',
                                    {
                                        modal: 'trap',
                                        resourceName: 'Edit Trap',
                                    }
                                )">
                                <x-ui.icon name="edit" />
                            </button>
                            <form
                                action="{{ route('zones.traps.destroy', ['zone' => $zone->short_name, 'trap' => $trap->id]) }}"
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
            @empty
                <tr>
                    <td colspan="10" class="text-center py-6 text-base-content/50">
                        No traps found.
                    </td>
                </tr>
            @endforelse
        </x-slot:body>
    </x-ui.table>
    <x-slot:footer>
        <div></div>
        <button type="button" class="btn btn-sm btn-soft btn-success"
            @click="$store.modalForm.openCreate({
                baseUrl: '{{ route('zones.traps.store', ['zone' => $zone->short_name]) }}',
                resourceName: 'Trap',
                modal: 'trap',
                defaults: zoneTrapDefaults
        })">
            <x-ui.icon name="add" /> Add
        </button>
    </x-slot:footer>
</x-ui.card>
