@props([
    'width' => 'max-w-7xl',
])
@php
    $height = $attributes->get('height', '');
@endphp
<div x-cloak x-show="$store.modalForm.isOpen" class="fixed inset-0 z-50 flex items-center justify-center">
    <div class="absolute inset-0 bg-black/50"></div>
    <div class="relative bg-base-100 rounded-lg shadow-lg z-10 w-full max-h-[83vh] flex flex-col"
        :class="[$store.modalForm.meta && $store.modalForm.meta.width ? $store.modalForm.meta.width : '{{ $width }}', $store.modalForm.meta && $store.modalForm.meta.height ? $store.modalForm.meta.height : '{{ $height }}']"
        @click.stop>
        <div class="flex items-center justify-between p-6 border-b border-base-content/10">
            <h2 class="text-lg font-semibold" x-text="$store.modalForm.title"></h2>
            <button class="btn btn-soft btn-circle" :disabled="$store.modalForm.saving"
                @click="if(!$store.modalForm.saving) $store.modalForm.close()">
                <x-ui.icon name="close" />
            </button>
        </div>
        <form :action="$store.modalForm.formAction" method="POST" class="flex flex-col flex-1 min-h-0"
            @submit.prevent="$store.modalForm.submit($event)">
            @csrf

            <template x-if="$store.modalForm.submitMethod === 'PUT'">
                <input type="hidden" name="_method" value="PUT">
            </template>

            <div x-show="$store.modalForm.errorMessage || Object.keys($store.modalForm.errors || {}).length > 0"
                x-cloak class="p-6 pb-0 mb-4">
                <div role="alert" class="alert alert-error">
                    <div>
                        <strong x-text="$store.modalForm.errorMessage || 'There were validation errors.'"></strong>
                        <ul class="mt-2 list-disc ml-5" x-show="Object.keys($store.modalForm.errors || {}).length > 0">
                            <template x-for="(msgs, field) in $store.modalForm.errors" :key="field">
                                <template x-for="msg in msgs">
                                    <li x-text="msg"></li>
                                </template>
                            </template>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="flex-1 overflow-y-auto overflow-x-hidden p-6">
                {{ $slot }}
            </div>

            <div class="p-6 border-t border-base-content/10 flex gap-2">
                <button type="submit" class="btn btn-soft btn-success" :disabled="$store.modalForm.saving">
                    <x-ui.icon name="save" /> <span x-text="$store.modalForm.submitLabel"></span>
                </button>
                <button type="button" class="btn btn-soft btn-error"
                    @click="if(!$store.modalForm.saving) $store.modalForm.close()"
                    :disabled="$store.modalForm.saving">
                    <x-ui.icon name="cancel" /> Cancel
                </button>
            </div>
            <div x-show="$store.modalForm.saving" x-transition
                class="absolute inset-0 z-40 flex items-center justify-center bg-base-100/80 backdrop-blur"
                style="display: none;">
                <div class="text-center">
                    <div class="loader mb-4" aria-hidden="true"></div>
                    <div class="font-medium">
                        <span class="loading loading-dots loading-xl"></span>
                        Saving... Please wait
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
