<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContentFlagRequest;
use App\Models\ContentFlag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ContentFlagController extends Controller
{
    public function index(Request $request)
    {
        $contentFlags = ContentFlag::orderBy('id')->get();

        return view('content-flags.index', compact('contentFlags'));
    }

    public function store(ContentFlagRequest $request)
    {
        $data = $request->validated();

        $model = ContentFlag::create($data);
        Cache::forget('content_flags_all');
        Cache::put('content_flags_all', ContentFlag::orderBy('flag_name')->get());

        toast()->success('Saved!', 'Content Flag created.');

        return response()->json([
            'success' => true,
            'data'    => $model,
            'redirect'=> route('content-flags.index'),
        ], 201);
    }

    public function update(ContentFlagRequest $request, ContentFlag $contentFlag)
    {
        $data = $request->validated();

        $contentFlag->update($data);
        Cache::forget('content_flags_all');
        Cache::put('content_flags_all', ContentFlag::orderBy('flag_name')->get());

        toast()->success('Saved!', 'Content Flag updated.');

        return response()->json([
            'success' => true,
            'data'    => $contentFlag,
            'redirect'=> route('content-flags.index'),
        ], 201);
    }

    public function destroy(ContentFlag $contentFlag)
    {
        $contentFlag->delete();
        Cache::forget('content_flags_all');
        Cache::put('content_flags_all', ContentFlag::orderBy('flag_name')->get());

        return back()->with('success', 'Content Flag deleted.');
    }
}
