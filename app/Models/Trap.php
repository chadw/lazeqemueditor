<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Trap extends BaseModel
{
    protected $connection = 'eqemu';
    protected $table = 'traps';
    public $timestamps = false;

    protected $fillable = [
        'zone',
        'version',
        'x',
        'y',
        'z',
        'chance',
        'maxzdiff',
        'radius',
        'effect',
        'effectvalue',
        'effectvalue2',
        'message',
        'skill',
        'level',
        'respawn_time',
        'respawn_var',
        'triggered_number',
        'group',
        'despawn_when_triggered',
        'undetectable',
        'min_expansion',
        'max_expansion',
        'content_flags',
        'content_flags_disabled',
    ];

    protected $casts = [
        'despawn_when_triggered' => 'boolean',
        'undetectable' => 'boolean',
    ];

    public function spell(): BelongsTo
    {
        return $this->belongsTo(Spell::class, 'effectvalue', 'id')
            ->select('id', 'name', 'new_icon');
    }

    public function npc(): BelongsTo
    {
        return $this->belongsTo(NpcType::class, 'effectvalue', 'id')
            ->select('id', 'name');
    }

    protected function effectTarget(): Attribute
    {
        return Attribute::get(function () {
            return match ($this->effect) {
                0 => $this->spell
                    ? [
                        'type'  => 'spell',
                        'model' => $this->spell,
                    ]
                    : null,

                2, 3 => $this->npc
                    ? [
                        'type'  => 'npc',
                        'model' => $this->npc,
                    ]
                    : null,

                default => null,
            };
        });
    }

    public function effectValue2Label(): string
    {
        return match ($this->effect) {
            // spell
            0 => 'Unused',
            // aggro type
            1 => match ($this->effectvalue2) {
                0 => 'Everything will aggro',
                1 => 'Only KoS will aggro',
                default => (string) $this->effectvalue2,
            },
            // npcs
            2, 3 => sprintf(
                '%d Spawn%s',
                $this->effectvalue2,
                $this->effectvalue2 == 1 ? '' : 's'
            ),
            // damage
            4 => sprintf(
                '%d <div class="badge badge-sm badge-soft badge-accent ml-1">Max Dmg</div>',
                $this->effectvalue2
            ),

            default => (string) $this->effectvalue2,
        };
    }
}
