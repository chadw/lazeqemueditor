<form method="get" action="{{ route('spells.index') }}" class="mb-6">
    <div class="space-y-6">
        @if ($errors->any())
        <div>
            @error('class')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>
        @endif
        <div>
            <label class="input w-full">
                <span>By Name</span>
                <input type="text" id="name" name="name" value="{{ request('name') }}"
                    class="w-full input" placeholder="Searches spells by name" />
            </label>
        </div>
        <div class="flex flex-wrap gap-4">
            <div class="flex flex-col">
                <label class="select">
                    <span class="label">Class</span>
                    <select id="classes" name="class" class="select">
                        <option value="">-</option>
                        @foreach([
                            8 => 'Bard',
                            15 => 'Beastlord',
                            16 => 'Berserker',
                            2 => 'Cleric',
                            6 => 'Druid',
                            14 => 'Enchanter',
                            13 => 'Magician',
                            7 => 'Monk',
                            11 => 'Necromancer',
                            3 => 'Paladin',
                            4 => 'Ranger',
                            9 => 'Rogue',
                            5 => 'Shadowknight',
                            10 => 'Shaman',
                            1 => 'Warrior',
                            12 => 'Wizard',
                        ] as $value => $label)
                        <option value="{{ $value }}" {{ request('class') == $value ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                        @endforeach
                    </select>
                </label>
            </div>
            <div class="flex flex-col">
                <label class="select">
                    <span class="label">Level</span>
                    <select name="level" id="level" class="select">
                        @for ($i = 1; $i <= config('everquest.server_max_level', 70); $i++)
                            <option value="{{ $i }}" {{ request('level') == $i ? 'selected' : '' }}>{{ $i }}</option>
                        @endfor
                    </select>
                </label>
            </div>
            <div class="flex flex-col">
                <label class="select">
                    <span class="label">Options</span>
                    <select name="opt" id="opts" class="select">
                        <option value="1"{{ request('opt', 2) == 1 ? ' selected' : '' }}>Only</option>
                        <option value="2"{{ request('opt', 2) == 2 ? ' selected' : '' }}>And Higher</option>
                        <option value="3"{{ request('opt', 2) == 3 ? ' selected' : '' }}>And Lower</option>
                    </select>
                </label>
            </div>
        </div>
        <div class="pt-4">
            <button type="submit" class="btn btn-soft">
                Search
            </button>
            <a href="{{ route('spells.index') }}" class="btn btn-soft btn-error">
                Reset
            </a>
        </div>
    </div>
</form>
