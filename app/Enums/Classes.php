<?php

namespace App\Enums;

enum Classes: int
{
    case WARRIOR = 1;
    case CLERIC = 2;
    case PALADIN = 4;
    case RANGER = 8;
    case SHADOWKNIGHT = 16;
    case DRUID = 32;
    case MONK = 64;
    case BARD = 128;
    case ROGUE = 256;
    case SHAMAN = 512;
    case NECROMANCER = 1024;
    case WIZARD = 2048;
    case MAGICIAN = 4096;
    case ENCHANTER = 8192;
    case BEASTLORD = 16384;
    case BERSERKER = 32768;

    public function label(): string
    {
        return match ($this) {
            self::WARRIOR => 'Warrior',
            self::CLERIC => 'Cleric',
            self::PALADIN => 'Paladin',
            self::RANGER => 'Ranger',
            self::SHADOWKNIGHT => 'Shadowknight',
            self::DRUID => 'Druid',
            self::MONK => 'Monk',
            self::BARD => 'Bard',
            self::ROGUE => 'Rogue',
            self::SHAMAN => 'Shaman',
            self::NECROMANCER => 'Necromancer',
            self::WIZARD => 'Wizard',
            self::MAGICIAN => 'Magician',
            self::ENCHANTER => 'Enchanter',
            self::BEASTLORD => 'Beastlord',
            self::BERSERKER => 'Berserker',
            default => str($this->name)->title()->replace('_', ''),
        };
    }

    public function shortName(): string
    {
        return match ($this) {
            self::WARRIOR => 'WAR',
            self::CLERIC => 'CLR',
            self::PALADIN => 'PAL',
            self::RANGER => 'RNG',
            self::SHADOWKNIGHT => 'SHD',
            self::DRUID => 'DRU',
            self::MONK => 'MNK',
            self::BARD => 'BRD',
            self::ROGUE => 'ROG',
            self::SHAMAN => 'SHM',
            self::NECROMANCER => 'NEC',
            self::WIZARD => 'WIZ',
            self::MAGICIAN => 'MAG',
            self::ENCHANTER => 'ENC',
            self::BEASTLORD => 'BST',
            self::BERSERKER => 'BER',
        };
    }

    public static function fromBitmask(int $bitmask): array
    {
        return collect(self::cases())
            ->filter(fn($enum) => ($bitmask & $enum->value))
            ->values()
            ->all();
    }
}
