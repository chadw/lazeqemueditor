<template x-if="$store.modalCache.auras && $store.modalCache.auras.length">
    <div>
        <div class="hidden md:grid grid-cols-[1fr_120px_120px] gap-2 items-center p-3 text-sm font-semibold text-left bg-base-200 sticky top-0 z-20 border-b border-base-content/10">
            <div>Aura</div>
            <div>Type</div>
            <div>Distance</div>
        </div>
        <template x-for="(item, idx) in getModalItems()" :key="idx">
            <button
                type="button"
                class="w-full text-left p-2 hover:bg-base-200"
                @click="selectItem(item)"
            >
                <div class="grid grid-cols-[1fr_120px_120px] gap-2 items-center text-left">
                    <div class="font-medium truncate" x-text="item.name"></div>
                    <div class="font-medium truncate" x-text="item.aura_type"></div>
                    <div class="truncate" x-text="item.distance"></div>
                </div>
            </button>
        </template>
    </div>
</template>
