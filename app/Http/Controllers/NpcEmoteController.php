<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\NpcEmote;
use App\Http\Requests\NpcEmoteRequest;

class NpcEmoteController extends Controller
{
    public function index(Request $request)
    {
        $npcEmotes = NpcEmote::orderBy('id')
            ->paginate(100)
            ->withQueryString();

        return view('npc-emotes.index', compact('npcEmotes'));
    }

    public function store(NpcEmoteRequest $request)
    {
        $data = $request->validated();

        $model = NpcEmote::create($data);
        toast()->success('Saved!', 'NPC Emote created.');

        return response()->json([
            'success'  => true,
            'data'     => $model->fresh(),
            'redirect' => url()->previous(),
        ], 201);
    }

    public function update(NpcEmoteRequest $request, NpcEmote $npcEmote)
    {
        $data = $request->validated();

        $npcEmote->update($data);
        toast()->success('Saved!', 'NPC Emote updated.');

        return response()->json([
            'success'  => true,
            'data'     => $npcEmote->fresh(),
            'redirect' => url()->previous(),
        ], 201);
    }

    public function destroy(NpcEmote $npcEmote)
    {
        $npcEmote->delete();
        toast()->success('Deleted!', 'NPC Emote deleted.');

        return back();
    }
}
