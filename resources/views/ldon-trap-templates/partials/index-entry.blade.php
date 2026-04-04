<div x-data @keydown.window.escape="if(!$store.modalForm.saving) $store.modalForm.close()">
    <x-ui.card>
        <x-slot:header>
            <h3 class="card-title text-lg">LDoN Trap Template Entries</h3>
            <button type="button"
                class="btn btn-xs btn-soft btn-success"
                @click="$store.modalForm.openCreate({
                    baseUrl: '{{ route('ldon-trap-templates.entries.store', $trapTemplate) }}',
                    resourceName: 'LDoN Trap Template Entry',
                    defaults: {
                        trap_id: {{ $trapTemplate->id }},
                    }
                })">
                New LDoN Trap Template Entry
            </button>
        </x-slot:header>
        <div>
            <table class="table table-auto table-zebra md:table-fixed w-full">
                <thead class="text-sm uppercase bg-base-300">
                    <tr>
                        <th scope="col" class="w-[10%]">ID</th>
                        <th scope="col">Trap ID</th>
                        <th scope="col" class="w-[10%] text-right">-</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($trapTemplate->entries as $entry)
                        <tr x-data data-ldontrap='@json($entry)'>
                            <td>{{ $entry->id }}</td>
                            <td>{{ $entry->trap_id }}</td>
                            <td class="text-right">
                                <div class="inline join">
                                    <button type="button"
                                        class="join-item btn btn-sm btn-soft"
                                        @click="$store.modalForm.openEdit(
                                            $el.closest('tr').dataset.ldontrap,
                                            '{{ route('ldon-trap-templates.entries.update', [
                                                'trapTemplate' => $trapTemplate->id,
                                                'trapEntry'    => $entry->id,
                                            ]) }}',
                                            { resourceName: 'Edit LDoN Trap Template Entry' }
                                        )">
                                        <x-ui.icon name="edit" />
                                    </button>
                                    <form action="{{ route('ldon-trap-templates.entries.destroy', [
                                        'trapTemplate' => $trapTemplate->id,
                                        'trapEntry'    => $entry->id,
                                    ]) }}"
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
                            <td colspan="3" class="text-center text-sm opacity-60">
                                No trap entries assigned
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <x-slot:footer>
            <div></div>
            <button type="button"
                class="btn btn-xs btn-soft btn-success"
                @click="$store.modalForm.openCreate({
                    baseUrl: '{{ route('ldon-trap-templates.entries.store', $trapTemplate) }}',
                    resourceName: 'LDoN Trap Template Entry',
                    defaults: {
                        trap_id: {{ $trapTemplate->id }},
                    }
                })">
                New LDoN Trap Template Entry
            </button>
        </x-slot:footer>
    </x-ui.card>

    <x-modal-form>
        @include('ldon-trap-templates.forms.form-entry')
    </x-modal-form>
</div>
