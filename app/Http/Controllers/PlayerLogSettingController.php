<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DiscordWebhook;
use App\Models\PlayerEventLogSetting;
use App\Http\Requests\PlayerEventLogSettingRequest;

class PlayerLogSettingController extends Controller
{
    public function index(Request $request)
    {
        $settings = PlayerEventLogSetting::orderBy('id')->get();
        $discord_hooks = DiscordWebhook::pluck('webhook_name', 'id')->toArray();

        return view('player-logs.settings.index', compact('settings', 'discord_hooks'));
    }

    public function update(PlayerEventLogSettingRequest $request, PlayerEventLogSetting $setting)
    {
        $setting->{$request->field} = $request->value;
        $setting->save();

        return response()->json(['ok' => true]);
    }
}
