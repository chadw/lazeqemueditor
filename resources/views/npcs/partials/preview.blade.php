<div x-data="formPreview('#npc-edit-form','npcs')" x-cloak class="space-y-2 sticky top-0.5">
    <div class="card bg-neutral shadow-xl border border-base-content/10">
        <div class="card-body p-5 space-y-4">
            <div x-ref="container">
                @include('npcs.partials.preview-npc', ['npc' => $npc])
            </div>
        </div>
    </div>
</div>
