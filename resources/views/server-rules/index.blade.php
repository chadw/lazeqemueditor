@extends('layouts.app')
@section('title', 'Server Rules')
@section('page-title', 'Server Rules')

@section('content')
    <div x-data="{ searchTerm: '' }">
        <x-top-links>
            <x-slot name="left">
                <div class="flex gap-2 items-end">
                    <div class="w-200">
                        <label class="label label-text">Filter Rule</label>
                        <input type="text" placeholder="Search rules..."
                            x-model="searchTerm"
                            x-on:input="$dispatch('filter-rules', searchTerm)"
                            class="input w-full"
                        />
                    </div>
                </div>
            </x-slot>
        </x-top-links>

        <x-ui.table
            :tbody-attributes="[
                'x-data' => \Illuminate\Support\Js::from(['searchTerm' => '']),
                'x-on:filter-rules.window' => 'searchTerm = $event.detail'
            ]"
            height="overflow-x-auto max-h-[75vh] overflow-y-auto" theadsticky="top-0 z-10"
        >
            <x-slot:head>
                <tr>
                    <th scope="col" class="w-[5%]">RID</th>
                    <th scope="col" class="w-[20%]">Name</th>
                    <th scope="col" class="w-[10%]">Value</th>
                    <th scope="col">Description</th>
                </tr>
            </x-slot:head>
            <x-slot:body>
                @foreach ($rules as $rule)
                    <tr x-data="inlineField({
                        field: 'rule_value',
                        value: '{{ $rule->rule_value }}',
                        updateUrl: '{{ route('server-rules.update', rawurlencode($rule->rule_name)) }}',
                    })"
                        x-show="
                            !searchTerm ||
                            @js($rule->rule_name).toLowerCase().includes(searchTerm.toLowerCase()) ||
                            @js($rule->notes).toLowerCase().includes(searchTerm.toLowerCase())">
                        <td>{{ $rule->ruleset_id }}</td>
                        <td>{{ $rule->rule_name }}</td>
                        <td>
                            <template x-if="isBoolean()">
                                <input type="checkbox" class="checkbox" :checked="value === 'true'"
                                    x-on:change="save($event.target.checked ? 'true' : 'false')" />
                            </template>
                            <template x-if="!isBoolean()">
                                <input type="text" class="input w-full" :value="value"
                                    @blur="save($event.target.value)" />
                            </template>

                            <span x-cloak x-show="saving" class="loading loading-xs"></span>
                            <span x-cloak x-show="saved" class="text-success text-xs">✓</span>
                            <span x-cloak x-show="error" class="text-error text-xs">error</span>
                        </td>
                        <td>{{ $rule->notes }}</td>
                    </tr>
                @endforeach
            </x-slot:body>
        </x-ui.table>
    </div>
@endsection
