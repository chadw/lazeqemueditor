<div x-data="zoneSelector({zone: @js($selectedZoneId), version: @js($selectedVersion)})" class="flex gap-4">
    <div class="w-96">
        <label class="label label-text">Zone</label>
        <select class="select w-full" @change="changeZone($event.target.value)">
            <option value="">Select Zone</option>
            @foreach ($zones as $z)
                <option value="{{ $z->id }}" @selected($selectedZoneId == $z->zoneidnumber)>
                    {{ $z->zoneidnumber }}: {{ $z->short_name }} - {{ $z->long_name }}
                </option>
            @endforeach
        </select>
    </div>
    @if (request()->has('zone'))
        <div class="w-32">
            <label class="label label-text">Version</label>
            <select class="select w-full" x-show="zone" @change="changeVersion($event.target.value)">
                @foreach ($versions as $v)
                    <option value="{{ $v->id }}" @selected($selectedVersion === $v->id)>
                        v{{ $v->version }}
                    </option>
                @endforeach
            </select>
        </div>
    @endif
</div>
