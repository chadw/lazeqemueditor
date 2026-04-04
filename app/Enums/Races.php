<?php

namespace App\Enums;

enum Races: int
{
    case HUMAN = 1;
    case BARBARIAN = 2;
    case ERUDITE = 4;
    case WOODELF = 8;
    case HIGHELF = 16;
    case DARKELF = 32;
    case HALFELF = 64;
    case DWARF = 128;
    case TROLL = 256;
    case OGRE = 512;
    case HALFLING = 1024;
    case GNOME = 2048;
    case IKSAR = 4096;
    case VAHSHIR = 8192;
    case FROGLOK = 16384;
    case DRAKKIN = 32768;

    public function label(): string
    {
        return match ($this) {
            self::HUMAN => 'Human',
            self::BARBARIAN => 'Barbarian',
            self::ERUDITE => 'Erudite',
            self::WOODELF => 'Wood Elf',
            self::HIGHELF => 'High Elf',
            self::DARKELF => 'Dark Elf',
            self::HALFELF => 'Half Elf',
            self::DWARF => 'Dwarf',
            self::TROLL => 'Troll',
            self::OGRE => 'Ogre',
            self::HALFLING => 'Halfling',
            self::GNOME => 'Gnome',
            self::IKSAR => 'Iksar',
            self::VAHSHIR => 'Vah Shir',
            self::FROGLOK => 'Froglok',
            self::DRAKKIN => 'Drakkin',
            default => str($this->name)->title()->replace('_', ''),
        };
    }

    public function shortName(): string
    {
        return match ($this) {
            self::HUMAN => 'HUM',
            self::BARBARIAN => 'BAR',
            self::ERUDITE => 'ERU',
            self::WOODELF => 'WLF',
            self::HIGHELF => 'HEF',
            self::DARKELF => 'DKE',
            self::HALFELF => 'HLF',
            self::DWARF => 'DWF',
            self::TROLL => 'TRL',
            self::OGRE => 'OGR',
            self::HALFLING => 'HFL',
            self::GNOME => 'GNM',
            self::IKSAR => 'IKS',
            self::VAHSHIR => 'VAH',
            self::FROGLOK => 'FRG',
            self::DRAKKIN => 'DRK',
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
