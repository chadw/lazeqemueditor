<?php

namespace App\Http\Controllers;

use App\Models\LdonTrapTemplate;
use Illuminate\Http\Request;

class LdonTrapTemplateController extends Controller
{
    public function index()
    {
        $templates = LdonTrapTemplate::withCount('entries')
            ->with('spell')
            ->paginate(50);

        return view('ldon-trap-templates.index', compact('templates'));
    }

    public function edit(LdonTrapTemplate $trapTemplate)
    {
        $trapTemplate = LdonTrapTemplate::with(['spell', 'entries'])
            ->where('id', $trapTemplate->id)
            ->firstOrFail();

        return view('ldon-trap-templates.edit', compact('trapTemplate'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'type' => 'required|integer',
            'spell_id' => 'nullable|integer',
            'skill' => 'nullable|integer',
            'locked' => 'boolean',
        ]);

        LdonTrapTemplate::create($data);

        return redirect()->route('ldon.trap-templates.index')
            ->with('success', 'Trap template created.');
    }

    public function update(Request $request, LdonTrapTemplate $trapTemplate)
    {
        $data = $request->validate([
            'type' => 'required|integer',
            'spell_id' => 'nullable|integer',
            'skill' => 'nullable|integer',
            'locked' => 'boolean',
        ]);

        $trapTemplate->update($data);

        return redirect()->route('ldon.trap-templates.index')
            ->with('success', 'Trap template updated.');
    }

    public function destroy(LdonTrapTemplate $trapTemplate)
    {
        $trapTemplate->delete();

        return back()->with('success', 'Trap template deleted.');
    }
}
