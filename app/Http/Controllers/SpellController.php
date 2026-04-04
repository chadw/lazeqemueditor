<?php

namespace App\Http\Controllers;

use App\Models\Pet;
use App\Models\Aura;
use App\Models\Zone;
use App\Models\DbStr;
use App\Models\Horse;
use App\Models\Spell;
use App\Filters\SpellFilter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\SpellRequest;
use Illuminate\Support\Facades\Cache;
use Spatie\DiscordAlerts\Facades\DiscordAlert;
use Illuminate\Support\Facades\Auth;
use App\Jobs\RefreshAllSpellsCache;

class SpellController extends Controller
{
    protected $allSpells;
    protected $allZones;

    public function __construct()
    {
        $this->allSpells = collect(Cache::rememberForever('all_spells', function () {
            return Spell::pluck('name', 'id');
        }));

        $this->allZones = Cache::rememberForever('all_zones', function () {
            return Zone::select('id', 'short_name', 'long_name', 'expansion')
                ->orderBy('id')
                ->get()
                ->unique('short_name')
                ->keyBy('short_name');
        });

        view()->share('allSpells', $this->allSpells);
        view()->share('allZones', $this->allZones);
    }

    public function index(Request $request, SpellFilter $filter)
    {
        $spells = $filter
            ->apply(Spell::query())
            ->orderBy('id')
            ->paginate(50)
            ->withQueryString();

        return view('spells.index', compact('spells'));
    }

    public function edit(Spell $spell)
    {
        $dbstr = DbStr::where('type', 5)
            ->select('id', 'value')
            ->limit(100000)
            ->pluck('value', 'id')
            ->toArray();

        $spell->load([
            'recourseLink',
            'comp1', 'comp2', 'comp3', 'comp4',
            'reagent1', 'reagent2', 'reagent3', 'reagent4',
        ]);

        $dbstrDesc = DbStr::where('type', 6)
            ->where('id', $spell->descnum)
            ->first();

        return view('spells.edit', compact('spell', 'dbstr', 'dbstrDesc'));
    }

    public function update(SpellRequest $request, Spell $spell)
    {
        $input = $request->except(['_token', '_method', 'spell_tabs']);

        $newId = isset($input['id']) ? (int)$input['id'] : (int)$spell->id;
        if ($newId !== (int)$spell->id) {
            $existing = Spell::find($newId);
            if ($existing && !$request->boolean('confirm_id_replace')) {
                return redirect()->back()->withInput()->with('id_conflict', [
                    'id' => $existing->id,
                    'name' => $existing->name,
                ]);
            }
        }

        $changes = [];
        DB::connection('eqemu')->transaction(function () use ($input, $spell, &$changes, $newId, $request) {
            if (isset($input['id']) && (int)$input['id'] !== (int)$spell->id) {
                $targetId = (int)$input['id'];
                if (Spell::where('id', $targetId)->exists()) {
                    if ($request->boolean('confirm_id_replace')) {
                        Spell::where('id', $targetId)->delete();
                    }
                }
            }

            $spell->fill($input);

            if (isset($input['id']) && (int)$input['id'] !== (int)$spell->getOriginal('id')) {
                $spell->id = (int)$input['id'];
            }

            $dirty = $spell->getDirty();
            foreach ($dirty as $key => $new) {
                $old = $spell->getOriginal($key);
                $changes[$key] = ['old' => $old, 'new' => $new];
            }

            if (!empty($changes)) {
                $spell->save();
            }
        });

        try {
            Cache::forget('all_spells');
            RefreshAllSpellsCache::dispatch();
        } catch (\Throwable $e) {
        }

        return redirect()
            ->route('spells.edit', $spell->id)
            ->with('status', count($changes) ? 'Spell updated' : 'No changes detected');
    }

