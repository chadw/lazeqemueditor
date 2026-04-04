@extends('layouts.app')

@section('title', 'Player Event Log Settings')
@section('page-title', 'Player Event Log Settings')

@section('content')
    <x-ui.table>
        <x-slot:head>
            <tr>
                <th scope="col" class="w-[5%]">ID</th>
                <th scope="col">Event</th>
                <th scope="col" class="w-[5%]">Enabled</th>
                <th scope="col" class="w-[5%]">ETL Enabled</th>
                <th scope="col">Retention</th>
                <th scope="col">Discord Webhook</th>
            </tr>
        </x-slot:head>
        <x-slot:body>
            @foreach ($settings as $setting)
                <tr>
                    <td>{{ $setting->id }}</td>
                    <td class="font-medium">{{ $setting->event_name }}</td>
                    <td x-data="inlineField({
                        value: {{ $setting->event_enabled ? 1 : 0 }},
                        field: 'event_enabled',
                        updateUrl: '{{ route('player-logs.settings.update', $setting) }}'
                    })">
                        <input type="checkbox" class="checkbox"
                            :class="{
                                'checkbox-success': (saved || value == 1),
                                'checkbox-error': error,
                                'opacity-70': saving
                            }"
                            :checked="value == 1" @change="save($event.target.checked ? 1 : 0)" />
                        <x-inline-status />
                    </td>
                    <td x-data="inlineField({
                        value: {{ $setting->etl_enabled ? 1 : 0 }},
                        field: 'etl_enabled',
                        updateUrl: '{{ route('player-logs.settings.update', $setting) }}'
                    })">
                        <input type="checkbox" class="checkbox"
                            :class="{
                                'checkbox-success': (saved || value == 1),
                                'checkbox-error': error,
                                'opacity-70': saving
                            }"
                            :checked="value == 1" @change="save($event.target.checked ? 1 : 0)" />

                        <x-inline-status />
                    </td>
                    <td x-data="inlineField({
                        value: {{ $setting->retention_days }},
                        field: 'retention_days',
                        updateUrl: '{{ route('player-logs.settings.update', $setting) }}'
                    })">
                        <select class="select w-full"
                            :class="{
                                'select-success': saved,
                                'select-error': error,
                                'opacity-70': saving
                            }"
                            :value="value" @change="save(parseInt($event.target.value))">
                            <option value="0">(0) Forever</option>
                            <option value="7">(7) 1 Week</option>
                            <option value="14">(14) 2 Weeks</option>
                            <option value="30">(30) 1 Month</option>
                            <option value="90">(90) 3 Months</option>
                            <option value="180">(180) 6 Months</option>
                            <option value="365">(365) 1 Year</option>
                        </select>

                        <x-inline-status type="select" />
                    </td>
                    <td x-data="inlineField({
                        value: {{ $setting->discord_webhook_id }},
                        field: 'discord_webhook_id',
                        updateUrl: '{{ route('player-logs.settings.update', $setting) }}'
                    })">
                        <select class="select w-full"
                            :class="{
                                'select-success': saved,
                                'select-error': error,
                                'opacity-70': saving
                            }"
                            :value="value" @change="save(parseInt($event.target.value))">
                            <option value="0">None</option>
                            @foreach ($discord_hooks as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>

                        <x-inline-status type="select" />
                    </td>
                </tr>
            @endforeach
        </x-slot:body>
    </x-ui.table>
@endsection
