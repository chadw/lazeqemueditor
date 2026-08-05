@extends('layouts.app')
@section('title', 'Achievement Categories')
@section('page-title', 'Achievement Categories')

@section('content')
    @php
        $parentOptions = [0 => 'Root category'] + $categories;
        $showCreate = old('_category_form') === 'create' || request()->boolean('create');
        $suggestedCategoryId = min(
            4294967295,
            max(0, (int) collect($categoryRows)->max('id')) + 1
        );
    @endphp

    <div x-data="{ showCreate: {{ $showCreate ? 'true' : 'false' }} }" class="space-y-5">
        <x-top-links>
            <x-slot name="left">
                <a href="{{ route('achievements.index') }}" class="btn btn-soft"
                    title="Return to achievement definitions" aria-label="Return to achievement definitions">
                    <x-ui.icon name="square-arrow-left" /> Definitions
                </a>
            </x-slot>
            <button type="button" class="btn btn-soft btn-success" @click="showCreate = !showCreate"
                title="Show or hide the new category form" aria-label="Show or hide the new category form">
                <x-ui.icon name="add" /> New Category
            </button>
        </x-top-links>

        <x-ui.alert-info>
            Categories form the client-side achievement tree. ID <strong>0</strong> is reserved, and active
            branches must have a complete, cycle-free parent chain.
        </x-ui.alert-info>

        <div x-show="showCreate" x-collapse x-cloak class="card bg-base-100 shadow">
            <form method="POST" action="{{ route('achievement-categories.store') }}" class="card-body">
                @csrf
                <input type="hidden" name="_category_form" value="create">
                <h2 class="card-title">New Category</h2>
                <div class="grid grid-cols-1 md:grid-cols-6 gap-4">
                    <x-form.input name="id" label="Stable ID" type="number" min="1" max="4294967295"
                        :value="old('_category_form') === 'create' ? old('id') : $suggestedCategoryId"
                        help="Durable nonzero category identity referenced by parent links and achievement associations. The editor suggests max + 1; uniqueness is rechecked transactionally when saved."
                        required />
                    <x-form.select name="parent_id" label="Parent" :options="$parentOptions"
                        :selected="old('_category_form') === 'create' ? old('parent_id', 0) : 0"
                        wrapper-class="md:col-span-2"
                        help="Choose Root for parent ID 0, or place the category beneath an existing cycle-free parent." />
                    <x-form.input name="sequence" label="Sibling order" type="number" min="0" max="4294967295"
                        :value="old('_category_form') === 'create' ? old('sequence', 0) : 0"
                        help="Sort order among categories with the same parent; ties are ordered by category ID." />
                    <x-form.input name="name" label="Name" :value="old('_category_form') === 'create' ? old('name') : ''"
                        wrapper-class="md:col-span-2"
                        help="Player-facing label shown in the achievement category tree." required />
                    <x-form.input name="icon" label="Client texture/resource" :value="old('_category_form') === 'create' ? old('icon') : ''"
                        wrapper-class="md:col-span-2"
                        help="Optional client texture or resource name, such as A_Hunter; empty produces text-only presentation." />
                    <x-form.textarea name="description" label="Description"
                        :value="old('_category_form') === 'create' ? old('description') : ''"
                        wrapper-class="md:col-span-4"
                        help="Description sent to the client for this category." />
                </div>
                <div class="card-actions justify-end">
                    <button type="submit" class="btn btn-soft btn-success">
                        <x-ui.icon name="save" /> Create Category
                    </button>
                </div>
            </form>
        </div>

        @if($editingCategory)
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
            <div class="card bg-base-100 shadow border border-info/30">
                <form method="POST" action="{{ route('achievement-categories.update', $editingCategory->id) }}" class="card-body">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="_category_form" value="edit">
                    <h2 class="card-title">Edit {{ $editingCategory->name }} <span class="badge">#{{ $editingCategory->id }}</span></h2>
                    <div class="grid grid-cols-1 md:grid-cols-6 gap-4">
                        <x-form.input name="id" label="Stable ID" type="number" :value="$editingCategory->id"
                            readonly tooltip="Category identities are immutable after creation."
                            help="Durable category identity; edit other fields or create a replacement instead of changing it." />
                        <x-form.select name="parent_id" label="Parent" :options="$editParents"
                            :selected="old('_category_form') === 'edit' ? old('parent_id') : $editingCategory->parent_id"
                            wrapper-class="md:col-span-2"
                            help="Choose Root for parent ID 0; reparenting cannot make this category its own ancestor." />
                        <x-form.input name="sequence" label="Sibling order" type="number" min="0" max="4294967295"
                            :value="old('_category_form') === 'edit' ? old('sequence') : $editingCategory->sequence"
                            help="Sort order among categories with the same parent; ties are ordered by category ID." />
                        <x-form.input name="name" label="Name"
                            :value="old('_category_form') === 'edit' ? old('name') : $editingCategory->name"
                            wrapper-class="md:col-span-2"
                            help="Player-facing label shown in the achievement category tree." required />
                        <x-form.input name="icon" label="Client texture/resource"
                            :value="old('_category_form') === 'edit' ? old('icon') : $editingCategory->icon"
                            wrapper-class="md:col-span-2"
                            help="Optional client texture or resource name, such as A_Hunter; empty produces text-only presentation." />
                        <x-form.textarea name="description" label="Description"
                            :value="old('_category_form') === 'edit' ? old('description') : $editingCategory->description"
                            wrapper-class="md:col-span-4"
                            help="Description sent to the client for this category." />
                    </div>
                    <div class="card-actions justify-end">
                        <a href="{{ route('achievement-categories.index') }}" class="btn btn-soft">Cancel</a>
                        <button type="submit" class="btn btn-soft btn-success">
                            <x-ui.icon name="save" /> Save Category
                        </button>
                    </div>
                </form>
            </div>
        @endif

        <div>
            <h2 class="card-title mb-4 inline-flex items-center gap-1">
                Category Tree ({{ number_format(count($categoryRows)) }})
                <x-ui.field-help text="The client receives categories with enabled achievement associations plus the ancestor chain needed to reach their roots." />
            </h2>
            <x-ui.table>
                <x-slot:head>
                    <tr>
                        <th class="w-[9%]">ID</th>
                        <th>Name</th>
                        <th class="w-[12%]">
                            <span class="inline-flex items-center gap-1">Parent
                                <x-ui.field-help text="Immediate parent category, or Root when parent ID is 0." />
                            </span>
                        </th>
                        <th class="w-[10%] text-center">
                            <span class="inline-flex items-center gap-1">Order
                                <x-ui.field-help text="Display sequence among categories that share the same parent." />
                            </span>
                        </th>
                        <th class="w-[10%] text-center">
                            <span class="inline-flex items-center gap-1">Definitions
                                <x-ui.field-help text="Number of achievement definitions directly associated with this category." />
                            </span>
                        </th>
                        <th class="w-[10%] text-center">
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
                        @endphp
                        <tr @class(['bg-info/5' => optional($editingCategory)->id === $id])>
                            <td class="tabular-nums">{{ $id }}</td>
                            <td>
                                <div class="flex items-center gap-2" style="padding-left: {{ $depth * 1.25 }}rem">
                                    @if($depth > 0)<span class="opacity-40">↳</span>@endif
                                    <div>
                                        <div class="font-medium">{{ data_get($category, 'name') ?: '(unnamed)' }}</div>
                                        @if(data_get($category, 'description'))
                                            <div class="text-xs opacity-60 line-clamp-1">{{ data_get($category, 'description') }}</div>
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
                                    <a href="{{ route('achievement-categories.index', ['edit' => $id]) }}"
                                        class="join-item btn btn-sm btn-soft tooltip" data-tip="Edit"
                                        title="Edit category {{ $id }}" aria-label="Edit category {{ $id }}">
                                        <x-ui.icon name="edit" />
                                    </a>
                                    <form method="POST" action="{{ route('achievement-categories.destroy', $id) }}"
                                        onsubmit="return confirm('Delete this category? This is allowed only when it has no children or achievement associations.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="join-item btn btn-sm btn-soft btn-error tooltip"
                                            data-tip="Delete" title="Delete category {{ $id }}" aria-label="Delete category {{ $id }}"
                                            @disabled($associationCount > 0 || $childrenCount > 0)>
                                            <x-ui.icon name="delete" />
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center italic opacity-60">No achievement categories found.</td></tr>
                    @endforelse
                </x-slot:body>
            </x-ui.table>
        </div>
    </div>
@endsection
