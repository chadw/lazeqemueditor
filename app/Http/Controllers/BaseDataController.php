<?php

namespace App\Http\Controllers;

use App\Http\Requests\BaseDataRequest;
use App\Models\BaseData;
use Illuminate\Http\Request;

class BaseDataController extends Controller
{
    public function index(Request $request)
    {
        $rows = BaseData::query()
            ->when($request->filled('class'), fn ($q) =>
                $q->where('class', $request->class)
            )
            ->when($request->filled('level'), fn ($q) =>
                $q->where('level', $request->level)
            )
            ->orderBy('class')
            ->orderBy('level')
            ->paginate(50)
            ->withQueryString();

        return view('characters.base-data.index', compact('rows'));
    }

    public function store(BaseDataRequest $request)
    {
        $data = $request->validated();

        $exists = BaseData::where('level', $data['level'])
            ->where('class', $data['class'])
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Character Base Data for this level/class already exists.',
            ], 409);
        }

        $model = BaseData::create($data);

        toast()->success('Saved!', 'Character Base Data created.');

        return response()->json([
            'success' => true,
            'data'    => $model,
            'redirect'=> route('characters.base-data.index'),
            'message' => 'Character Base Data created.',
        ], 201);
    }

    public function update(BaseDataRequest $request, int $level, int $class)
    {
        $row = BaseData::where('level', $level)
            ->where('class', $class)
            ->firstOrFail();

        $data = $request->validated();

        $row->update($data);

        toast()->success('Saved!', 'Character Base Data updated.');

        return response()->json([
            'success' => true,
            'data'    => $row,
            'redirect'=> route('characters.base-data.index'),
            'message' => 'Character Base Data updated.',
        ], 201);
    }

    public function destroy(int $level, int $class)
    {
        BaseData::where('level', $level)
            ->where('class', $class)
            ->delete();

        return back()->with('success', 'Character Base Data deleted.');
    }
}
