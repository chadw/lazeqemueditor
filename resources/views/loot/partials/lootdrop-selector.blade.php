<div class="min-w-85 form-control" x-data="{ saving: false }">
    <div class="join w-full">
        <div class="flex-1"
            x-data='ajaxSelect({
                searchUrl: "/loot/drops/search",
                prefillValue: () => null,
            })''
            x-init="init()"
        >
            <select
                x-ref="select"
                id="lootdrop_select"
                name="lootdrop_id"
                class="join-item w-full validator invalid:select-error"
                required
            ></select>
        </div>
        <button
            type="button"
            class="btn btn-soft btn-success join-item"
            :class="saving && 'loading'"
            :disabled="saving"
            @click="
                saving = true;
                const lootdropId = document.getElementById('lootdrop_select').value;
                axios.patch('{{ route('loot.update-lootdrop', $lt->id) }}', { lootdrop_id: lootdropId })
                    .then(() => { window.location.reload() })
                    .catch(err => { window.location.reload() });
            "
        >
            <x-ui.icon name="save" x-show="!saving" />
            Save
        </button>
    </div>
</div>
