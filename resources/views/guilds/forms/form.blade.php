<div class="card bg-base-200 card-sm shadow-sm mb-45">
    <div class="card-body">
        <div class="grid grid-cols-1 gap-4">
            <input type="hidden" name="guild_id" x-model="$store.modalForm.form.guild_id">
            <div
                x-data="ajaxSelect({
                    searchUrl: '/characters/search',
                    prefillPath: ''
                })"
                x-init="init()"
            >
                <label class="label">Character</label>
                <select
                    x-ref="select"
                    name="char_id"
                    class="w-full"
                ></select>
            </div>
        </div>
    </div>
</div>
