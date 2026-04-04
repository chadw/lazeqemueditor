<?php

namespace App\Http\Controllers;

use App\Models\Title;
use Illuminate\Http\Request;
use App\Http\Requests\TitleRequest;

class TitleController extends Controller
{
    public function index(Request $request)
    {
        $titles = Title::with('character', 'item')
            ->orderBy('id', 'asc')
            ->get();

        return view('titles.index', compact('titles'));
    }

    public function store(TitleRequest $request)
    {
        $data = $request->validated();

        $model = Title::create($data);
        toast()->success('Saved!', 'Title created.');

        return response()->json([
            'success'  => true,
            'data'     => $model->fresh(),
            'redirect' => url()->previous(),
        ], 200);
    }

    public function update(TitleRequest $request, Title $title)
    {
        $data = $request->validated();

        $title->update($data);
        toast()->success('Saved!', 'Title updated.');

        return response()->json([
            'success'  => true,
            'data'     => $title->fresh(),
            'redirect' => url()->previous(),
        ], 200);
    }

    public function destroy(Title $title)
    {
        $title->delete();
        toast()->success('Deleted!', 'Title deleted.');

        return back();
    }
}
