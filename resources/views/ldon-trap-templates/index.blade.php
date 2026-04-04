@extends('layouts.app')
@section('title', 'LDoN Trap Templates')
@section('page-title', 'LDoN Trap Templates')

@section('content')
    <div x-data @keydown.window.escape="if(!$store.modalForm.saving) $store.modalForm.close()">

        <x-top-links>
            <button type="button" class="btn btn-soft btn-success"
                @click="$store.modalForm.openCreate({
                    baseUrl: '{{ route('ldon-trap-templates.store') }}',
                    resourceName: 'LDoN Trap Template',
                    defaults: {

                    }
                })">
                <x-ui.icon name="add" /> New LDoN Trap Template
            </button>
        </x-top-links>

        <x-ui.table>
            <x-slot:head>
                <tr>
                    <th scope="col" class="w-[10%]">Type</th>
                    <th scope="col">Spell</th>
                    <th scope="col" class="w-[10%]">Skill</th>
                    <th scope="col" class="w-[10%]">Trap Count</th>
                    <th scope="col" class="w-[10%] text-right">-</th>
                </tr>
            </x-slot:head>
            <x-slot:body>
                @foreach ($templates as $template)
                    <tr>
                        <td>{{ config('everquest.trap_type.' . $template->type) }}</td>
                        <td>
                            @if ($template->spell)
                                <x-spell-link
                                    :spell_id="$template->spell->id"
                                    :spell_name="$template->spell->name"
                                    :spell_icon="$template->spell->new_icon"
                                    spell_class="inline-flex"
                                    :effects_only="1"
                                />
                            @else
                                Unknown Spell (ID: {{ $template->spell_id }})
                            @endif
                        </td>
                        <td>{{ $template->skill }}</td>
                        <td>{{ $template->entries_count }}</td>
                        <td class="text-right">
                            <div class="inline join">
                                <a href="{{ route('ldon-trap-templates.edit', $template) }}"
                                    class="join-item btn btn-sm btn-soft">
                                    <x-ui.icon name="edit" />
                                </a>
                                <form action="{{ route('ldon-trap-templates.destroy', $template) }}"
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
            </x-slot:body>
        </x-ui.table>

        <div class="mt-4">{{ $templates->links() }}</div>

        <x-modal-form>
            @include('ldon-trap-templates.forms.new')
        </x-modal-form>
    </div>
@endsection
