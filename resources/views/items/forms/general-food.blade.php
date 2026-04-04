<div class="card bg-base-200 card-sm shadow-sm">
    <div class="card-body">
        <h2 class="card-title">Food</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
            <x-form.input
                name="casttime_"
                label="Consumption Rate"
                type="number"
                tooltip=""
                :value="$item->casttime_"
            />
        </div>
    </div>
</div>
