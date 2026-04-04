<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class NpcSpell extends BaseModel
{
    protected $connection = 'eqemu';
    protected $table = 'npc_spells';
    public $timestamps = false;

    protected $fillable = [
        'name',
        'parent_list',
        'fail_recast',
        'attack_proc',
        'proc_chance',
        'range_proc',
        'rproc_chance',
        'defensive_proc',
        'dproc_chance',
        'engaged_no_sp_recast_min',
        'engaged_no_sp_recast_max',
        'engaged_b_self_chance',
        'engaged_b_other_chance',
        'engaged_d_chance',
        'pursue_no_sp_recast_min',
        'pursue_no_sp_recast_max',
        'pursue_d_chance',
        'idle_no_sp_recast_min',
        'idle_no_sp_recast_max',
        'idle_b_chance',
    ];

    public function parentSet(): BelongsTo
    {
        return $this->belongsTo(NpcSpell::class, 'parent_list', 'id');
    }

    public function npcSpellEntries(): HasMany
    {
        return $this->hasMany(NpcSpellEntry::class, 'npc_spells_id', 'id')
            ->orderBy('minlevel', 'asc')
            ->orderBy('maxlevel', 'asc');
    }

    public function attackProcSpell(): BelongsTo
    {
        return $this->belongsTo(Spell::class, 'attack_proc', 'id')
            ->select('id', 'name', 'new_icon');
    }

    public function rangeProcSpell(): BelongsTo
    {
        return $this->belongsTo(Spell::class, 'range_proc', 'id')
            ->select('id', 'name', 'new_icon');
    }

    public function defensiveProcSpell(): BelongsTo
    {
        return $this->belongsTo(Spell::class, 'defensive_proc', 'id')
            ->select('id', 'name', 'new_icon');
    }

    public function npcTypes(): HasMany
    {
        return $this->hasMany(NpcType::class, 'npc_spells_id', 'id')
            ->select('id', 'npc_spells_id');
    }

    public static function npcSpellOptions(): Collection
    {
        return self::select('id', 'name')
            ->orderBy('name')
            ->get();
    }
}
