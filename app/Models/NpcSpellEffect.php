<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

class NpcSpellEffect extends BaseModel
{
    protected $connection = 'eqemu';
    protected $table = 'npc_spells_effects';
    public $timestamps = false;

    protected $fillable = [
        'name',
        'parent_list',
    ];

    public function npcSpellEffectEntries(): HasMany
    {
        return $this->hasMany(NpcSpellEffectEntry::class, 'npc_spells_effects_id', 'id');
    }
}
