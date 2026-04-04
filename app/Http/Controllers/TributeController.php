<?php

namespace App\Http\Controllers;

use App\Http\Requests\TributeRequest;
use App\Models\Tribute;
use Illuminate\Support\Facades\DB;

class TributeController extends Controller
{
    public function index()
    {
        $tributes = Tribute::orderBy('id', 'desc')
            ->with('levels.item')
            ->paginate(50);

        return view('tribute.index', compact('tributes'));
    }

    public function store(TributeRequest $request)
    {
        $validated = $request->validated();

        $nextId = (Tribute::max('id') ?? 0) + 1;

        $tribute = Tribute::create(array_merge($validated, ['id' => $nextId]));

        $message = "Tribute <b>[%s]</b> created.";
        toast()->success('Saved!', $message, [$tribute->name]);

        return back();
    }

    public function update(TributeRequest $request, int $id, int $isguild)
    {
        $validated = $request->validated();

        $tribute = Tribute::where('id', $id)
            ->where('isguild', $isguild)
            ->firstOrFail();

        $tribute->update($validated);
        $tribute->triggerEvent('updated');

        $message = "Tribute <b>[%s]</b> updated.";
        toast()->success('Saved!', $message, [$tribute->name]);

        return back();
    }

    public function destroy(int $id, int $isguild)
    {
        $deleted = false;

        DB::transaction(function () use ($id, $isguild, &$deleted) {
            $tributes = Tribute::where('id', $id)
                ->where('isguild', $isguild)
                ->get();

            foreach ($tributes as $tribute) {
                $tribute->levels()->delete();
                $tribute->delete();
                $deleted = true;
            }
        });

        if ($deleted) {
            return redirect()->route('tribute.index')
                ->with('success', 'Tribute deleted.');
        }

        return redirect()->route('tribute.index')
            ->with('error', 'Tribute not found.');
    }
}
