<div
    x-cloak
    x-data="{ loading: true, items: [] }"
    x-show="$store.modalForm.isOpen && $store.modalForm.activeModal === 'lootdrop-tables'"
    class="modal modal-open"
    x-transition
    x-init="() => {
        const load = async () => {
            try {
                loading = true;
                const url = $store.modalForm.meta?.url;
                if (!url) { items = []; loading = false; return; }
                const res = await fetch(url);
                if (!res.ok) { items = []; loading = false; return; }
                items = await res.json();
            } catch (e) { items = []; }
            loading = false;
        };

        $watch(() => $store.modalForm.isOpen && $store.modalForm.activeModal === 'lootdrop-tables', (open) => {
            if (open) load();
        });

        if ($store.modalForm.isOpen && $store.modalForm.activeModal === 'lootdrop-tables') {
            load();
        }
    }"
>
    <div class="modal-box w-full max-w-7xl max-h-[60vh] flex flex-col relative">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-bold text-lg">Loot Tables using <span class="text-accent font-bold"><span x-text="$store.modalForm.form?.drop?.name"></span></span></h3>
            <button
                type="button"
                class="btn btn-sm btn-circle btn-ghost absolute right-4 top-4"
                @click="$store.modalForm.close()"
            >
                ✕
            </button>
        </div>

        <div class="flex-1 overflow-y-auto overflow-x-hidden pr-1">
            <template x-if="loading">
                <div class="text-center py-6">Loading...</div>
            </template>

            <template x-if="!loading && items.length === 0">
                <div class="p-4">No Loot Tables reference this Loot Drop.</div>
            </template>

            <div x-show="!loading && items.length > 0">
                <div class="overflow-x-auto">
                    <table class="table table-auto table-zebra md:table-fixed w-full">
                        <thead class="text-xs uppercase bg-neutral">
                            <tr>
                                <th scope="col" class="w-[10%]">ID</th>
                                <th scope="col">Name</th>
                                <th scope="col" class="w-[5%]"># NPCs</th>
                                <th scope="col" class="w-[5%]">Drops</th>
                                <th scope="col">Cash</th>
                                <th scope="col" class="w-[10%] text-right">-</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="it in items" :key="it.id">
                                <tr>
                                    <td>
                                        <a :href="`{{ url('/loot') }}/${it.id}/edit`"
                                            class="text-base link link-info link-hover" x-text="it.id"></a>
                                    </td>
                                    <td>
                                        <a :href="`{{ url('/loot') }}/${it.id}/edit`"
                                            class="text-base link link-info link-hover" x-text="it.name"></a>
                                    </td>
                                    <td class="text-center"><span x-text="it.npcs ?? 0"></span></td>
                                    <td class="text-center"><span x-text="it.drops ?? 0"></span></td>
                                    <td>
                                        <div class="inline-flex items-center gap-2">
                                            <span class="badge badge-sm badge-soft" x-html="it.mincash_html ?? '-'"></span>
                                            <span class="opacity-60">to</span>
                                            <span class="badge badge-sm badge-soft" x-html="it.maxcash_html ?? '-'"></span>
                                        </div>
                                    </td>
                                    <td class="text-right">
                                        <a :href="`{{ url('/loot') }}/${it.id}/edit`"
                                            class="join-item btn btn-sm btn-soft tooltip"
                                            data-tip="Edit">
                                            <x-ui.icon name="edit" />
                                        </a>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="modal-action">
            <button type="button" class="btn btn-soft" @click="$store.modalForm.close()">
                Close
            </button>
        </div>
    </div>
    <div class="modal-backdrop" @click="$store.modalForm.close()"></div>
</div>
