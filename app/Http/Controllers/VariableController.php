<?php

namespace App\Http\Controllers;

use App\Models\Variable;
use Illuminate\Http\Request;
use App\Http\Requests\VariableRequest;

class VariableController extends Controller
{
    public function index(Request $request)
    {
        $variables = Variable::orderBy('id', 'asc')
            ->get();

        return view('variables.index', compact('variables'));
    }

    public function update(VariableRequest $request, Variable $variable)
    {
        $data = $request->validated();
        $data['varname'] = $variable->varname;

        if (array_key_exists('ts', $data) && $request->filled('ts') === false) {
            unset($data['ts']);
        }

        $variable->update($data);
        toast()->success('Saved!', 'Variable updated.');

        return response()->json([
            'success'  => true,
            'data'     => $variable->fresh(),
            'redirect' => url()->previous(),
        ], 200);
    }
}
