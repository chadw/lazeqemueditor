<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
//use Kyslik\ColumnSortable\Sortable;
use App\Models\FactionList;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CharacterData extends BaseModel
{
    //use Sortable;

    protected $connection = 'eqemu';
    protected $table = 'character_data';
    public $timestamps = false;
    protected $guarded = [];

    /* public $sortable = [
        'id',
        'name',
        'account.name',
        'race',
        'class',
        'level',
        'birthday',
        'last_login',
        'time_played',
    ]; */

    public function zone(): BelongsTo
    {
        return $this->belongsTo(Zone::class, 'zone_id', 'zoneidnumber')
            ->select('zoneidnumber', 'short_name');
    }

    public function skills(): HasMany
    {
        return $this->hasMany(CharacterSkill::class, 'id');
    }

    public function currency(): HasOne
    {
        return $this->hasOne(CharacterCurrency::class, 'id');
    }

    public function languages(): HasMany
    {
        return $this->hasMany(CharacterLanguage::class, 'id');
    }

    public function inventory(): HasMany
    {
        return $this->hasMany(CharacterInventory::class, 'character_id');
    }

    public function sharedbank(): HasMany
    {
        return $this->hasMany(SharedBank::class, 'account_id', 'account_id');
    }

    public function aa(): HasMany
    {
        return $this->hasMany(CharacterAa::class, 'id');
    }

    public function achievementCompletions(): HasMany
    {
        return $this->hasMany(CharacterAchievement::class, 'character_id', 'id');
    }

    public function achievementProgress(): HasMany
    {
        return $this->hasMany(CharacterAchievementProgress::class, 'character_id', 'id');
    }

    public function achievementRewards(): HasMany
    {
        return $this->hasMany(CharacterAchievementReward::class, 'character_id', 'id');
    }

    public function achievementRewardSelections(): HasMany
    {
        return $this->hasMany(CharacterAchievementRewardSelection::class, 'character_id', 'id');
    }

    public function achievementPendingMutations(): HasMany
    {
        return $this->hasMany(CharacterAchievementPendingMutation::class, 'character_id', 'id');
    }

    public function guildMember(): HasOne
    {
        return $this->hasOne(GuildMember::class, 'char_id', 'id');
    }

    public function stats(): HasOne
    {
        return $this->hasOne(CharacterStatsRecord::class, 'character_id');
    }

    public function faction(): HasMany
    {
        return $this->hasMany(FactionValue::class, 'char_id')
            ->with('faction')
            ->orderBy(
                FactionList::select('name')
                    ->whereColumn('faction_list.id', 'faction_values.faction_id')
            );
    }

    public function bindpoint(): HasMany
    {
        return $this->hasMany(CharacterBind::class, 'id')
            ->orderBy('slot');
    }

    public function questGlobals(): HasMany
    {
        return $this->hasMany(QuestGlobal::class, 'charid');
    }

    public function zoneFlags(): HasMany
    {
        return $this->hasMany(ZoneFlag::class, 'charID');
    }

    public function keys(): HasMany
    {
        return $this->hasMany(KeyRing::class, 'char_id');
    }

    public function corpses(): HasMany
    {
        return $this->hasMany(CharacterCorpse::class, 'charid');
    }

    public function dataBuckets(): HasMany
    {
        return $this->hasMany(DataBucket::class, 'character_id');
    }

    public function account(): HasOne
    {
        return $this->hasOne(Account::class, 'id', 'account_id')
            ->select('id', 'name', 'sharedplat');
    }

    public function traders(): HasMany
    {
        return $this->hasMany(Trader::class, 'char_id');
    }

    public function altCurrency(): HasMany
    {
        return $this->hasMany(CharacterAltCurrency::class, 'char_id');
    }

    public function adventureStats(): HasOne
    {
        return $this->hasOne(AdventureStat::class, 'player_id', 'id');
    }

    public function tribute(): HasMany
    {
        return $this->hasMany(CharacterTribute::class, 'character_id');
    }

    public function disciplines(): HasMany
    {
        return $this->hasMany(CharacterDiscipline::class, 'id')
            ->orderBy('slot_id');
    }

    public function spells(): HasMany
    {
        return $this->hasMany(CharacterSpell::class, 'id')
            ->orderBy('slot_id');
    }

    public function memmedSpells(): HasMany
    {
        return $this->hasMany(CharacterMemmedSpell::class, 'id')
            ->orderBy('slot_id');
    }

    public function lockouts(): HasMany
    {
        return $this->hasMany(CharacterExpeditionLockout::class, 'character_id');
    }

    public function bandolier(): HasMany
    {
        return $this->hasMany(CharacterBandolier::class, 'id')
            ->orderBy('bandolier_id');
    }

    public function getDataBucketsByKey()
    {
        $char_id = $this->id;

        return DataBucket::where(function ($query) use ($char_id) {
            $query->where('key', 'like', $char_id . '-%')
                ->orWhere('key', 'like', '%-' . $char_id);
        });
    }

    public function hasQuestGlobal(string $name)
    {
        return $this->questGlobals->first(fn($q) => $q->name === $name);
    }

    public function getMemmedSpellMapAttribute(): array
    {
        $mems = $this->relationLoaded('memmedSpells') ? $this->memmedSpells : $this->memmedSpells()->get();
        $map = array_fill(1, 12, null);

        foreach ($mems as $m) {
            $slot = $m->slot ?? ($m->slot_id ?? ($m->slotnum ?? ($m->snum ?? null)));
            if ($slot === null) continue;
            if (is_numeric($slot) && $slot >= 0 && $slot < 12) {
                $idx = intval($slot) + 1;
            } else {
                $idx = intval($slot);
            }
            if ($idx >= 1 && $idx <= 12 && !isset($map[$idx])) {
                $map[$idx] = $m;
            }
        }

        return $map;
    }

    public function getSpellPagesAttribute(): array
    {
        $spells = $this->relationLoaded('spells') ? $this->spells : $this->spells()->get();

        $hasSlotId = $spells->first() && isset($spells->first()->slot_id);

        $pages = [];

        if ($hasSlotId) {
            $maxSlot = $spells->pluck('slot_id')->max() ?? 0;
            $totalSlots = max($maxSlot + 1, $spells->count());
            $totalPages = intdiv($totalSlots + 7, 8);

            for ($p = 0; $p < $totalPages; $p++) {
                $pageSlots = [];
                for ($i = 0; $i < 8; $i++) {
                    $slotIndex = $p * 8 + $i;
                    $pageSlots[] = $spells->firstWhere('slot_id', $slotIndex);
                }
                $pages[] = $pageSlots;
            }
        } else {
            $chunked = $spells->chunk(8);
            foreach ($chunked as $chunk) {
                $page = [];
                for ($i = 0; $i < 8; $i++) {
                    $page[] = $chunk->get($i);
                }
                $pages[] = $page;
            }

            if (empty($pages)) {
                $pages[] = array_fill(0, 8, null);
            }
        }

        return ['pages' => $pages, 'hasSlotId' => $hasSlotId];
    }
}
