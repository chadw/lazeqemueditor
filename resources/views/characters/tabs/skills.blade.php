<div>
    @php
        $skillsConfig = config('everquest.skills') ?? [];
        $charSkills = collect($character->skills ?? [])->keyBy('skill_id');
        $charLangs = collect($character->languages ?? [])->keyBy('lang_id');
    @endphp

    @if (count($skillsConfig))
        <div class="space-y-4 mt-3">
            @foreach ($skillsConfig as $section => $skills)
                <section class="bg-base-200 rounded p-4">
                    <div class="flex items-center justify-between mb-3">
                        <h4 class="text-lg font-medium">{{ ucwords(str_replace('_', ' ', $section)) }}</h4>
                        <span class="text-sm text-muted">{{ count($skills) }}
                            @if ($section === 'languages')
                                languages
                            @else
                                skills
                            @endif
                        </span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-2">
                        @foreach ($skills as $id => $label)
                            @php
                                if ($section === 'languages') {
                                    $s = $charLangs->get($id);
                                    $value = $s->value ?? $s->proficiency ?? null;
                                    $display = $s ? ($value ?? 'Known') : '-';
                                } else {
                                    $s = $charSkills->get($id);
                                    $value = $s->value ?? $s->current ?? $s->skill_value ?? null;
                                    $display = is_null($value) ? '-' : $value;
                                }
                            @endphp

                            <div class="p-2 bg-base-100 rounded flex items-center gap-3">
                                <div class="text-sm text-muted w-36">{{ $label }}</div>
                                <div class="flex-1 border-b border-dotted border-base-content/20"></div>
                                <div class="w-12 text-right font-medium text-sm">{{ $display }}</div>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endforeach
        </div>
    @else
        <p class="text-muted mt-2">No skills configured.</p>
    @endif
</div>
