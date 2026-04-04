<div x-data="formPreview('#spell-edit-form','spells')" x-cloak class="space-y-2 sticky top-0.5">
    <template x-if="effectDescOpen">
        <div class="card bg-neutral/80 shadow border border-base-content/10">
            <div class="card-body p-6">
                <div class="font-semibold mb-1 text-secondary" x-text="effectDescTitle"></div>
                <div class="text-sm text-neutral-300" x-html="effectDescBody"></div>
            </div>
        </div>
    </template>

    <div class="card bg-neutral shadow-xl border border-base-content/10">
        <div class="card-body p-5 space-y-4">
            <div x-ref="container">
                @include('spells.partials.preview-spell', ['spell' => $spell, 'dbstr_desc' => $dbstrDesc])
            </div>
        </div>
    </div>
</div>
