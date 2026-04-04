<?php

namespace App\Http\Controllers;

use App\Models\DbStr;
use Illuminate\Http\Request;
use App\Http\Requests\DbStrRequest;

class DbstrController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->query('dbtype');

        $dbstrCounts = DbStr::query()
            ->selectRaw('type, COUNT(id) as count')
            ->groupBy('type')
            ->pluck('count', 'type');

        $typeOptions = collect(config('everquest.dbstr_types'))
            ->mapWithKeys(function ($label, $key) use ($dbstrCounts) {
                $count = $dbstrCounts[$key] ?? 0;
                return [
                    $key => "{$label} ({$count})"
                ];
            })
            ->toArray();

        $dbstrs = collect();
        if ($type !== null) {
            $type = (int) $type;

            $dbstrs = DbStr::query()
                ->where('type', $type)
                ->orderBy('id')
                ->paginate(500)
                ->withQueryString();
        }

        return view('dbstr.index', [
            'dbstrs'      => $dbstrs,
            'type'        => $type,
            'typeOptions' => $typeOptions,
        ]);
    }

    public function store(DbStrRequest $request)
    {
        $data = $request->validated();

        if (empty($data['id'])) {
            $maxId = DbStr::where('type', $data['type'])->max('id') ?? 0;
            $data['id'] = $maxId + 1;
        }

        $model = DbStr::create($data);

        toast()->success('Saved!', 'DBStr created.');

        return response()->json([
            'success'  => true,
            'data'     => $model,
            'redirect' => url()->previous(),
        ], 201);
    }

    public function update(DbStrRequest $request, int $type, int $id)
    {
        $data = $request->validated();

        $model = DbStr::where('type', $type)->where('id', $id)->firstOrFail();
        $model->value = $data['value'];
        $model->save();

        toast()->success('Saved!', 'DBStr updated.');

        return response()->json([
            'success'  => true,
            'data'     => $model,
            'redirect' => url()->previous(),
        ], 201);
    }

    public function destroy(int $type, int $id)
    {
        $model = DbStr::where('type', $type)->where('id', $id)->first();
        if ($model) {
            $model->delete();
        }

        return back()->with('success', 'DBStr deleted.');
    }

    public function lookup(Request $request)
    {
        $type = (int) $request->query('type');
        $id = (int) $request->query('id');

        if ($id <= 0) {
            return response()->json(['value' => null]);
        }

        $dbstr = DbStr::where('type', $type)
            ->where('id', $id)
            ->first();

        return response()->json(['value' => $dbstr ? $dbstr->value : null]);
    }

    public function search(Request $request)
    {
        $type = (int) $request->query('type', 6);
        $q = $request->query('q', '');

        $query = DbStr::where('type', $type);

        if ($q !== '') {
            $query->where(function ($sub) use ($q) {
                $sub->where('id', $q)
                    ->orWhere('value', 'like', '%' . $q . '%');
            });
        }

        return $query->orderBy('id')
            ->paginate(50)
            ->withQueryString();
    }

    public function nextId(Request $request)
    {
        $type = (int) $request->query('type', 6);

        $maxId = DbStr::where('type', $type)->max('id') ?? 0;

        return response()->json(['next_id' => $maxId + 1]);
    }
}
