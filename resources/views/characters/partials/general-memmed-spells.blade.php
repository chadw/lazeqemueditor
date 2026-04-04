<div class="bg-base-200 p-4 rounded mb-4">
    <div class="text-lg font-medium mb-2">Memmed Spells</div>
    @php
        $memMap = $character->memmedSpellMap ?? [];
    @endphp

    <div class="grid grid-cols-6 md:grid-cols-12 gap-2">
        @for ($i = 1; $i <= 12; $i++)
            @php
                $m = $memMap[$i] ?? null;
                $spell = $m && ($m->spell ?? null) ? $m->spell : null;
            @endphp
            <div class="w-full h-12 flex items-center justify-center bg-base-100 rounded-full border border-base-content/10">
                @if ($spell)
                    <x-spell-link-stacked
                        :spell_id="$spell->id"
                        :spell_icon="$spell->new_icon"
                        :spell_target_type="$spell->targettype"
                        :icon_only="1"
                        :effects_only="1"
                    />
                @else
                    <div class="w-8 h-8 bg-base-200 border border-base-content/20 rounded"></div>
                @endif
            </div>
        @endfor
    </div>
</div>
