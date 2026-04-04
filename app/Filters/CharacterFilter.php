<?php

namespace App\Filters;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

class CharacterFilter
{
    protected Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function apply(Builder $query): Builder
    {
        // q: id or name
        if ($val = trim((string) $this->request->input('q', ''))) {
            $query->where(function ($q) use ($val) {
                if (is_numeric($val)) {
                    $q->orWhere('id', (int) $val);
                }

                $q->orWhere('name', 'like', "%{$val}%");
            });
        }

        // account (id or name)
        if ($val = trim((string) $this->request->input('account', ''))) {
            $query->where(function ($q) use ($val) {
                if (is_numeric($val)) {
                    $q->orWhere('account_id', (int) $val);
                }

                $q->orWhereExists(function ($sub) use ($val) {
                    $sub->selectRaw('1')
                        ->from('account as a')
                        ->whereColumn('a.id', 'character_data.account_id')
                        ->where('a.name', 'like', "%{$val}%");
                });
            });
        }

        // guild name
        if ($val = trim((string) $this->request->input('guild', ''))) {
            $query->whereExists(function ($sub) use ($val) {
                $sub->selectRaw('1')
                    ->from('guild_members as gm')
                    ->join('guilds as g', 'g.id', '=', 'gm.guild_id')
                    ->whereColumn('gm.char_id', 'character_data.id')
                    ->where('g.name', 'like', "%{$val}%");
            });
        }

        // deleted filter: show deleted when ?deleted=1, otherwise only non-deleted
        if ($this->request->boolean('deleted')) {
            $query->whereNotNull('deleted_at');
        } else {
            $query->whereNull('deleted_at');
        }

        return $query;
    }
}
