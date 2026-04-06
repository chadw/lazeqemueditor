@extends('layouts.app')
@section('title', 'Tradeskill Recipes')
@section('page-title', 'Tradeskill Recipes')

@section('content')
    <div x-data @keydown.window.escape="if(!$store.modalForm.saving) $store.modalForm.close()">

        <x-top-links>
            <x-slot name="left">
                @include('tradeskills.partials.filters')
            </x-slot>
            <button type="button" class="btn btn-soft btn-success"
                @click="$store.modalForm.openCreate({
                baseUrl: '{{ route('tradeskills.store') }}',
                resourceName: 'Recipe',
                defaults: {
                    skillneeded: 0,
                    trivial: 0,
                    enabled: true,
                }
            })">
                <x-ui.icon name="add" /> New Recipe
            </button>
        </x-top-links>

        <x-search-results :items="$recipes" title="Tradeskill Recipes">
            <x-ui.table>
                <x-slot:head>
                    <tr>
                        <th scope="col" class="w-[5%]">@sortablelink('id', 'ID')</th>
                        <th scope="col">@sortablelink('name', 'Name')</th>
                        <th scope="col" class="w-[20%]">Result</th>
                        <th scope="col" class="w-[10%]">@sortablelink('tradeskill', 'Tradeskill')</th>
                        <th scope="col" class="w-[5%]">@sortablelink('skillneeded', 'Skill')</th>
                        <th scope="col" class="w-[5%]">@sortablelink('trivial', 'Trivial')</th>
                        <th class="w-[5%]">Enabled</th>
                        <th class="w-[15%] text-right">Actions</th>
                    </tr>
                </x-slot:head>
                <x-slot:body>
                    @forelse ($recipes as $recipe)
                        <tr>
                            <td>{{ $recipe->id }}</td>
                            <td>
                                <a href="{{ route('tradeskills.edit', $recipe) }}"
                                    class="text-base link-info link-hover">
                                    {{ $recipe->name }}
                                </a>
                            </td>
                            <td>
                                @php $results = $recipe->resultEntries ?? collect(); @endphp
                                @if ($results->isNotEmpty())
                                    @php $first = $results->first(); $more = $results->count() - 1; @endphp
                                    <div x-data="{ open: false }" class="flex items-center gap-2">
                                        <div x-data="{ open: false }" class="flex flex-col">
                                            <div class="flex items-center gap-2">
                                                @if ($first->item)
                                                    <div class="flex items-center gap-2">
                                                        <x-item-link
                                                            :item_id="$first->item->id"
                                                            :item_name="$first->item->Name"
                                                            :item_icon="$first->item->icon"
                                                            item_class="flex"
                                                        />
                                                    </div>
                                                @endif

                                                @if($more > 0)
                                                    <button x-show="!open" class="btn btn-xs btn-soft" @click.prevent="open = true">
                                                        +{{ $more }} more
                                                    </button>
                                                @endif
                                            </div>

                                            @if($more > 0)
                                                <div x-show="open" x-cloak class="w-full mt-1">
                                                    <div class="flex flex-wrap gap-2">
                                                        @foreach($results->slice(1) as $r)
                                                            @if($r->item)
                                                                <div class="flex items-center gap-2">
                                                                    <x-item-link
                                                                        :item_id="$r->item->id"
                                                                        :item_name="$r->item->Name"
                                                                        :item_icon="$r->item->icon"
                                                                        item_class="flex"
                                                                    />
                                                                </div>
                                                            @endif
                                                        @endforeach
                                                    </div>

                                                    <div class="mt-1">
                                                        <button class="btn btn-xs btn-soft" @click.prevent="open = false">show less</button>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                @else
                                    —
                                @endif
                            </td>
                            <td>{{ $tradeskills[$recipe->tradeskill] ?? $recipe->tradeskill }}</td>
                            <td>{{ $recipe->skillneeded }}</td>
                            <td>{{ $recipe->trivial }}</td>
                            <td>
                                @if ($recipe->enabled)
                                    <span class="badge badge-soft badge-success">Yes</span>
                                @else
                                    <span class="badge badge-soft badge-error">No</span>
                                @endif
                            </td>
                            <td class="text-right space-x-2">
                                <div class="join">
                                    <form method="POST" action="{{ route('tradeskills.clone', $recipe) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-soft btn-info tooltip" data-tip="Clone">
                                            <x-ui.icon name="clone" />
                                        </button>
                                    </form>
                                    <a href="{{ route('tradeskills.edit', $recipe) }}" data-tip="Edit"
                                        class="join-item btn btn-sm btn-soft tooltip">
                                        <x-ui.icon name="edit" />
                                    </a>
                                    <form action="{{ route('tradeskills.destroy', $recipe) }}" method="POST" class="inline"
                                        onsubmit="return confirm('Delete this recipe?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="join-item btn btn-sm btn-soft btn-error tooltip" data-tip="Delete">
                                            <x-ui.icon name="delete" />
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-6 text-base-content/50">
                                No tradeskill recipes found.
                            </td>
                        </tr>
                    @endforelse
                </x-slot:body>
            </x-ui.table>
        </x-search-results>

        <div class="mt-4 shrink-0">{{ $recipes->links() }}</div>

        <x-modal-form>
            @include('tradeskills.forms.form')
        </x-modal-form>
    </div>
@endsection
