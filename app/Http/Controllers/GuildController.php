<?php

namespace App\Http\Controllers;

use App\Models\Guild;
use App\Filters\GuildFilter;
use App\Models\GuildMember;
use Illuminate\Http\Request;

class GuildController extends Controller
{
    public function index(Request $request)
    {
        $guilds = (new GuildFilter($request))
            ->apply(Guild::query())
            ->with('leaderCharacter')
            ->withCount('members')
            ->sortable(['name' => 'asc'])
            ->paginate(100)
            ->withQueryString();

        return view('guilds.index', compact('guilds'));
    }

    public function show(Guild $guild, Request $request)
    {
        $membersQuery = GuildMember::query()
            ->where('guild_id', $guild->id)
            ->with([
                'character' => function ($query) {
                    $query->select(['id', 'name', 'race', 'class', 'level', 'anon', 'gm']);
                },
                'guildRank' => function ($query) use($guild) {
                    $query->where('guild_id', $guild->id)->select('guild_id', 'rank', 'title');
                }
            ])
            ->sortable(['name' => 'asc']);

        $members = $membersQuery->paginate(100)->withQueryString();

        $guild->load('leaderCharacter', 'ranks', 'permissions', 'bank.item');

        $avgLevel = GuildMember::where('guild_id', $guild->id)
            ->join('character_data as c', 'guild_members.char_id', '=', 'c.id')
            ->avg('c.level');

        return view('guilds.show', [
            'guild' => $guild,
            'members' => $members,
            'avgLevel' => round($avgLevel, 0),
        ]);
    }

    public function storeMember(Request $request, Guild $guild)
    {
        $data = $request->validate([
            'char_id' => ['required', 'exists:eqemu.character_data,id'],
        ]);

        // prevent duplicates
        if ($guild->members()->where('char_id', $data['char_id'])->exists()) {
            return response()->json([
                'message' => 'Character is already in this guild.'
            ], 422);
        }

        $member = $guild->members()->create([
            'char_id' => $data['char_id'],
            'rank' => 5,
            'tribute_enable' => 0,
            'total_tribute' => 0,
            'last_tribute' => 0,
            'banker' => 0,
            'public_note' => '',
            'alt' => 0,
            'online' => 0,
        ]);

        $member->loadMissing('character');
        $memberName = $member->character->name ?? ('#' . $data['char_id']);

        toast()->success('Saved!', 'Add [' . $memberName . '] to guild [' . $guild->name . '].');

        return response()->json([
            'success'  => true,
            'data'     => $member,
            'redirect' => url()->previous(),
        ], 201);
    }

    public function destroyMember(Guild $guild, GuildMember $member)
    {
        if ($member->guild_id !== $guild->id) {
            abort(403);
        }

        $member->delete();

        return response()->json(['status' => 'ok']);
    }
}
