<div class="form-control" x-data="{ saving: false }">
    <div class="join w-full">
        <div class="flex-1"
            x-data='ajaxSelect({
                searchUrl: "/loot/search",
                prefillValue: @json($prefill),
                allowNone: true,
                noneId: 0,
                noneLabel: "None",
            })''
            x-init="init()"
        >
            <select
                x-ref="select"
                id="loottable_select"
                name="loottable_id"
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
                const loottableId = document.getElementById('loottable_select').value;
                axios.patch('{{ route('npcs.update-loottable', $npc->id) }}', { loottable_id: loottableId })
                    .then(() => window.location.reload() )
                    .catch(err => window.location.reload() );
            "
        >
            <x-ui.icon name="save" x-show="!saving" />
            Save
        </button>
    </div>
</div>
