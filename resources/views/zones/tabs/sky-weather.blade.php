<div class="space-y-6" x-data="formTracker">
    <div class="card bg-base-200 card-sm shadow-sm">
        <div class="card-body">
            <h2 class="card-title">Sky</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-2">
                <x-form.input
                    name="sky"
                    label="Sky"
                    type="number"
                    :value="$zone->sky"
                />
                <x-form.input
                    name="skylock"
                    label="Sky Lock"
                    type="number"
                    :value="$zone->skylock"
                />
                <x-form.input
                    name="fog_density"
                    label="Fog Density"
                    type="number"
                    step="any"
                    :value="$zone->fog_density"
                />
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                <x-form.input
                    name="minclip"
                    label="Min Clip"
                    type="number"
                    step="any"
                    :value="$zone->minclip"
                />
                <x-form.input
                    name="maxclip"
                    label="Max Clip"
                    type="number"
                    step="any"
                    :value="$zone->maxclip"
                />
            </div>
        </div>
    </div>

    <div class="card bg-base-200 card-sm shadow-sm">
        <div class="card-body">
            <h2 class="card-title">Fog Clips</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mt-2">
                <div>
                    <div class="text-sm text-base-content/70 mb-2">Fog Clip 0</div>
                    <div class="grid grid-cols-2 gap-2">
                        <x-form.input
                            name="fog_minclip"
                            label="Min"
                            type="number"
                            step="any"
                            :value="$zone->fog_minclip"
                        />
                        <x-form.input
                            name="fog_maxclip"
                            label="Max"
                            type="number"
                            step="any"
                            :value="$zone->fog_maxclip"
                        />
                    </div>
                </div>

                @for ($i = 1; $i <= 4; $i++)
                    <div>
                        <div class="text-sm text-base-content/70 mb-2">Fog Clip {{ $i }}</div>
                        <div class="grid grid-cols-2 gap-2">
                            <x-form.input
                                name="fog_minclip{{ $i }}"
                                label="Min"
                                type="number"
                                step="any"
                                :value="$zone->{'fog_minclip'.$i} ?? ''"
                            />
                            <x-form.input
                                name="fog_maxclip{{ $i }}"
                                label="Max"
                                type="number"
                                step="any"
                                :value="$zone->{'fog_maxclip'.$i} ?? ''"
                            />
                        </div>
                    </div>
                @endfor
            </div>
        </div>
    </div>

    <div class="card bg-base-200 card-sm shadow-sm">
        <div class="card-body">
            <h2 class="card-title">Fog Colors</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mt-2">
                <div>
                    <div class="text-sm text-base-content/70 mb-2">Fog Colors 0</div>
                    <div class="grid grid-cols-3 gap-2">
                        <x-form.input
                            name="fog_red"
                            label="R"
                            type="number"
                            :value="$zone->fog_red"
                        />
                        <x-form.input
                            name="fog_green"
                            label="G"
                            type="number"
                            :value="$zone->fog_green"
                        />
                        <x-form.input
                            name="fog_blue"
                            label="B"
                            type="number"
                            :value="$zone->fog_blue"
                        />
                    </div>
                </div>

                @for ($i = 1; $i <= 4; $i++)
                    <div>
                        <div class="text-sm text-base-content/70 mb-2">Fog Colors {{ $i }}</div>
                        <div class="grid grid-cols-3 gap-2">
                            <x-form.input
                                name="fog_red{{ $i }}"
                                label="R"
                                type="number"
                                :value="$zone->{'fog_red'.$i} ?? ''"
                            />
                            <x-form.input
                                name="fog_green{{ $i }}"
                                label="G"
                                type="number"
                                :value="$zone->{'fog_green'.$i} ?? ''"
                            />
                            <x-form.input
                                name="fog_blue{{ $i }}"
                                label="B"
                                type="number"
                                :value="$zone->{'fog_blue'.$i} ?? ''"
                            />
                        </div>
                    </div>
                @endfor
            </div>
        </div>
    </div>

    <div class="card bg-base-200 card-sm shadow-sm">
        <div class="card-body">
            <h2 class="card-title">Weather</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-2">
                <div>
                    <div class="text-sm text-base-content/70 mb-2">Rain (Chance / Duration)</div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                        @for ($i = 1; $i <= 4; $i++)
                            <div class="grid grid-cols-2 gap-2">
                                <x-form.input
                                    name="rain_chance{{ $i }}"
                                    label="Chance {{ $i }}"
                                    type="number"
                                    :value="$zone->{'rain_chance'.$i} ?? ''"
                                />
                                <x-form.input
                                    name="rain_duration{{ $i }}"
                                    label="Duration {{ $i }}"
                                    type="number"
                                    :value="$zone->{'rain_duration'.$i} ?? ''"
                                />
                            </div>
                        @endfor
                    </div>
                </div>

                <div>
                    <div class="text-sm text-base-content/70 mb-2">Snow (Chance / Duration)</div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                        @for ($i = 1; $i <= 4; $i++)
                            <div class="grid grid-cols-2 gap-2">
                                <x-form.input
                                    name="snow_chance{{ $i }}"
                                    label="Chance {{ $i }}"
                                    type="number"
                                    :Value="$zone->{'snow_chance'.$i} ?? ''"
                                />
                                <x-form.input
                                    name="snow_duration{{ $i }}"
                                    label="Duration {{ $i }}"
                                    type="number"
                                    :value="$zone->{'snow_duration'.$i} ?? ''"
                                />
                            </div>
                        @endfor
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
