<div
    x-data
    x-init="$store.toast.init(@js(session('toasts', [])))"
    class="toast toast-top toast-end top-8 right-10 z-50"
>
    <template x-for="toast in $store.toast.items" :key="toast.id">
        <div
            x-show="toast.visible"
            x-transition:enter="transform transition ease-out duration-300"
            x-transition:enter-start="translate-x-20 opacity-0"
            x-transition:enter-end="translate-x-0 opacity-100"
            x-transition:leave="transform transition ease-in duration-300"
            x-transition:leave-start="translate-x-0 opacity-100"
            x-transition:leave-end="translate-x-20 opacity-0"
            @mouseenter="$store.toast.pause(toast.id)"
            @mouseleave="$store.toast.resume(toast.id)"
            class="alert shadow-xl mb-3 relative overflow-hidden"
            :class="{
                'alert-success': toast.type === 'success',
                'alert-error': toast.type === 'error',
                'alert-warning': toast.type === 'warning',
                'alert-info': toast.type === 'info'
            }"
        >
            <div class="flex flex-col">
                <span x-show="toast.title" class="font-bold" x-text="toast.title"></span>
                <span x-show="toast.message" x-html="toast.message"></span>
            </div>

            <button
                type="button"
                class="btn btn-sm btn-ghost"
                @click="$store.toast.remove(toast.id)"
            >
                ✕
            </button>
            <div
                x-show="toast.timeout > 0"
                class="absolute bottom-0 left-0 w-full h-1 bg-black/10"
            >
                <div
                    class="h-full bg-current opacity-50"
                    :style="`
                        animation: toast-progress ${toast.remaining}ms linear forwards;
                        animation-play-state: ${toast.paused ? 'paused' : 'running'};
                    `"
                ></div>
            </div>
        </div>
    </template>
</div>
<style>
@keyframes toast-progress {
    from { width: 100%; }
    to { width: 0%; }
}
</style>