    public function tz(Request $request)
    {
        $type = $request->query('type');
        $allowed = ['zones', 'pets', 'horses', 'auras'];
        if (!in_array($type, $allowed)) {
            return response()->json(['data' => []]);
        }

        if ($type === 'zones') {
            $data = Zone::select('zoneidnumber', 'short_name', 'long_name')
                ->groupBy('short_name')
                ->orderBy('short_name')
                ->get();

            return response()->json(['data' => $data]);
        } elseif ($type === 'pets') {
            $data = Pet::select('id', 'type', 'npcID')->get();

            return response()->json(['data' => $data]);
        } elseif ($type == 'auras') {
            $data = Aura::select('name', 'aura_type', 'distance')->get();

            return response()->json(['data' => $data]);
        } elseif ($type === 'horses') {
            $data = Horse::select('filename', 'race', 'gender', 'texture', 'mountspeed', 'notes')->get();

            return response()->json(['data' => $data]);
        }
    }

    public function search(Request $request)
    {
        $search = $request->string('q');

        if ($request->filled('ids')) {
            $raw = $request->input('ids');
            $ids = array_values(array_filter(array_map('trim', explode(',', (string)$raw)), function ($v) {
                return $v !== '' && is_numeric($v);
            }));

            if (count($ids) === 0) {
                return response()->json([]);
            }

            return Spell::query()
                ->select('id', 'name', 'new_icon')
                ->whereIn('id', $ids)
                ->orderBy('id')
                ->get();
        }

        if ($request->filled('id')) {
            $id = $request->input('id');

            $result = Spell::query()
                ->select('id', 'name', 'new_icon')
                ->where('id', $id)
                ->first();

            return response()->json($result);
        }

        return Spell::query()
            ->select('id', 'name', 'new_icon')
            ->when($search, function ($q) use ($search) {
                $q->where('id', $search)
                ->orWhere('name', 'like', "%{$search}%");
            })
            ->orderBy('id')
            ->limit(50)
            ->get();
    }

    public function popup(Spell $spell)
    {
        $effectsOnly = request()->boolean('effects-only');

        $spell = Spell::where('id', $spell->id)->firstOrFail();

        return response()->json([
            'html' => view('spells.partials.popup', [
                'spell' => $spell,
                'effectsOnly' => $effectsOnly,
            ])->render()
        ]);
    }

    public function preview(Request $request)
    {
        $data = $request->all();

        $normalize = function ($val) use (&$normalize) {
            if (!is_array($val)) {
                return $val;
            }

            $last = null;
            foreach (array_reverse($val) as $v) {
                if ($v === null) {
                    continue;
                }
                if (is_string($v) && trim($v) === '') {
                    continue;
                }
                if (is_array($v)) {
                    $last = $normalize($v);
                    if ($last !== null && $last !== '') {
                        break;
                    }
                    continue;
                }

                $last = $v;
                break;
            }

            if ($last === null && count($val) > 0) {
                $first = reset($val);
                return is_array($first) ? $normalize($first) : $first;
            }

            return $last;
        };

        $normalized = [];
        foreach ($data as $k => $v) {
            $normalized[$k] = $normalize($v);
        }

        $spell = null;
        $id = isset($normalized['id']) ? (int)$normalized['id'] : null;
        if ($id) {
            $orig = Spell::find($id);
            if ($orig) {
                $spell = $orig->replicate();
                $spell->setRawAttributes($orig->getAttributes(), true);
            }
        }

        if (!$spell) {
            $spell = new Spell();
        }

        foreach ($normalized as $k => $v) {
            if (is_string($v) && is_numeric($v)) {
                $v = (strpos($v, '.') !== false) ? (float)$v : (int)$v;
            }

            $spell->{$k} = $v;
        }

        $allSpells = $this->allSpells ?? Spell::pluck('name', 'id');
        $allZones = $this->allZones ?? [];

        $html = view('spells.partials.preview-spell', [
            'spell' => $spell,
            'effectsOnly' => false,
            'allSpells' => $allSpells,
            'allZones' => $allZones,
            'dbstr_desc' => null,
        ])->render();

        return response()->json(['html' => $html]);
    }

