<div data-spells-root
    x-init="$store.spellsFilter.filter($el)"
    x-effect="$store.spellsFilter.query; $store.spellsFilter.filter($el)">

	<div class="space-y-6">
		<div class="max-w-md">
			<label class="label label-text">Filter spells</label>
			<input
				type="search"
				x-model.debounce.300ms="$store.spellsFilter.query"
				placeholder="Filter spells (e.g. heal)"
				class="input w-full"
			/>
		</div>

		{{-- disciplines --}}
		@if ($character->disciplines && $character->disciplines->count())
			<div>
				<div class="flex items-center justify-between mb-3">
					<h3 class="text-lg text-accent font-semibold">Disciplines</h3>
					<div class="ml-4 flex-1 border-b border-dotted border-base-content/20"></div>
				</div>

				<div class="grid grid-cols-4 md:grid-cols-6 lg:grid-cols-8 gap-4 mb-6">
					@foreach($character->disciplines as $disc)
						@php
							$spellObj = $disc && ($disc->spell ?? null) ? $disc->spell : null;
							$spellName = $spellObj ? strtolower($spellObj->name) : '';
						@endphp
						<div data-spell-slot data-spell-name="{{ $spellName }}">
							@if($spellObj)
								<div class="flex flex-col items-center p-3 bg-base-100 border border-base-content/10 rounded-lg">
                                    <x-spell-link-stacked
                                        :spell_id="$spellObj->id"
                                        :spell_name="$spellObj->name"
                                        :spell_icon="$spellObj->new_icon"
                                        :spell_target_type="$spellObj->targettype"
                                        :effects_only="1"
                                    />
								</div>
							@else
								<div class="flex flex-col items-center p-3 bg-base-200 border border-dashed border-base-content/10 rounded-lg">
									<div class="w-6 h-6 bg-base-300 rounded-sm mb-1"></div>
									<div class="text-xs text-center text-base-content/50">Empty</div>
								</div>
							@endif
						</div>
					@endforeach
				</div>
			</div>
		@endif

        @if ($character->spells->isNotEmpty())
            @php
                $pagesData = $character->spellPages ?? ['pages' => [], 'hasSlotId' => false];
                $pages = $pagesData['pages'];
                $hasSlotId = $pagesData['hasSlotId'];
            @endphp
            @foreach($pages as $pIndex => $page)
                <div data-page>
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-lg text-accent font-semibold">Page {{ $pIndex + 1 }}</h3>
                        <div class="ml-4 flex-1 border-b border-dotted border-base-content/20"></div>
                    </div>

                    <div class="grid grid-cols-4 md:grid-cols-6 lg:grid-cols-8 gap-4">
                        @foreach($page as $slotIndex => $charSpell)
                            @php
                                $spellObj = $charSpell && ($charSpell->spell ?? null) ? $charSpell->spell : null;
                                $spellName = $spellObj ? strtolower($spellObj->name) : '';
                            @endphp
                            <div data-spell-slot data-spell-name="{{ $spellName }}">
                            @if($spellObj)
                                <div class="flex flex-col items-center p-3 bg-base-100 border border-base-content/10 rounded-lg">
                                    <x-spell-link-stacked
                                        :spell_id="$spellObj->id"
                                        :spell_name="$spellObj->name"
                                        :spell_icon="$spellObj->new_icon"
                                        :spell_target_type="$spellObj->targettype"
                                        :effects_only="1"
                                    />
                                    <div class="text-xs text-base-content/60 mt-2">Slot {{ $hasSlotId ? ($charSpell->slot_id) : ($pIndex*8 + $slotIndex) }}</div>
                                </div>
                            @else
                                <div class="flex flex-col items-center p-3 bg-base-200 border border-dashed border-base-content/10 rounded-lg">
                                    <div class="w-6 h-6 bg-base-300 rounded-sm mb-1"></div>
                                    <div class="text-xs text-center text-base-content/50">Empty</div>
                                    <div class="text-xs text-base-content/60 mt-2">Slot {{ $hasSlotId ? ($pIndex*8 + $slotIndex) : ($pIndex*8 + $slotIndex) }}</div>
                                </div>
                            @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        @endif
	</div>
</div>
