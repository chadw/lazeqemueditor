<div class="form-control" x-data="{ saving: false }">
    <div class="join w-full">
        <div class="flex-1"
            x-data='ajaxSelect({
                searchUrl: "/npc-spells/search",
                prefillValue: @json($prefill),
                allowNone: true,
                noneId: 0,
                noneLabel: "None",
            })''
            x-init="init()"
        >
            <select
                x-ref="select"
                id="npcspells_select"
                name="npc_spells_id"
                class="join-item w-full validator invalid:select-error"
            ></select>
        </div>
        <button
            type="button"
            class="btn btn-soft btn-success join-item"
            :class="saving && 'loading'"
            :disabled="saving"
            @click="
                saving = true;
                const npcSpellsId = document.getElementById('npcspells_select').value;
                axios.patch('{{ route('npcs.update-spellset', $npc->id) }}', { npc_spells_id: npcSpellsId })
                    .then(() => window.location.reload())
                    .catch(err => { window.location.reload(); });
            "
        >
            <x-ui.icon name="save" x-show="!saving" />
            Save
        </button>
    </div>
</div>
