@extends('layouts.app')
@section('title', 'Books')
@section('page-title', 'Books')

@section('content')
    <div x-data @keydown.window.escape="if(!$store.modalForm.saving) $store.modalForm.close()">

        <x-top-links>
            <x-slot name="left">
                @include('books.partials.filters')
            </x-slot>
            <button type="button" class="btn btn-soft btn-success float-end"
                @click="$store.modalForm.openCreate({
                    baseUrl: '{{ route('books.store') }}',
                    resourceName: 'Book'
                })">
                <x-ui.icon name="add" /> New Book
            </button>
        </x-top-links>

        <x-search-results :items="$books" title="Books">
            <x-ui.table>
                <x-slot:head>
                    <tr>
                        <th scope="col" class="w-[5%]">@sortablelink('id', 'ID')</th>
                        <th scope="col" class="w-[10%]">@sortablelink('name', 'Name')</th>
                        <th scope="col" class="w-[30%]">Items</th>
                        <th scope="col">Text</th>
                        <th scope="col" class="w-[10%]">Language</th>
                        <th scope="col" class="w-[10%] text-right">-</th>
                    </tr>
                </x-slot:head>
                <x-slot:body>
                    @forelse ($books as $book)
                        <tr x-data data-book='@json($book)'>
                            <td>{{ $book->id }}</td>
                            <td>{{ $book->name }}</td>
                            <td>
                                @foreach ($book->item as $item)
                                    <x-item-link
                                        :item_id="$item->id"
                                        :item_name="$item->Name"
                                        :item_icon="$item->icon"
                                        item_class="inline-flex"
                                    />
                                @endforeach
                            </td>
                            <td class="truncate">
                                {{ Str::limit($book->txtfile, 150, ' ...') }}
                            </td>
                            <td>
                                @if ($book->language >= 0)
                                    {{ config('everquest.skills.languages')[$book->language] }}
                                @else
                                    Unknown ({{ $book->language }})
                                @endif
                            </td>
                            <td class="text-right">
                                <div class="inline join">
                                    <button type="button" class="join-item btn btn-sm btn-soft tooltip" data-tip="Edit"
                                        @click="$store.modalForm.openEdit(
                                            $el.closest('tr').dataset.book,
                                            '{{ route('books.update', $book) }}',
                                            { resourceName: 'Edit Book' }
                                        )">
                                        <x-ui.icon name="edit" />
                                    </button>
                                    <form action="{{ route('books.destroy', $book) }}" method="POST" class="inline">
                                        @csrf @method('DELETE')
                                        <button class="join-item btn btn-sm btn-soft btn-error tooltip" data-tip="Delete"
                                            onclick="return confirm('Delete?')">
                                            <x-ui.icon name="delete" />
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-6 text-base-content/50">
                                No books found.
                            </td>
                        </tr>
                    @endforelse
                </x-slot:body>
            </x-ui.table>
        </x-search-results>

        <div class="mt-4">{{ $books->links() }}</div>

        <x-modal-form>
            @include('books.forms.form')
        </x-modal-form>
    </div>
@endsection
