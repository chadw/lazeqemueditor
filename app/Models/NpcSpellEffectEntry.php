<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasOne;

class NpcSpellEffectEntry extends BaseModel
{
    protected $connection = 'eqemu';
    protected $table = 'npc_spells_effects_entries';
    public $timestamps = false;

    protected $fillable = [
        'npc_spells_effects_id',
        'spell_effect_id',
        'minlevel',
        'maxlevel',
        'se_base',
        'se_limit',
        'se_max',
    ];

    public function spells(): HasOne
    {
        return $this->hasOne(Spell::class, 'id', 'spellid')
            ->select('id', 'name', 'new_icon');
    }
}
