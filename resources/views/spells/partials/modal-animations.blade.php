<div
    x-data
    x-show="$store.animationPicker.isOpen"
    x-on:open-anim-picker.window="$store.animationPicker.open($event.detail)"
    class="modal modal-open"
    x-transition
    x-cloak
>
    <div class="modal-box w-full max-w-6xl h-[90vh] flex flex-col">
        <button
            type="button"
            @click="$store.animationPicker.close()"
            class="absolute top-3 right-3 z-20
                   btn btn-sm btn-circle btn-ghost
                   hover:bg-error hover:text-error-content"
            aria-label="Close"
        >
            ✕
        </button>
        <h3 class="font-bold text-lg mb-4">
            Select animation
        </h3>
        <div
            x-ref="grid"
            class="grid grid-cols-2 lg:grid-cols-3 gap-3 overflow-y-auto flex-1 p-4"
        >
            <template x-for="id in $store.animationPicker.animations" :key="`${$store.animationPicker.type}-${id}`">
                <div
                    :data-anim-id="id"
                    class="group relative aspect-video w-full min-h-65 rounded border border-base-content/20
                        bg-neutral overflow-hidden cursor-pointer"
                    :class="{
                        'border-2 border-secondary shadow-[0_0_12px_var(--color-secondary)]':
                            $store.animationPicker.selectedId === String(id),
                        'border-2 border-transparent group-hover:border-accent/70':
                            $store.animationPicker.selectedId !== String(id)
                    }"
                    x-intersect.once="$store.animationPicker.loadVideo($el, id)"
                    @click="$store.animationPicker.select(id)"
                >
                    <video
                        muted
                        loop
                        playsinline
                        preload="none"
                        class="absolute inset-0 w-full h-full object-cover"
                    ></video>
                    <span class="badge badge-ghost badge-sm absolute left-1 top-1 text-xs opacity-90" x-text="id"></span>
                    <div class="pointer-events-none absolute inset-0 rounded border-2 border-accent/70
                        shadow-[0_0_12px_var(--color-accent)] opacity-0 group-hover:opacity-100 transition"
                    ></div>
                </div>
            </template>
        </div>
        <div class="modal-action">
            <button type="button" class="btn btn-soft" @click="$store.animationPicker.close()">Close</button>
        </div>
    </div>
    <div class="modal-backdrop" @click="$store.animationPicker.close()"></div>
</div>
