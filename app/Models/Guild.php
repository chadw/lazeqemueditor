<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Kyslik\ColumnSortable\Sortable;

class Guild extends BaseModel
{
    use Sortable;

    protected $connection = 'eqemu';
    protected $table = 'guilds';
    public $timestamps = false;

    protected $fillable = [
        'name',
        'leader',
        'minstatus',
        'motd',
        'tribute',
        'motd_setter',
        'channel',
        'url',
        'favor',
    ];

    public array $sortable = [
        'name',
        'leader',
        'members_count',
    ];

    public function leaderSortable($query, $direction)
    {
        return $query->leftJoin('character_data as lc', 'lc.id', '=', 'guilds.leader')
            ->orderBy('lc.name', $direction)
            ->select('guilds.*');
    }

    public function membersCountSortable($query, $direction)
    {
        $sub = DB::connection('eqemu')
            ->table('guild_members')
            ->selectRaw('guild_id, count(*) as cnt')
            ->groupBy('guild_id');

        return $query->leftJoinSub($sub, 'gm_counts', function ($join) {
                $join->on('gm_counts.guild_id', '=', 'guilds.id');
            })
            ->orderByRaw('COALESCE(gm_counts.cnt,0) ' . $direction);
    }

    public function members(): HasMany
    {
        return $this->hasMany(GuildMember::class, 'guild_id');
    }

    public function ranks(): HasMany
    {
        return $this->hasMany(GuildRank::class, 'guild_id')
            ->orderBy('rank', 'asc');
    }

    public function permissions(): HasMany
    {
        return $this->hasMany(GuildPermission::class, 'guild_id')
            ->orderBy('perm_id', 'asc');
    }

    public function bank(): HasMany
    {
        return $this->hasMany(GuildBank::class, 'guild_id')
            ->orderBy('area', 'asc')
            ->orderBy('slot', 'asc');
    }

    public function leaderCharacter(): BelongsTo
    {
        return $this->belongsTo(CharacterData::class, 'leader', 'id')->select(['id', 'name']);
    }
}