    public function defaults(int $spa)
    {
        $query = Spell::query();

        $query->where(function ($q) use ($spa) {
            for ($i = 1; $i <= 12; $i++) {
                $q->orWhere("effectid{$i}", $spa);
            }
        });

        $spell = $query->first();

        if (!$spell) {
            return response()->json(null);
        }

        $matchedIndex = null;

        for ($i = 1; $i <= 12; $i++) {
            if ((int)$spell->{"effectid{$i}"} === $spa) {
                $matchedIndex = $i;
                break;
            }
        }

        if (!$matchedIndex) {
            return response()->json(null);
        }

        return response()->json([
            'slot'    => $matchedIndex,
            'base'    => $spell->{"effect_base_value{$matchedIndex}"},
            'limit'   => $spell->{"effect_limit_value{$matchedIndex}"},
            'max'     => $spell->{"max{$matchedIndex}"},
            'formula' => $spell->{"formula{$matchedIndex}"},
        ]);
    }

    public function clone(Request $request, Spell $spell)
    {
        $new = $spell->replicate();

        $suffix = ' (Copy)';
        $newName = $spell->name . $suffix;

        if (Spell::where('name', $newName)->exists()) {
            $newName = $spell->name . $suffix . ' ' . now()->format('YmdHis');
        }
        $new->name = $newName;

        $newId = null;
        DB::connection('eqemu')->transaction(function () use (&$new, &$newId) {
            $table = $new->getTable();
            $max = DB::connection('eqemu')->table($table)->lockForUpdate()->max('id');
            $newId = (($max ?? 0) + 1);
            $new->id = $newId;
            $new->save();
        });

        try {
            Cache::forget('all_spells');
            RefreshAllSpellsCache::dispatch();
        } catch (\Throwable $e) {
        }

        $redirect = $request->input('redirect', 'edit');
        if ($redirect === 'index') {
            return back()->with('success', 'Spell cloned.')->with('new_id', $newId);
            //return redirect()->route('spells.index')->with('status', 'Spell cloned')->with('new_id', $newId);
        }

        return redirect()->route('spells.edit', $new)->with('status', 'Spell cloned');
    }

    public function destroy(Request $request, Spell $spell)
    {
        $attrs = $spell->getAttributes();
        $spellId = $attrs['id'] ?? $spell->id;
        $spellName = $attrs['name'] ?? $spell->name ?? 'Unknown';

        $spell->delete();

        try {
            Cache::forget('all_spells');
            RefreshAllSpellsCache::dispatch();
        } catch (\Throwable $e) {
        }

        try {
            $u = Auth::user();
            $user = $u ? $u->name : 'System';
            $message = "[DELETED] [Spell] - **User**: {$user}, **id:** {$spellId}, **name:** {$spellName}";
            DiscordAlert::message($message);
        } catch (\Throwable $e) {
        }

        $previous = url()->previous();

        if (str_ends_with($previous, "/spells/{$spellId}/edit")) {
            return redirect()
                ->route('spells.index')
                ->with('success', 'Spell deleted.');
        }

        return redirect()
            ->back()
            ->with('success', 'Spell deleted.');
    }

    public function effects(Request $request)
    {
        $search = $request->string('q')->trim();

        $effects = collect(config('everquest.spell_effects', []));

        $items = $effects->map(function ($label, $id) {
            return [
                'value' => (string)$id,
                'label' => sprintf('%s - %s', $id, $label),
            ];
        });

        if ($search !== '') {
            $q = mb_strtolower($search);
            $items = $items->filter(function ($item) use ($q) {
                return mb_stripos($item['label'], $q) !== false || mb_stripos((string)$item['value'], $q) !== false;
            });
        }

        $items = $items->values()->take(500)->all();

        return response()->json($items);
    }

    public function spelleffects()
    {
        return response()->json(config('everquest.spell_effects'));
    }

    public function spelleffect($id)
    {
        $s = config('everquest.spell_effects');

        return response()->json([
            'id' => $id,
            'label' => $s[$id] ?? null,
        ]);
    }

    public function animationsList(Request $request)
    {
        $type = $request->query('type', 'spell');

        $dirs = [
            'spell'  => public_path('spell-animations'),
            'player' => public_path('player-animations'),
        ];

        abort_unless(isset($dirs[$type]), 404);

        $result = [];

        foreach (scandir($dirs[$type]) as $f) {
            if (str_ends_with(strtolower($f), '.mp4')) {
                $result[] = pathinfo($f, PATHINFO_FILENAME);
            }
        }

        sort($result, SORT_NATURAL);

        return response()->json(array_values($result));
    }
}
