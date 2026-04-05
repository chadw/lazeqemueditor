<?php

namespace App\Http\Controllers;

use App\Filters\ItemFilter;
use App\Http\Requests\ItemRequest;
use App\Models\FactionList;
use App\Models\Item;
use App\Models\RuleValue;
use App\Models\Zone;
use App\Support\ObjectSprite;
use App\ViewModels\ItemViewModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Spatie\DiscordAlerts\Facades\DiscordAlert;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $items = collect();

        if ($request->query->count() > 0) {
            $query = (new ItemFilter($request))->apply(Item::query());

            $items = $query
                ->select([
                    'id', 'Name', 'icon', 'itemtype', 'ac', 'hp', 'damage', 'delay',
                    'augtype', 'slots', 'bagslots', 'bagwr', 'created', 'updated'
                ])
                ->orderBy('id')
                ->paginate(50)
                ->withQueryString();
        }

        $lastUpdated = Item::select([
            'id', 'Name', 'icon', 'itemtype', 'ac', 'hp', 'damage', 'delay',
            'augtype', 'slots', 'bagslots', 'bagwr', 'created', 'updated'
        ])
            ->orderBy('updated', 'desc')
            ->limit(50)
            ->get();

        return view('items.index', compact('items', 'lastUpdated'));
    }

    public function edit(Item $item)
    {
        $factions = FactionList::select('id', 'name')
            ->orderBy('name', 'asc')
            ->pluck('name', 'id')
            ->toArray();

        (new ItemViewModel($item))->withEffects();

        $item->load([
            'augDistillerItem',
            'evolvingDetails.item',
        ]);

        // worn "stacking" option
        $wornAdditiveType = (int) RuleValue::where('rule_name', 'Spells:AdditiveBonusWornType')
            ->value('rule_value') ?? 0;

        // objects for modal
        $objectIds = ObjectSprite::ids();

        $zones = Zone::baseZones();

        return view('items.edit', compact(
            'item', 'factions', 'wornAdditiveType', 'objectIds', 'zones'
        ));
    }

    public function store(ItemRequest $request)
    {
        $data = $request->validated();

        Item::create($data);

        return redirect()->route('items.index')->with('success','Item created.');
    }

    public function update(ItemRequest $request, Item $item)
    {
        $input = $request->except(['_token', '_method', 'item_tabs']);

        $newId = isset($input['id']) ? (int)$input['id'] : (int)$item->id;
        if ($newId !== (int)$item->id) {
            $existing = Item::find($newId);
            if ($existing && !$request->boolean('confirm_id_replace')) {
                return redirect()->back()->withInput()->with('id_conflict', [
                    'id' => $existing->id,
                    'name' => $existing->name,
                ]);
            }
        }

        $changes = [];
        DB::connection('eqemu')->transaction(function () use ($input, $item, &$changes, $newId, $request) {
            if (isset($input['id']) && (int)$input['id'] !== (int)$item->id) {
                $targetId = (int)$input['id'];
                if (Item::where('id', $targetId)->exists()) {
                    if ($request->boolean('confirm_id_replace')) {
                        Item::where('id', $targetId)->delete();
                    }
                }
            }

            $item->fill($input);

            if (isset($input['id']) && (int)$input['id'] !== (int)$item->getOriginal('id')) {
                $item->id = (int)$input['id'];
            }

            $dirty = $item->getDirty();
            foreach ($dirty as $key => $new) {
                $old = $item->getOriginal($key);
                $changes[$key] = ['old' => $old, 'new' => $new];
            }

            if (!empty($changes)) {
                $item->save();
            }
        });

        return redirect()
            ->route('items.edit', $item->id)
            ->with('status', count($changes) ? 'Item updated' : 'No changes detected');
    }

    public function popup(Item $item)
    {
        $item = Item::where('id', $item->id)->firstOrFail();
        (new ItemViewModel($item))->withEffects();

        return response()->json([
            'html' => view('items.partials.popup', ['item' => $item])->render()
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

        $item = null;
        $id = isset($normalized['id']) ? (int)$normalized['id'] : null;
        if ($id) {
            $orig = Item::find($id);
            if ($orig) {
                $item = $orig->replicate();
                $item->setRawAttributes($orig->getAttributes(), true);
            }
        }

        if (!$item) {
            $item = new Item();
        }

        foreach ($normalized as $k => $v) {
            if (is_string($v) && is_numeric($v)) {
                $v = (strpos($v, '.') !== false) ? (float)$v : (int)$v;
            }

            $item->{$k} = $v;
        }

        (new ItemViewModel($item))->withEffects();

        $html = view('items.partials.preview-item', [
            'item' => $item,
        ])->render();

        return response()->json(['html' => $html]);
    }

    public function clone(Request $request, Item $item)
    {
        $new = $item->replicate();

        $suffix = ' (Copy)';
        $newName = $item->Name . $suffix;

        if (Item::where('Name', $newName)->exists()) {
            $newName = $item->Name . $suffix . ' ' . now()->format('YmdHis');
        }

        $new->Name = $newName;
        $new->created = now();
        $new->updated = now();

        $newId = null;
        DB::connection('eqemu')->transaction(function () use (&$new, &$newId) {
            $table = $new->getTable();
            $max = DB::connection('eqemu')->table($table)->lockForUpdate()->max('id');
            $newId = (($max ?? 0) + 1);
            $new->id = $newId;
            $new->save();
        });

        try {
            $userName = auth()->user()?->name ?? 'System';
            $message = "[CLONED] [Item] - **User**: {$userName}, **Original:** ({$item->id}) {$item->Name}, **Cloned to:** ({$newId}) {$new->Name}";
            DiscordAlert::message($message);
        } catch (\Throwable $e) {
        }

        toast()->success('Cloned!', 'Item cloned.');

        $redirect = $request->input('redirect', 'edit');
        if ($redirect === 'index') {
            return back()->with('new_id', $newId);
        }

        return redirect()->route('items.edit', $new);
    }

    public function destroy(Item $item)
    {
        $attrs = $item->getAttributes();
        $itemId = $attrs['id'] ?? $item->id;
        $itemName = $attrs['Name'] ?? $item->Name ?? 'Unknown';

        $item->delete();

        try {
            $u = Auth::user();
            $user = $u ? $u->name : 'System';
            $message = "[DELETED] [Item] - **User**: {$user}, **id:** {$itemId}, **name:** {$itemName}";
            DiscordAlert::message($message);
        } catch (\Throwable $e) {
        }

        $previous = url()->previous();

        if (str_ends_with($previous, "/items/{$itemId}/edit")) {
            return redirect()
                ->route('items.index')
                ->with('success', 'Item deleted.');
        }

        return redirect()
            ->back()
            ->with('success', 'Item deleted.');
    }

    public function search(Request $request)
    {
        $search = $request->string('q');

        if ($request->filled('id')) {
            $id = $request->input('id');

            $result = Item::query()
                ->select('id', 'Name', 'icon')
                ->where('id', $id)
                ->first();

            return response()->json($result);
        }

        return Item::query()
            ->select('id', 'Name', 'icon')
            ->when($search, function ($q) use ($search) {
                $q->where('id', $search)
                ->orWhere('Name', 'like', "%{$search}%");
            })
            ->orderBy('id')
            ->limit(50)
            ->get();
    }
}
