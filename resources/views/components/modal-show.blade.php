<div
    x-cloak
    x-show="$store.modalForm.isOpen" class="fixed inset-0 z-50 flex items-center justify-center"
>
    <div class="absolute inset-0 bg-black/50" @click="$store.modalForm.close()"></div>
    <div class="bg-base-100 rounded-lg shadow-lg z-10 w-full max-w-4xl max-h-[83vh] flex flex-col" @click.stop>
        <div class="flex items-center justify-between p-6 border-b border-base-content/10">
            <h2 class="text-lg font-semibold" x-text="$store.modalForm.title"></h2>
            <button class="btn btn-ghost" @click="$store.modalForm.close()">Close</button>
        </div>
        <div class="flex-1 overflow-y-auto p-6">
            {{ $slot }}
        </div>
        <div class="flex flex-col flex-1 min-h-0">
            <div class="p-6 border-t border-base-content/10 flex gap-2">
                <button type="button" class="btn btn-soft btn-error" @click="$store.modalForm.close()">Cancel</button>
            </div>
        </div>
    </div>
</div>
