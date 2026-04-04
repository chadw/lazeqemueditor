@extends('layouts.app')
@section('title', 'Character Recipes Made')
@section('page-title', 'Character Recipes Made')

@section('content')
    <x-top-links>
        <x-slot name="left">
            @include('characters.recipes.partials.filters')
        </x-slot>
    </x-top-links>

    <x-search-results :items="$recipes" title="Recipes Made">
        <x-ui.table>
            <x-slot:head>
                <tr>
                    <th scope="col">Character</th>
                    <th scope="col">Recipe</th>
                    <th scope="col" class="text-right w-[10%]">Times Made</th>
                </tr>
            </x-slot:head>
            <x-slot:body>
                @forelse ($recipes as $entry)
                    <tr>
                        <td>
                            <a href="{{ route('characters.show', $entry->char_id) }}"
                                class="text-base link-accent link-hover">{{ $entry->character?->name }}</a>
                            <span class="badge badge-sm badge-soft ml-1">{{ $entry->char_id }}</span>
                        </td>
                        <td>
                            <a href="{{ route('tradeskills.edit', $entry->recipe_id) }}"
                                class="text-base link-accent link-hover">{{ $entry->recipe?->name }}</a>
                            <span class="badge badge-sm badge-soft ml-1">{{ $entry->recipe_id }}</span>
                        </td>
                        <td class="text-right">
                            {{ number_format($entry->madecount) }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="text-center text-gray-500">
                            No recipes found.
                        </td>
                    </tr>
                @endforelse
            </x-slot:body>
        </x-ui.table>
    </x-search-results>

    @if ($recipes->hasPages())
        <div class="mt-4">{{ $recipes->links() }}</div>
    @endif

@endsection
