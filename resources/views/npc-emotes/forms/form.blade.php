<div class="card bg-base-200 card-sm shadow-sm mb-4">
    <div class="card-body">
        <div class="grid grid-cols-3 gap-4">
            <x-form.input
                name="emoteid"
                label="Emote ID"
                x-model="$store.modalForm.form.emoteid"
            />
            <x-form.select
                name="event_"
                label="Event"
                :options="config('everquest.emote_event')"
                x-model="$store.modalForm.form.event_"
            />
            <x-form.select
                name="type"
                label="Type"
                :options="config('everquest.emote_type')"
                x-model="$store.modalForm.form.type"
            />
        </div>
    </div>
</div>

<div class="card bg-base-200 card-sm shadow-sm mb-4">
    <div class="card-body">
        <div class="grid grid-cols-1 gap-4">
            <x-form.textarea
                name="text"
                label="Text"
                x-model="$store.modalForm.form.text"
            />
        </div>
    </div>
</div>
