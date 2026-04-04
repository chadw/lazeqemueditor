<?php

namespace App\Http\Controllers;

use App\Models\DiscordWebhook;
use Illuminate\Http\Request;
use App\Http\Requests\DiscordWebhookRequest;

class DiscordWebhookController extends Controller
{
    public function index(Request $request)
    {
        $hooks = DiscordWebhook::orderBy('id')
            ->get();

        return view('discord-webhooks.index', compact('hooks'));
    }

    public function store(DiscordWebhookRequest $request)
    {
        $model = DiscordWebhook::create($request->validated());
        toast()->success('Saved!', 'Discord Webhook created.');

        return response()->json([
            'success' => true,
            'data'    => $model,
            'redirect'=> route('discord-webhooks.index'),
        ], 201);
    }

    public function update(DiscordWebhookRequest $request, DiscordWebhook $hook)
    {
        $hook->update($request->validated());
        toast()->success('Saved!', 'Discord Webhook updated.');

        return response()->json([
            'success' => true,
            'data'    => $hook,
            'redirect'=> route('discord-webhooks.index'),
        ], 201);
    }

    public function destroy(DiscordWebhook $hook)
    {
        $hook->delete();
        toast()->success('Deleted!', 'Discord Webhook deleted.');

        return back();
    }
}
