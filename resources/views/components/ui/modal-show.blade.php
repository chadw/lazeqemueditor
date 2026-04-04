@props(['id' => 'modal-show-ui'])

<div
    x-data
    x-show="$store.modalForm.isOpen && $store.modalForm.activeModal === '{{ $id }}'"
    class="modal modal-open"
    role="dialog"
    aria-labelledby="modal-title"
    @keydown.escape.window="$store.modalForm.close()"
    x-cloak
>
    <div class="modal-box w-11/12 max-w-7xl max-h-[83vh] relative flex flex-col p-0"
        @click.outside="$store.modalForm.close()">
        <div class="p-6 pb-2">
            <button
                type="button"
                @click="$store.modalForm.close()"
                class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2"
            >✕</button>
            <h3 id="modal-title" class="text-lg font-bold" x-text="$store.modalForm.title"></h3>
        </div>
        <div class="flex-1 overflow-y-auto px-6 py-4 border-y border-base-300 bg-base-100/50">
            {{ $slot }}
        </div>
        <div class="p-4 px-6 bg-base-200/30">
            <div class="flex justify-end">
                <button type="button" class="btn" @click="$store.modalForm.close()">Close</button>
            </div>
        </div>
    </div>
</div>
