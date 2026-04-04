<template x-if="$store.modalCache.horses && $store.modalCache.horses.length">
    <div>
        <div class="hidden md:grid grid-cols-[1fr_1fr_50px_1fr_70px_70px] gap-2 items-center p-3 text-sm font-semibold text-left bg-base-200 sticky top-0 z-20 border-b border-base-content/10">
            <div>Name</div>
            <div>Notes</div>
            <div>Speed</div>
            <div>Race</div>
            <div>Gender</div>
            <div>Texture</div>
        </div>
        <template x-for="(item, idx) in getModalItems()" :key="idx">
            <button
                type="button"
                class="w-full text-left p-2 hover:bg-base-200"
                @click="selectItem(item)"
            >
                <div class="grid grid-cols-[1fr_1fr_50px_1fr_70px_70px] gap-2 items-center text-left">
                    <div class="truncate" x-text="item.filename ?? item.name ?? '-'"></div>
                    <div class="truncate" x-text="item.notes ?? '-'"></div>
                    <div class="truncate" x-text="item.mountspeed ?? item.speed ?? '-'"></div>
                    <div class="truncate" x-text="(dbRaces && dbRaces[String(item.race)]) ? dbRaces[String(item.race)] : (item.race ?? '-')"></div>
                    <div class="truncate" x-text="item.gender ?? '-'"></div>
                    <div class="truncate" x-text="item.texture ?? '-'"></div>
                </div>
            </button>
        </template>
    </div>
</template>
