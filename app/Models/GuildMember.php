<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Kyslik\ColumnSortable\Sortable;

class GuildMember extends BaseModel
{
    use Sortable;

    protected $connection = 'eqemu';
    protected $table = 'guild_members';
    protected $primaryKey = 'char_id';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'char_id',
        'guild_id',
        'rank',
        'tribute_enable',
        'total_tribute',
        'last_tribute',
        'banker',
        'public_note',
        'alt',
        'online',
    ];

    public array $sortable = [
        'name',
        'level',
        'class',
        'race',
    ];

    public function guild(): BelongsTo
    {
        return $this->belongsTo(Guild::class, 'guild_id');
    }

    public function character(): BelongsTo
    {
        return $this->belongsTo(CharacterData::class, 'char_id', 'id');
    }

    public function guildRank(): BelongsTo
    {
        return $this->belongsTo(GuildRank::class, 'rank', 'rank');
    }

    public function nameSortable($query, $direction)
    {
        return $query->leftJoin('character_data as c', 'c.id', '=', 'guild_members.char_id')
            ->orderBy('c.name', $direction)
            ->select('guild_members.*');
    }

    public function levelSortable($query, $direction)
    {
        return $query->leftJoin('character_data as c', 'c.id', '=', 'guild_members.char_id')
            ->orderBy('c.level', $direction)
            ->select('guild_members.*');
    }

    public function classSortable($query, $direction)
    {
        $classes = (array) config('everquest.classes', []);
        $case = 'CASE';
        foreach ($classes as $id => $name) {
            $case .= " WHEN c.class = " . (int) $id . " THEN '" . str_replace("'", "''", $name) . "'";
        }
        $case .= " ELSE '' END";

        return $query->leftJoin('character_data as c', 'c.id', '=', 'guild_members.char_id')
            ->orderByRaw($case . ' ' . $direction)
            ->select('guild_members.*');
    }

    public function raceSortable($query, $direction)
    {
        $races = (array) config('everquest.races', []);
        $case = 'CASE';
        foreach ($races as $id => $name) {
            $case .= " WHEN c.race = " . (int) $id . " THEN '" . str_replace("'", "''", $name) . "'";
        }
        $case .= " ELSE '' END";

        return $query->leftJoin('character_data as c', 'c.id', '=', 'guild_members.char_id')
            ->orderByRaw($case . ' ' . $direction)
            ->select('guild_members.*');
    }
}
