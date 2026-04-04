<div class="space-y-6">
    <div class="card bg-base-200 card-sm shadow-sm">
        <div class="card-body">
            <h2 class="card-title">
                Faction Association: <span x-text="$store.modalForm.form.faction_list.name"></span>
            </h2>
            <div class="grid grid-cols-6 gap-4">
                @for ($i = 1; $i <= 10; $i++)
                    <div
                        x-data='ajaxSelect({
                            searchUrl: "/factions/search",
                            useModal: true,
                            prefillValue: () => $store.modalForm.form.faction{{ $i }} ?? null,
                            allowNone: true,
                            noneId: 0,
                            keyInOption: true,
                        })'
                        x-init="init()"
                        class="col-span-5"
                    >
                        <label class="label">Faction {{ $i }}</label>
                        <select
                            x-ref="select"
                            name="id_{{ $i }}"
                            class="w-full validator invalid:select-error"
                        ></select>
                    </div>
                    <x-form.input
                        name="mod_{{ $i }}"
                        label="Mod"
                        type="number"
                        min="-2000"
                        max="2000"
                        x-model="$store.modalForm.form.mod_{{ $i }}"
                    />
                @endfor
            </div>
        </div>
    </div>
</div>
