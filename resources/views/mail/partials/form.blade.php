<div class="card bg-base-200 shadow-md border border-base-300">
    <div class="card-body space-y-4">

        <div class="flex items-start justify-between">
            <div>
                <h3 class="text-lg font-semibold" x-text="$store.modalForm.form.subject"></h3>
                <p class="text-sm text-base-content/60">
                    From <span class="font-medium" x-text="$store.modalForm.form.from"></span>
                    to <span class="font-medium" x-text="$store.modalForm.form.character.name"></span>
                </p>
            </div>

            <span
                class="badge badge-sm badge-soft"
                :class="$store.modalForm.form.friendlyStatus === 'Unread'
                    ? 'badge-warning'
                    : 'badge-success'"
                x-text="$store.modalForm.form.friendlyStatus"
            ></span>
        </div>

        <div class="divider my-1"></div>

        <div class="max-w-none bg-base-100 rounded-lg p-4 border border-base-300">
            <p x-text="$store.modalForm.form.body"></p>
        </div>

        <div class="flex justify-between text-xs text-base-content/60">
            <span>
                Sent:
                <span class="font-medium" x-text="$store.modalForm.form.datetime"></span>
            </span>
        </div>

    </div>
</div>
