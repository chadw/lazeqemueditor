<form method="GET" action="{{ url()->current() }}" class="flex gap-2 items-end">
	<div class="w-60">
		<label class="label label-text">Loot Drop</label>
		<input type="text" name="drop" value="{{ request('drop') }}" class="input w-full"
			placeholder="Loot Drop ID or Name">
	</div>

	<div class="w-60">
		<label class="label label-text">Item</label>
		<input type="text" name="item" value="{{ request('item') }}" class="input w-full"
			placeholder="Item ID or Name">
	</div>

	<div class="flex items-center gap-2">
		<label class="cursor-pointer label">
			<input type="checkbox" name="orphan" value="1" class="checkbox" {{ request()->boolean('orphan') ? 'checked' : '' }} />
			<span class="label-text ml-2">Orphaned only</span>
		</label>
	</div>

	<div class="flex gap-2">
		<button type="submit" class="btn btn-soft btn-success">
			Filter
		</button>

		@if (request()->hasAny(['drop']) || request()->boolean('orphan'))
			<a href="{{ url()->current() }}" class="btn btn-soft btn-error">
				Reset
			</a>
		@endif
	</div>
</form>
