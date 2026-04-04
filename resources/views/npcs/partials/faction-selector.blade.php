<div class="form-control" x-data="{ saving: false }">
    <div class="join w-full">
        <div class="flex-1"
            x-data='ajaxSelect({
                searchUrl: "/npcs/factions/search",
                prefillValue: @json($prefill),
                allowNone: true,
                noneId: 0,
                noneLabel: "None",
            })''
            x-init="init()"
        >
            <select
                x-ref="select"
                id="primary_faction_select"
                name="npc_faction_id"
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
                const factionId = document.getElementById('primary_faction_select').value;
                axios.patch('{{ route('npcs.update-faction', $npc->id) }}', { npc_faction_id: factionId })
                    .then(() => window.location.reload() )
                    .catch(err => notify().error('Failed to save faction.'))
                    .finally(() => saving = false);
            "
        >
            <x-ui.icon name="save" x-show="!saving" />
            Save
        </button>
    </div>
</div>
