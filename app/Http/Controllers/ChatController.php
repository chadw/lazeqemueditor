<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use Illuminate\Http\Request;
use App\Http\Requests\ChatRequest;

class ChatController extends Controller
{
    public function index(Request $request)
    {
        $chats = Chat::select('id', 'name', 'owner', 'minstatus')->orderBy('id', 'asc')->get();

        return view('chats.index', compact('chats'));
    }

    public function store(ChatRequest $request)
    {
        $data = $request->validated();
        $data['password'] = (string) ($data['password'] ?? "");
        $data['minstatus'] = (int) ($data['minstatus'] ?? 0);

        $model = Chat::create($data);

        toast()->success('Saved!', 'Chat Channel created.');

        return response()->json([
            'success' => true,
            'data'    => $model,
            'redirect'=> route('chats.index'),
        ], 201);
    }

    public function update(ChatRequest $request, Chat $chat)
    {
        $data = $request->validated();
        $data['password'] = (string) ($data['password'] ?? "");
        $data['minstatus'] = (int) ($data['minstatus'] ?? 0);

        $chat->update($data);

        toast()->success('Saved!', 'Chat Channel updated.');

        return response()->json([
            'success' => true,
            'data'    => $chat,
            'redirect'=> route('chats.index'),
        ], 201);
    }

    public function destroy(Chat $chat)
    {
        $chat->delete();

        return back()->with('success', 'Chat Channel deleted.');
    }
}
