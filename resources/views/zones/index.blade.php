@extends('layouts.app')
@section('title', 'Zones')
@section('page-title', 'Zones')

@section('content')
    <div x-data @keydown.window.escape="if(!$store.modalForm.saving) $store.modalForm.close()"
         data-zones-root
         x-init="$store.zonesFilter.filter($el)"
         x-effect="$store.zonesFilter.query; $store.zonesFilter.filter($el)">
        <x-top-links>
            <x-slot name="left">
                <div class="w-xs">
                    <label class="label label-text">Filter zones</label>
                    <input
                        type="search"
                        x-model.debounce.300ms="$store.zonesFilter.query"
                        placeholder="name or id, e.g. soldung or 89"
                        class="input w-full"
                    />
                </div>
                @include('zones.partials.filters')
            </x-slot>
        </x-top-links>

        @if ($allZones->isNotEmpty())
            @foreach ($allZones as $k => $zone)
                <div class="collapse collapse-arrow bg-base-100 border-base-300 border mb-4" data-expansion data-expansion-first="{{ $loop->first ? '1' : '0' }}">
                    <input type="checkbox" id="exp-{{ $k }}" {{ $loop->first ? 'checked' : '' }} />
                    <label for="exp-{{ $k }}" class="collapse-title flex items-center justify-between">
                        <span class="text-info/70 text-2xl font-semibold">{{ config('everquest.expansions')[$k] ?? 'Other - ' . $k }}</span>
                        <span class="badge badge-sm badge-soft badge-accent">{{ count($zone) }}</span>
                    </label>
                    <div class="collapse-content">
                        <ul class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                            @foreach ($zone as $val)
                                <li data-zone-item
                                    data-zone-name="{{ strtolower($val->long_name) }}"
                                    data-zone-short="{{ strtolower($val->short_name) }}"
                                    data-zone-id="{{ $val->zoneidnumber }}">
                                    <a href="{{ route('zones.edit', $val->id) }}"
                                        class="block bg-base-100 border border-base-content/10 rounded-lg p-3 transition hover:bg-base-200 shadow-sm">
                                        <div class="flex items-start justify-between">
                                            <div class="text-base text-base-content font-medium">{{ $val->long_name }}</div>
                                            <div class="badge badge-sm badge-soft">ID: {{ $val->zoneidnumber }}</div>
                                        </div>
                                        <div class="text-xs text-info/50 uppercase mt-1">
                                            {{ $val->short_name }}
                                            @if ($val->version > 0)
                                                <span class="text-accent">(v{{ $val->version }})</span>
                                            @endif
                                            @if ($val->zone_exp_multiplier)
                                                - <span class="text-base-content/50">{{ $val->zone_exp_multiplier * 100 }}% exp</span>
                                            @endif
                                        </div>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endforeach
        @endif
    </div>
@endsection
