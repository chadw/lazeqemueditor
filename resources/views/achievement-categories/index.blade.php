@extends('layouts.app')
@section('title', 'Achievement Categories')
@section('page-title', 'Achievement Categories')

@section('content')
    @php
        $parentOptions = [0 => 'Root category'] + $categories;
        $showCreate = old('_category_form') === 'create' || request()->boolean('create');
        $suggestedCategoryId = min(4294967295, max(0, (int) collect($categoryRows)->max('id')) + 1);
    @endphp
    <div x-data @keydown.window.escape="if(!$store.modalForm.saving) $store.modalForm.close()">
        <x-top-links>
            <x-slot name="left">
                <a href="{{ route('achievements.index') }}" class="btn btn-soft"
                    title="Return to achievement definitions" aria-label="Return to achievement definitions">
                    <x-ui.icon name="square-arrow-left" /> Definitions
                </a>
            </x-slot>
            <button type="button" class="btn btn-soft btn-success float-end" title="Show or hide the new category form"
                aria-label="Show or hide the new category form"
                @click="$store.modalForm.openCreate({
                        baseUrl: '{{ route('achievement-categories.store') }}',
                        resourceName: 'Achievement Category',
                        defaults: {
                            id: {{ $suggestedCategoryId }},
                            parent_id: 0,
                            sequence: 0,
                            name: '',
                            icon: '',
                            description: '',
                        }
                    })">
                <x-ui.icon name="add" /> New Category
            </button>
        </x-top-links>

        <x-ui.alert-info>
            Categories form the client-side achievement tree. ID <strong>0</strong> is reserved, and active
            branches must have a complete, cycle-free parent chain.
        </x-ui.alert-info>

        @if ($editingCategory)
            @php
                $blockedParentIds = [(int) $editingCategory->id => true];
                do {
                    $foundDescendant = false;
                    foreach ($categoryRows as $candidate) {
                        $candidateId = (int) data_get($candidate, 'id');
                        $candidateParentId = (int) data_get($candidate, 'parent_id');
                        if (isset($blockedParentIds[$candidateParentId]) && !isset($blockedParentIds[$candidateId])) {
                            $blockedParentIds[$candidateId] = true;
                            $foundDescendant = true;
                        }
                    }
                } while ($foundDescendant);
                $editParents = collect($parentOptions)->except(array_keys($blockedParentIds))->all();
            @endphp
        @endif

        @php
            $formParentOptions = $editingCategory ? $editParents : $parentOptions;
        @endphp

        <div x-data>
            <h2 class="card-title mb-4 inline-flex items-center gap-1">
                Category Tree ({{ number_format(count($categoryRows)) }})
                <x-ui.field-help
                    text="The client receives categories with enabled achievement associations plus the ancestor chain needed to reach their roots." />
            </h2>
            <x-ui.table>
                <x-slot:head>
                    <tr>
                        <th class="w-[5%]">ID</th>
                        <th>Name</th>
                        <th class="w-[5%]">
                            <span class="inline-flex items-center gap-1">Parent
                                <x-ui.field-help text="Immediate parent category, or Root when parent ID is 0." />
                            </span>
                        </th>
                        <th class="w-[5%] text-center">
                            <span class="inline-flex items-center gap-1">Order
                                <x-ui.field-help text="Display sequence among categories that share the same parent." />
                            </span>
                        </th>
                        <th class="w-[5%] text-center">
                            <span class="inline-flex items-center gap-1">Definitions
                                <x-ui.field-help
                                    text="Number of achievement definitions directly associated with this category." />
                            </span>
                        </th>
                        <th class="w-[5%] text-center">
                            <span class="inline-flex items-center gap-1">Children
                                <x-ui.field-help text="Number of categories whose immediate parent is this category." />
                            </span>
                        </th>
                        <th class="w-[12%] text-right">-</th>
                    </tr>
                </x-slot:head>
                <x-slot:body>
                    @forelse($categoryRows as $category)
                        @php
                            $id = (int) data_get($category, 'id');
                            $depth = (int) data_get($category, 'depth', 0);
                            $associationCount = (int) data_get($category, 'associations_count', 0);
                            $childrenCount = (int) data_get($category, 'children_count', 0);
                            // compute blocked parent ids for this category (itself and descendants)
                            $blockedParentIdsRow = [$id => true];
                            do {
                                $foundDescendantRow = false;
                                foreach ($categoryRows as $candidateRow) {
                                    $candidateIdRow = (int) data_get($candidateRow, 'id');
                                    $candidateParentIdRow = (int) data_get($candidateRow, 'parent_id');
                                    if (
                                        isset($blockedParentIdsRow[$candidateParentIdRow]) &&
                                        !isset($blockedParentIdsRow[$candidateIdRow])
                                    ) {
                                        $blockedParentIdsRow[$candidateIdRow] = true;
                                        $foundDescendantRow = true;
                                    }
                                }
                            } while ($foundDescendantRow);
                            $blockedParentIdsList = array_values(array_keys($blockedParentIdsRow));
                        @endphp
                        <tr @class(['bg-info/5' => optional($editingCategory)->id === $id])
                            data-category='@json($category)'
                            data-blocked='@json($blockedParentIdsList)'
                        >
                            <td class="tabular-nums">{{ $id }}</td>
                            <td>
                                <div class="flex items-center gap-2" style="padding-left: {{ $depth * 1.25 }}rem">
                                    @if ($depth > 0)
                                        <span class="opacity-40">↳</span>
                                    @endif
                                    <div>
                                        <div class="font-medium">{{ data_get($category, 'name') ?: '(unnamed)' }}</div>
                                        @if (data_get($category, 'description'))
                                            <div class="text-xs opacity-60 line-clamp-1">
                                                {{ data_get($category, 'description') }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="tabular-nums">{{ data_get($category, 'parent_id') ?: 'Root' }}</td>
                            <td class="text-center tabular-nums">{{ data_get($category, 'sequence') }}</td>
                            <td class="text-center"><span class="badge badge-soft">{{ $associationCount }}</span></td>
                            <td class="text-center"><span class="badge badge-soft">{{ $childrenCount }}</span></td>
                            <td class="text-right">
                                <div class="join">
                                    <button type="button" class="join-item btn btn-sm btn-soft tooltip" data-tip="Edit"
                                        title="Edit category {{ $id }}"
                                        aria-label="Edit category {{ $id }}"
                                        @click="$store.modalForm.openEdit(
                                            $el.closest('tr').dataset.category,
                                            '{{ route('achievement-categories.update', $id) }}',
                                            {
                                                resourceName: 'Achievement Category',
                                            }
                                        )">
                                        <x-ui.icon name="edit" />
                                    </button>
                                    <form action="{{ route('achievement-categories.destroy', $id) }}" method="POST" class="inline">
                                        @csrf @method('DELETE')
                                        <button class="join-item btn btn-sm btn-soft btn-error"
                                            onclick="return confirm('Delete this category? This is allowed only when it has no children or achievement associations.?')"
                                            @disabled($associationCount > 0 || $childrenCount > 0)
                                        >
                                            <x-ui.icon name="delete" />
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center italic opacity-60">No achievement categories found.</td>
                        </tr>
                    @endforelse
                </x-slot:body>
            </x-ui.table>
        </div>

        <x-modal-form>
            @include('achievement-categories.forms.form', ['parentOptions' => $formParentOptions])
        </x-modal-form>
    </div>
@endsection
