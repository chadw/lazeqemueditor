<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use App\Models\Zone;

class NpcFilter
{
    protected $request;
    protected $builder;

    protected array $filters = [
        'id',
        'q',
        'class',
        'min_lvl',
        'max_lvl',
        'zone',
        'version',
    ];

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function apply(Builder $builder): Builder
    {
        $this->builder = $builder;

        foreach ($this->filters as $filter) {
            if (!$this->request->filled($filter)) {
                continue;
            }

            $value = $this->request->input($filter);

            if (method_exists($this, $filter)) {
                $this->{$filter}($value);
                continue;
            }

            $alt = $filter . '_filter';
            if (method_exists($this, $alt)) {
                $this->{$alt}($value);
            }
        }

        return $this->builder;
    }

    protected function q($value)
    {
        if ($value === null || $value === '') {
            return;
        }

        $value = str_replace(' ', '_', $value);
        $value = str_replace('`', '-', $value);

        $this->builder->where('name', 'like', "%{$value}%");
    }

    protected function id($value)
    {
        $this->builder->where('id', (int)$value);
    }

    protected function class_filter($value)
    {
        $this->builder->where('class', (int)$value);
    }

    protected function zone($value)
    {
        // value is zoneidnumber
        $zone = Zone::where('zoneidnumber', $value)->first();
        if (!$zone) return;

        $zoneShort = $zone->short_name;
        $version = (int) $this->request->input('version', 0);

        $this->builder->whereHas('spawnEntries.spawn2', function ($q) use ($zoneShort, $version) {
            $q->where('zone', $zoneShort)
                ->when($version > 0, fn($qq) => $qq->where('version', $version));
        });
    }

    protected function version($value)
    {
        // version is handled in zone() using request('version'), so ignore here
    }

    protected function min_lvl($value)
    {
        $this->builder->where('level', '>=', $value);
    }

    protected function max_lvl($value)
    {
        $this->builder->where('level', '<=', $value);
    }
}
