<div class="grid grid-cols-[1fr_auto] gap-6">
    <div class="space-y-6">
        <div class="card bg-base-200 card-sm shadow-sm">
            <div class="card-body">
                <div class="grid grid-cols-3 gap-4">
                    <x-form.input
                        name="name"
                        label="Name"
                        maxlength="30"
                        required
                        x-model="$store.modalForm.form.name"
                        wrapper-class="col-span-2"
                    />
                    <x-form.select
                        name="language"
                        label="Language"
                        :options="config('everquest.skills.languages')"
                        x-model="$store.modalForm.form.language"
                    />
                </div>
            </div>
        </div>
        <div class="card bg-base-200 shadow-sm">
            <div class="card-body">
                <x-form.textarea
                    name="txtfile"
                    label="Text"
                    x-model="$store.modalForm.form.txtfile"
                    rows="20"
                />
                <p class="text-sm opacity-70">
                    ` = new line, `` = blank line, etc. (12 lines per page)
                </p>
            </div>
        </div>
    </div>
    <div class="flex justify-center items-start w-125.75 shrink-0 px-2">
        @include('books.partials.preview')
    </div>
</div>
