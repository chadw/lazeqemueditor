<?php

namespace App\Http\Controllers;

use App\Filters\ParcelFilter;
use App\Http\Requests\ParcelRequest;
use App\Models\CharacterParcel;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Throwable;

class ParcelController extends Controller
{
    public function index(Request $request)
    {
        $sortable = ['id', 'character', 'item', 'from_name', 'sent_date'];
        $sort = $request->input('sort', 'sent_date');
        $direction = $request->input('direction', 'desc');
        if (!in_array($sort, $sortable)) {
            $sort = 'sent_date';
        }

        $query = CharacterParcel::query()
            ->with(['character', 'item', 'container.item', 'aug1', 'aug2', 'aug3', 'aug4', 'aug5', 'aug6']);

        if ($sort === 'character') {
            $query->leftJoin('character_data', 'character_data.id', '=', 'character_parcels.char_id')
                ->orderBy('character_data.name', $direction)
                ->select('character_parcels.*');
        } elseif ($sort === 'item') {
            $query->leftJoin('items', 'items.id', '=', 'character_parcels.item_id')
                ->orderBy('items.Name', $direction)
                ->select('character_parcels.*');
        } else {
            $query->orderBy("character_parcels.{$sort}", $direction);
        }

        $parcels = (new ParcelFilter($request))
            ->apply($query)
            ->paginate(50)
            ->withQueryString();

        return view('parcels.index', compact('parcels'));
    }

    public function store(ParcelRequest $request)
    {
        $data = $request->validated();

        if (empty($data['sent_date'])) {
            $data['sent_date'] = now();
        }

        $charId = (int) $data['char_id'];

        $tries = 0;
        $maxTries = 5;
        do {
            $tries++;
            $maxSlot = CharacterParcel::where('char_id', $charId)->max('slot_id');
            $candidate = max(1, (int) $maxSlot + 1);

            $data['slot_id'] = $candidate;

            try {
                $model = CharacterParcel::create($data);
                toast()->success('Saved!', "Parcel created.");

                return response()->json([
                    'success' => true,
                    'data'    => $model,
                    'redirect'=> url()->previous(),
                ], 201);

            } catch (QueryException $ex) {
                if ($tries >= $maxTries) {
                    throw $ex;
                }
            } catch (Throwable $t) {
                throw $t;
            }
        } while ($tries < $maxTries);

        return back()->with('error', 'Unable to create parcel.');
    }

    public function update(ParcelRequest $request, CharacterParcel $parcel)
    {
        $data = $request->validated();

        if (isset($data['slot_id'])) {
            unset($data['slot_id']);
        }

        $parcel->update($data);

        toast()->success('Saved!', "Parcel updated.");

        return response()->json([
            'success' => true,
            'data'    => $parcel,
            'redirect'=> url()->previous(),
        ], 201);
    }

    public function destroy(CharacterParcel $parcel)
    {
        $parcel->delete();

        return back()->with('success', 'Parcel deleted.');
    }
}
