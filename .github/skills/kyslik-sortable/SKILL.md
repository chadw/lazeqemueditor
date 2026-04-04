# Kyslik Sortable — Repo Skill

Purpose
- Document the consistent pattern for adding Kyslik ColumnSortable support to models, controllers, and views in this codebase.

When to use
- Any list page where columns should be sortable, especially when sorting requires joining another table (e.g., sort merchants by item name or item price).

Pattern summary
- Model: add the `Sortable` trait, declare `public array $sortable`, and implement `*Sortable` methods when joins are required. Always `select('main_table.*')` after any joins.
- Controller: call `->sortable()` on the Eloquent query before `paginate()`, and use `->withQueryString()` to preserve current filters.
- View: use `@sortablelink('column','Label')` (or the project's `x-th-sort` helper) for headers so Kyslik will toggle direction via query string.

Model example (app/Models/Merchantlist.php)
```php
use Kyslik\ColumnSortable\Sortable;

class Merchantlist extends BaseModel
{
    use Sortable;

    public array $sortable = ['slot', 'item', 'buy', 'sell', 'alt_currency_cost'];

    // Sort by related item name
    public function itemSortable($query, $direction)
    {
        return $query
            ->leftJoin('items', 'merchantlist.item', '=', 'items.id')
            ->orderBy('items.Name', $direction)
            ->select('merchantlist.*');
    }

    // Sort by related item's buy cost
    public function buySortable($query, $direction)
    {
        return $query
            ->leftJoin('items', 'merchantlist.item', '=', 'items.id')
            ->orderBy('items.buy_cost', $direction)
            ->select('merchantlist.*');
    }

    // Sort by related item's sell price
    public function sellSortable($query, $direction)
    {
        return $query
            ->leftJoin('items', 'merchantlist.item', '=', 'items.id')
            ->orderBy('items.sell_price', $direction)
            ->select('merchantlist.*');
    }
}
```

Controller example
```php
// Keep filters applied, include relationships needed by the view
$merchantItems = Merchantlist::where('merchantid', $merchantId)
    ->with('items')
    ->sortable(['slot' => 'asc'])
    ->paginate(100)
    ->withQueryString();
```

View example (Blade)
```blade
<th scope="col">@sortablelink('slot', 'Slot')</th>
<th scope="col">@sortablelink('item', 'Item')</th>
<th scope="col">@sortablelink('buy', 'Buy Price')</th>
<th scope="col">@sortablelink('sell', 'Sell Price')</th>
```

Rules & gotchas
- When you join tables for sorting, include `->select('your_main_table.*')` to avoid column collisions and ensure Eloquent hydrates the model correctly.
- Keep `with('relationship')` in the controller so the view's access to related data doesn't cause N+1 queries (eager load what the view uses).
- Use `->withQueryString()` so current filters (zone, npc, etc.) survive when toggling sort.
- Prefer `leftJoin` in `*Sortable` methods so records without a related row still appear.
- Add the sortable column name to the model's `$sortable` array even if the implementation uses a custom `*Sortable` method.

Testing
- After adding sortable support, load the page and toggle a few headers. Inspect Debugbar queries and confirm: a) no N+1 queries are introduced, and b) the SQL ORDER BY is using the intended joined column.

Example checklist for new sortable pages
- [ ] Add `Sortable` trait and `$sortable` array to model.
- [ ] Implement `*Sortable` join methods when needed and `select('main_table.*')`.
- [ ] Update controller to call `->sortable()` before `paginate()` and keep `withQueryString()`.
- [ ] Update the view headers to use `@sortablelink` or `x-th-sort`.
- [ ] Verify eager-loading of relationships used in the view.
- [ ] Confirm via Debugbar that queries are efficient.
