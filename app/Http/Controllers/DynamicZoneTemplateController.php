<?php

namespace App\Http\Controllers;

use App\Http\Requests\DynamicZoneTemplateRequest;
use App\Models\DynamicZoneTemplate;
use App\Models\Zone;
use Illuminate\Http\Request;

class DynamicZoneTemplateController extends Controller
{
    public function index(Request $request)
    {
        $templates = DynamicZoneTemplate::with('zone', 'compassZone', 'returnZone')
            ->orderBy('id')
            ->paginate(50)
            ->withQueryString();

        $zones = Zone::selectZones();

        return view('dynamiczones.templates.index', compact('templates', 'zones'));
    }

    public function store(DynamicZoneTemplateRequest $request)
    {
        $model = DynamicZoneTemplate::create($request->validated());
        toast()->success('Saved!', "DZ Template created.");

        return response()->json([
            'success' => true,
            'data'    => $model->fresh(),
            'redirect'=> url()->previous(),
        ], 200);
    }

    public function update(DynamicZoneTemplateRequest $request, DynamicZoneTemplate $template)
    {
        $template->update($request->validated());
        toast()->success('Saved!', "DZ Template updated.");

        return response()->json([
            'success' => true,
            'data'    => $template->fresh(),
            'redirect'=> url()->previous(),
        ], 200);
    }

    public function destroy(DynamicZoneTemplate $template)
    {
        $template->delete();
        toast()->success('Saved!', "DZ Template deleted.");

        return back();
    }
}
