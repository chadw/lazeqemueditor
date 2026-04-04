@extends('layouts.app')
@section('title', 'Mounts')
@section('page-title', 'Mounts')

@section('content')
    <div x-data @keydown.window.escape="if(!$store.modalForm.saving) $store.modalForm.close()">

        <x-top-links>
            <button type="button" class="btn btn-soft btn-success float-end"
                @click="$store.modalForm.openCreate({
                baseUrl: '{{ route('mounts.store') }}',
                resourceName: 'Mount',
                defaults: {
                    race: 216,
                    gender: 0,
                    texture: 0,
                    helmtexture: -1,
                    mountspeed: 0.75,
                    notes: 'None',
                }
            })">
                <x-ui.icon name="add" /> New Mount
            </button>
        </x-top-links>

        <x-ui.table>
            <x-slot:head>
                <tr>
                    <th scope="col">@sortablelink('filename', 'Name')</th>
                    <th scope="col">Notes</th>
                    <th scope="col" class="w-[10%]">@sortablelink('mountspeed', 'Speed')</th>
                    <th scope="col" class="w-[10%]">Race</th>
                    <th scope="col" class="w-[10%]">Gender</th>
                    <th scope="col" class="w-[10%]">Texture</th>
                    <th scope="col" class="w-[10%] text-right">-</th>
                </tr>
            </x-slot:head>
            <x-slot:body>
                @forelse ($mounts as $mount)
                    <tr x-data data-mount='@json($mount)'>
                        <td scope="row">{{ $mount->filename }}</td>
                        <td class="truncate">{{ $mount->notes }}</tyd>
                        <td>{{ $mount->mountspeed }}</td>
                        <td>
                            {{ config('everquest.db_races')[$mount->race] ?? 'Unknown' }}
                            <span class="badge badge-sm badge-soft">{{ $mount->race }}</span>
                        </td>
                        <td>
                            {{ config('everquest.npc_genders')[$mount->gender] }}
                            <span class="badge badge-sm badge-soft">{{ $mount->gender }}</span>
                        </td>
                        <td>
                            {{ config('everquest.npc_textures')[$mount->texture] ?? 'None' }}
                            <span class="badge badge-sm badge-soft">{{ $mount->texture }}</span>
                        </td>
                        <td class="text-right">
                            <div class="inline join">
                                <button type="button" class="join-item btn btn-sm btn-soft tooltip" data-tip="Edit"
                                    @click="$store.modalForm.openEdit(
                                        $el.closest('tr').dataset.mount,
                                        '{{ route('mounts.update', $mount) }}',
                                        { resourceName: 'Edit Mount' }
                                    )">
                                    <x-ui.icon name="edit" />
                                </button>
                                <form action="{{ route('mounts.destroy', $mount) }}" method="POST"
                                    class="inline">
                                    @csrf @method('DELETE')
                                    <button class="join-item btn btn-sm btn-soft btn-error tooltip" data-tip="Delete"
                                        onclick="return confirm('Delete?')">
                                        <x-ui.icon name="delete" />
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-6 text-base-content/50">
                            No mounts found.
                        </td>
                    </tr>
                @endforelse
            </x-slot:body>
        </x-ui.table>

        <div class="mt-4 shrink-0">{{ $mounts->links() }}</div>

        <x-modal-form>
            @include('mounts.forms.form')
        </x-modal-form>
    </div>
@endsection
