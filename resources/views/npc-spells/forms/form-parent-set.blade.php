<div class="card bg-base-200 card-sm shadow-sm mb-6">
    <div class="card-body">
        <div class="grid grid-cols-1 gap-4">
            <label class="label">Parent Spell Set</label>
            <select name="parent_list" x-model="$store.modalForm.form.parent_list" class="w-full select">
                <option value="">None</option>
                @foreach(($allNpcSpells ?? []) as $id => $name)
                    <option value="{{ $id }}">{{ $id }} - {{ $name }}</option>
                @endforeach
            </select>
        </div>
    </div>
</div>
