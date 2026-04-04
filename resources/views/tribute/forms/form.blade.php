<div class="grid grid-cols-1 lg:grid-cols-3 gap-6" x-data="formTracker">
    <div class="lg:col-span-2 space-y-6">
        <div class="card bg-base-200 card-sm shadow-sm">
            <div class="card-body">
                <div class="grid grid-cols-1 gap-4">
                    <x-form.input
                        name="name"
                        label="Name"
                        x-model="$store.modalForm.form.name"
                        required
                    />
                    <x-form.textarea
                        name="descr"
                        label="Description"
                        x-model="$store.modalForm.form.descr"
                        rows="6"
                        x-on:keydown.enter.prevent=""
                    />
                    <div class="label -mt-3">
                        <span class="label-text-alt">
                            Tip: Use <code class="mx-1">&lt;br&gt;</code> for new lines.
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="lg:col-span-1 flex flex-col">
        <div class="card bg-neutral text-neutral-content shadow-inner grow">
            <div class="card-body p-4">
                <h3 class="font-bold text-accent mb-2 border-b border-base-content/10 pb-1"
                    x-text="$store.modalForm.form.name || 'Tribute Name'">
                </h3>
                <div
                    class="text-xs font-mono whitespace-pre-wrap leading-relaxed"
                    x-html="$store.modalForm.form.descr || `<span class='opacity-40 italic'>No description provided...</span>`"
                ></div>
            </div>
        </div>
    </div>
</div>
