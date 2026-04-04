<div class="space-y-6" x-data="formTracker">
    <div class="card bg-base-200 card-sm shadow-sm">
        <div class="card-body">
            <h2 class="card-title">Faction ID: {{ $faction->id }}</h2>
            <div class="grid grid-cols-6 gap-4 mb-2">
                <x-form.input
                    name="name"
                    label="Name"
                    :value="$faction->name"
                    wrapper-class="col-span-5"
                    required
                />
                <x-form.input
                    name="base"
                    label="Base"
                    type="number"
                    min="0"
                    :value="$faction->base"
                    wrapper-class="col-span-1"
                />
            </div>
            <div class="grid grid-cols-5 gap-4 mb-2">
                <x-form.input
                    name="faction_base_data[min]"
                    label="Min Faction"
                    type="number"
                    min="-2000"
                    max="2000"
                    :value="$faction->basedata?->min"
                />
                <x-form.input
                    name="faction_base_data[max]"
                    label="Max Faction"
                    type="number"
                    min="-2000"
                    max="2000"
                    :value="$faction->basedata?->max"
                />
                <x-form.input
                    name="faction_base_data[unk_hero1]"
                    label="Hero 1"
                    type="number"
                    min="0"
                    :value="$faction->basedata?->unk_hero1"
                />
                <x-form.input
                    name="faction_base_data[unk_hero2]"
                    label="Hero 2"
                    type="number"
                    min="0"
                    :value="$faction->basedata?->unk_hero2"
                />
                <x-form.input
                    name="faction_base_data[unk_hero3]"
                    label="Hero 3"
                    type="number"
                    min="0"
                    :value="$faction->basedata?->unk_hero3"
                />
            </div>
            <div class="card-actions">
                <button type="submit" class="btn btn-soft btn-success">Save Faction</button>
            </div>
        </div>
    </div>
</div>
