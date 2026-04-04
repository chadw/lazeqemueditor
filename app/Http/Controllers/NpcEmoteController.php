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

        NpcEmote::create($data);

        return back()->with('success', 'NPC Emote created.');
    }

    public function update(NpcEmoteRequest $request, NpcEmote $npcEmote)
    {
        $data = $request->validated();

        $npcEmote->update($data);

        return back()->with('success', 'NPC Emote updated.');
    }

    public function destroy(NpcEmote $npcEmote)
    {
        $npcEmote->delete();

        return back()->with('success', 'NPC Emote deleted.');
    }
}
