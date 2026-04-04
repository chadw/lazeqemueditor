<?php

namespace App\Http\Controllers;

use App\Models\NpcFactionEntry;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class NpcFactionEntryController extends Controller
{
    /**
     * add a new faction hit
     *
     * @param  mixed $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'npc_faction_id' => 'required|integer|exists:npc_faction,id',
            'faction_id'     => 'required|integer|exists:faction_list,id',
            'value'          => 'required|integer',
            'npc_value'      => 'nullable|integer|in:-1,0,1',
            'temp'           => 'nullable|integer|boolean',
        ]);

        $entry = NpcFactionEntry::updateOrCreate(
            [
                'npc_faction_id' => $validated['npc_faction_id'],
                'faction_id'     => $validated['faction_id'],
            ],
            $validated
        );

        return response()->json([
            'success' => true,
            'message' => 'Faction entry added.',
            'entry'   => $entry
        ]);
    }

    /**
     * update an existing faction hit
     *
     * @param  mixed $request
     * @param  mixed $id
     * @return JsonResponse
     */
    public function update(Request $request, $id): JsonResponse
    {
        $validated = $request->validate([
            'value'     => 'required|integer',
            'npc_value' => 'nullable|integer|in:-1,0,1',
        ]);

        $entry = NpcFactionEntry::findOrFail($id);
        $entry->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Faction entry updated.'
        ]);
    }

    /**
     * delete a faction hit
     *
     * @param  mixed $id
     * @return JsonResponse
     */
    public function destroy($id): JsonResponse
    {
        $entry = NpcFactionEntry::findOrFail($id);
        $entry->delete();

        return response()->json([
            'success' => true,
            'message' => 'Faction entry removed.'
        ]);
    }
}
