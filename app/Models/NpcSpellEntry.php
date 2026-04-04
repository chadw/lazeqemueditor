<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasOne;

class NpcSpellEntry extends BaseModel
{
    protected $connection = 'eqemu';
    protected $table = 'npc_spells_entries';
    public $timestamps = false;

    protected $fillable = [
        'npc_spells_id',
        'spellid',
        'type',
        'minlevel',
        'maxlevel',
        'manacost',
        'recast_delay',
        'priority',
        'resist_adjust',
        'min_hp',
        'max_hp',
        'min_expansion',
        'max_expansion',
        'content_flags',
        'content_flags_disabled',
    ];

    public function spells(): HasOne
    {
        return $this->hasOne(Spell::class, 'id', 'spellid')
            ->select('id', 'name', 'new_icon');
    }

    public function getHasContentRestrictionsAttribute(): bool
    {
        return
            $this->min_expansion != -1 ||
            $this->max_expansion != -1 ||
            $this->content_flags != '' ||
            $this->content_flags_disabled != '';
    }
}
