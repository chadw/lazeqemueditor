@extends('layouts.app')
@section('title', 'AA Edit')
@section('page-title', 'AA Edit')

@section('content')
    <div x-data @keydown.window.escape="if(!$store.modalForm.saving) $store.modalForm.close()">

        <x-top-links>
            <x-slot name="left">
                @include('aa.partials.filters')
            </x-slot>
            <a href="{{ route('aa.create') }}" class="btn btn-soft btn-success">
                <x-ui.icon name="add" /> New AA
            </a>
        </x-top-links>

        <x-search-results :items="$abilities" title="AA Abilities">
            <x-ui.table>
                <x-slot:head>
                    <tr>
                        <th scope="col" class="w-[10%]">ID</th>
                        <th scope="col">Name</th>
                        <th scope="col">Classes</th>
                        <th scope="col" class="w-[15%]">Category</th>
                        <th scope="col" class="w-[10%]">Enabled</th>
                        <th scope="col" class="w-[10%] text-right">-</th>
                    </tr>
                </x-slot:head>
                <x-slot:body>
                    @foreach ($abilities as $ability)
                        <tr>
                            <td>{{ $ability->id }}</td>
                            <td class="font-medium">{{ $ability->name }}</td>
                            <td>
                                @php
                                    if (($ability->classes ?? 0) === 65535) {
                                        $abilityClassesStr = 'ALL';
                                    } else {
                                        $abilityClasses = [];
                                        foreach (config('everquest.classes_bit_abbr') as $bit => $label) {
                                            if ($ability->classes & $bit) {
                                                $abilityClasses[] = $label;
                                            }
                                        }
                                        $abilityClassesStr = implode(', ', $abilityClasses);
                                    }
                                @endphp
                                {{ $abilityClassesStr }}
                            </td>
                            <td>{{ config('everquest.aa_categories.' . $ability->category) }}</td>
                            <td>
                                <span class="badge badge-soft {{ $ability->enabled ? 'badge-success' : 'badge-error' }}">
                                    {{ $ability->enabled ? 'Yes' : 'No' }}
                                </span>
                            </td>
                            <td class="text-right">
                                <a href="{{ route('aa.edit', $ability) }}" class="btn btn-sm btn-soft tooltip"
                                    data-tip="Edit">
                                    <x-ui.icon name="edit" />
                                </a>
                                <form action="{{ route('aa.destroy', $ability) }}" method="POST" class="inline">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-soft btn-error tooltip" data-tip="Delete"
                                        onclick="return confirm('Delete this AA and all related ranks?')">
                                        <x-ui.icon name="delete" />
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </x-slot:body>
            </x-ui.table>
        </x-search-results>

        <div class="mt-4">
            {{ $abilities->links() }}
        </div>
    </div>
@endsection
