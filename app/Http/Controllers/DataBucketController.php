<?php

namespace App\Http\Controllers;

use App\Filters\DataBucketFilter;
use App\Http\Requests\DataBucketRequest;
use App\Models\DataBucket;
use Illuminate\Http\Request;

class DataBucketController extends Controller
{
    public function index(Request $request)
    {
        $databuckets = (new DataBucketFilter($request))
            ->apply(DataBucket::query())
            -> with([
                'account',
                'character',
                'npc',
                'zone',
            ])
            ->orderBy('id')
            ->paginate(100)
            ->withQueryString();

        return view('databuckets.index', compact('databuckets'));
    }

    public function store(DataBucketRequest $request)
    {
        $data = $request->validated();

        $model = DataBucket::create($data);

        toast()->success('Saved!', 'Data Bucket created.');

        return response()->json([
            'success' => true,
            'data'    => $model,
            'redirect'=> route('databuckets.index'),
        ], 201);
    }

    public function update(DataBucketRequest $request, DataBucket $databucket)
    {
        $data = $request->validated();

        $databucket->update($data);

        toast()->success('Saved!', 'Data Bucket updated.');

        return response()->json([
            'success' => true,
            'data'    => $databucket,
            'redirect'=> route('databuckets.index'),
        ], 201);
    }

    public function destroy(DataBucket $databucket)
    {
        $databucket->delete();

        return back()->with('success', 'Data Bucket deleted.');
    }
}
