<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CharacterExpeditionLockout;
use App\Http\Requests\CharacterExpeditionLockoutRequest;
use App\Filters\CharacterExpeditionLockoutFilter;

class CharacterExpeditionLockoutController extends Controller
{
    public function index(Request $request)
    {
        $query = CharacterExpeditionLockout::with([
            'character' => fn ($q) => $q->select('id', 'name')
        ]);

        $lockouts = (new CharacterExpeditionLockoutFilter($request))
            ->apply($query)
            ->sortable('expire_time', 'desc')
            ->paginate(50)
            ->withQueryString();

        return view('dynamiczones.character-lockouts.index', compact('lockouts'));
    }

    public function store(CharacterExpeditionLockoutRequest $request)
    {
        $data = $request->validated();

        if (array_key_exists('expire_time', $data) && $data['expire_time'] === '') {
            unset($data['expire_time']);
        }

        $model = CharacterExpeditionLockout::create($data);
        toast()->success('Saved!', "Character DZ Lockout created.");

        return response()->json([
            'success' => true,
            'data'    => $model->fresh(),
            'redirect'=> url()->previous(),
        ], 200);
    }

    public function update(CharacterExpeditionLockoutRequest $request, CharacterExpeditionLockout $lockout)
    {
        $data = $request->validated();
        $data['character_id'] = $lockout->character_id;
        $data['expedition_name'] = $lockout->expedition_name;
        $data['from_expedition_uuid'] = $lockout->from_expedition_uuid;

        if (array_key_exists('expire_time', $data) && $data['expire_time'] === '') {
            unset($data['expire_time']);
        }

        $lockout->update($data);
        toast()->success('Saved!', "Character DZ Lockout updated.");

        return response()->json([
            'success' => true,
            'data'    => $lockout->fresh(),
            'redirect'=> url()->previous(),
        ], 200);
    }

    public function destroy(CharacterExpeditionLockout $lockout)
    {
        $lockout->delete();
        toast()->success('Saved!', "Character DZ Lockout deleted.");

        return back();
    }

    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'ids'   => 'required|array',
        ]);

        $ids = $request->input('ids', []);

        $lockouts = CharacterExpeditionLockout::whereIn('id', $ids)->get();

        foreach ($lockouts as $lockout) {
            $lockout->delete();
        }

        return back()->with('success', count($lockouts) . ' Character DZ Lockouts deleted.');
    }
}
