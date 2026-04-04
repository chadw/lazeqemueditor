<div
    x-data="bookPreview()"
    x-init="$nextTick(() => init())"
    class="relative flex flex-col items-center"
    :style="layout ? `width:${layout.width}px; height:${layout.height}px` : ''"
>
    <div class="flex gap-2 mb-2">
        <button class="btn btn-sm btn-soft btn-accent"
            type="button"
            :class="{'btn-disabled': bookType===0}"
            @click="toggleBook(0)">Book 1</button>

        <button class="btn btn-sm btn-soft btn-accent"
            type="button"
            :class="{'btn-disabled': bookType===1}"
            @click="toggleBook(1)">Book 2</button>
    </div>

    <div class="relative"
        :style="layout ? `width:${layout.width}px; height:${layout.height}px` : ''">

        <img
            :src="layout.image"
            :width="layout.width"
            :height="layout.height"
            class="block select-none pointer-events-none"
        >

        {{-- book1 --}}
        <template x-if="bookType === 0">
            <div
                x-ref="overlay"
                class="absolute font-sans text-xs text-[#2b1b0e] z-50 overflow-y-auto"
                :style="`
                    left:${layout.text.x}px;
                    top:${layout.text.y}px;
                    width:${layout.text.width}px;
                    height:${layout.text.height}px;
                    line-height:${layout.lineHeight}px;
                `"
                x-html="pages[pageIndex] || ''"
            ></div>
        </template>

        {{-- book2 --}}
        <template x-if="bookType === 1">
            <div>
                <div
                    class="absolute font-sans text-xs text-[#2b1b0e] z-50"
                    :style="`
                        left:${layout.text.left.x}px;
                        top:${layout.text.left.y}px;
                        width:${layout.text.left.width}px;
                        height:${layout.text.height}px;
                        line-height:${layout.lineHeight}px;
                    `"
                >
                    <template x-for="(line, i) in book2Pages[pageIndex * 2] || []" :key="i">
                        <div x-html="line.replace(/ /g, '&nbsp;') || '&nbsp;'"></div>
                    </template>
                </div>

                <div
                    class="absolute font-sans text-xs text-[#2b1b0e] z-50"
                    :style="`
                        left:${layout.text.right.x}px;
                        top:${layout.text.right.y}px;
                        width:${layout.text.right.width}px;
                        height:${layout.text.height}px;
                        line-height:${layout.lineHeight}px;
                    `"
                >
                    <template x-for="(line, i) in book2Pages[pageIndex * 2 + 1] || []" :key="i">
                        <div x-html="line.replace(/ /g, '&nbsp;') || '&nbsp;'"></div>
                    </template>
                </div>
                <button
                    type="button"
                    class="absolute left-1 top-1/2 -translate-y-1/2 z-50 btn btn-xs btn-soft btn-warning"
                    x-show="pageIndex > 0"
                    @click="prevPage"
                ><x-ui.icon name="square-arrow-left" /></button>
                <button
                    type="button"
                    class="absolute right-1 top-1/2 -translate-y-1/2 z-50 btn btn-xs btn-soft btn-warning"
                    x-show="book2Pages.length > (pageIndex + 1) * 2"
                    @click="nextPage"
                ><x-ui.icon name="square-arrow-right" /></button>
            </div>
        </template>
    </div>
</div>
