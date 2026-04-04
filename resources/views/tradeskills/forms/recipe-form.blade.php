<form method="POST" action="{{ route('tradeskills.update', $recipe) }}">
    @csrf
    @method('PUT')
    <div class="grid grid-cols-6 gap-4 mb-4">
        <x-form.input
            name="name"
            label="Recipe Name"
            :value="$recipe->name"
            wrapper-class="col-span-3"
        />
        <x-form.select
            name="tradeskill"
            label="Tradeskill"
            :options="$tradeskills->toArray()"
            :selected="$recipe->tradeskill"
        />
        <x-form.input
            name="skillneeded"
            label="Skill Needed"
            :value="$recipe->skillneeded"
        />
        <x-form.input
            name="trivial"
            label="Trivial"
            :value="$recipe->trivial"
        />
    </div>
    <div class="grid grid-cols-4 gap-4 mb-4">
        @php
            $learn = $recipe->learn_flags;
        @endphp
        <x-form.select
            name="l_method"
            label="Learned By"
            :options="[0 => 'Not Learned', 1 => 'Quest', 2 => 'Experiment']"
            :selected="$learn['l_method']"
        />
        <x-form.select
            name="l_message"
            label="Client Message"
            :options="[0 => 'Yes', 16 => 'No']"
            :selected="$learn['l_message']"
        />
        <x-form.select
            name="l_search"
            label="Searchable"
            :options="[0 => 'Yes', 32 => 'No']"
            :selected="$learn['l_search']"
        />
        <div
            x-data="ajaxSelect({
                searchUrl: '/items/search',
                useModal: false,
                prefillValue: @js($recipe->learnedByItem
                    ? [
                        'id' => $recipe->learnedByItem->id,
                        'name' => $recipe->learnedByItem->Name,
                    ]
                    : null),
                allowNone: true,
                noneId: 0,
            })"
            x-init="init()"
        >
            <label class="label">Learned By Item</label>
            <select
                x-ref="select"
                name="learned_by_item_id"
                class="w-full"
            ></select>
        </div>
        <x-form.select
            name="min_expansion"
            label="Min Expansion"
            :options="[-1 => 'Any'] + config('everquest.expansions')"
            :selected="$recipe->min_expansion"
        />
        <x-form.select
            name="max_expansion"
            label="Max Expansion"
            :options="[-1 => 'Any'] + config('everquest.expansions')"
            :selected="$recipe->max_expansion"
        />
        <x-form.content-flag-select
            name="content_flags"
            label="Content Flags"
            :selected="$recipe->content_flags"
        />
        <x-form.content-flag-select
            name="content_flags_disabled"
            label="Content Flags Disabled"
            :selected="$recipe->content_flags_disabled"
        />
        <x-form.textarea
            name="notes"
            label="Notes"
            :value="$recipe->notes"
            rows="2"
            wrapper-class="col-span-4"
        />
    </div>
    <div class="flex flex-wrap items-center gap-4 my-6">
        <x-form.checkbox
            name="enabled"
            label="Enabled"
            :checked="$recipe->enabled"
        />
        <x-form.checkbox
            name="nofail"
            label="No Fail"
            :checked="$recipe->nofail"
        />
        <x-form.checkbox
            name="quest"
            label="Quest Controlled"
            :checked="$recipe->quest"
        />
        <x-form.checkbox
            name="replace_container"
            label="Replace Combine Container"
            :checked="$recipe->replace_container"
        />
    </div>

    <div class="md:col-span-2">
        <button class="btn btn-soft btn-success">Save Recipe</button>
    </div>
</form>
