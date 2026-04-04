<form method="POST" :action="$store.modalForm.formAction">
    @csrf

    <h3 class="text-lg font-bold mb-4"
        x-text="$store.modalForm.title"></h3>

    <label class="label">Required AA ID</label>
    <input name="aa_id" type="number"
           class="input input-bordered w-full"
           x-model="$store.modalForm.form.aa_id">

    <label class="label">Points Required</label>
    <input name="points" type="number"
           class="input input-bordered w-full"
           x-model="$store.modalForm.form.points">

    <div class="modal-action">
        <button class="btn btn-primary"
                x-text="$store.modalForm.submitLabel"></button>
        <button type="button" class="btn"
                @click="$store.modalForm.close()">Cancel</button>
    </div>
</form>
