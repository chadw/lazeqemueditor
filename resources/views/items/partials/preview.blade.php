<div x-data="formPreview('#item-edit-form','items')" x-cloak class="space-y-2 sticky top-0.5">
    <div class="card bg-neutral shadow-xl border border-base-content/10">
        <div class="card-body p-5 space-y-4">
            <div x-ref="container">
                @include('items.partials.preview-item', ['item' => $item])
            </div>
        </div>
    </div>
</div>
