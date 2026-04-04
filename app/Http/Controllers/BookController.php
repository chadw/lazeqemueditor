<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use App\Http\Requests\BookRequest;
use App\Filters\BookFilter;

class BookController extends Controller
{
    public function index(Request $request)
    {
        $books = (new BookFilter($request))
            ->apply(Book::query()->with('item'))
            ->sortable(['id' => 'asc'])
            ->paginate(100)
            ->withQueryString();

        return view('books.index', compact('books'));
    }

    public function store(BookRequest $request)
    {
        $data = $request->validated();

        $model = Book::create($data);

        toast()->success('Saved!', 'Book created.');

        return response()->json([
            'success' => true,
            'data'    => $model,
            'redirect'=> route('books.index'),
        ], 201);
    }

    public function update(BookRequest $request, Book $book)
    {
        $data = $request->validated();

        $book->update($data);

        toast()->success('Saved!', 'Book updated.');

        return response()->json([
            'success' => true,
            'data'    => $book,
            'redirect'=> route('books.index'),
        ], 201);
    }

    public function destroy(Book $book)
    {
        $book->delete();

        return back()->with('success', 'Book deleted.');
    }
}
