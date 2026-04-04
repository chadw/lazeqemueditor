<?php

namespace App\Enums;

enum Deities: int
{
    case AGNOSTIC = 1;
    case BERTOX = 2;
    case BRELL = 4;
    case CAZIC = 8;
    case EROLLISI = 16;
    case BRISTLEBANE = 32;
    case INNORUUK = 64;
    case KARANA = 128;
    case MITHMARR = 256;
    case PREXUS = 512;
    case QUELLIOUS = 1024;
    case RALLOSZEK = 2048;
    case RODCET = 4096;
    case SOLUSEK = 8192;
    case TRIBUNAL = 16384;
    case TUNARE = 32768;
    case VEESHAN = 65536;

    public function label(): string
    {
        return match ($this) {
            self::AGNOSTIC => 'Agnostic',
            self::BERTOX => 'Bertoxxulous',
            self::BRELL => 'Brell Serilis',
            self::CAZIC => 'Cazic Thule',
            self::EROLLISI => 'Erollisi Marr',
            self::BRISTLEBANE => 'Bristlebane',
            self::INNORUUK => 'Innoruuk',
            self::KARANA => 'Karana',
            self::MITHMARR => 'Mithaniel Marr',
            self::PREXUS => 'Prexus',
            self::QUELLIOUS => 'Quellious',
            self::RALLOSZEK => 'Rallos Zek',
            self::RODCET => 'Rodcet Nife',
            self::SOLUSEK => 'Solusek Ro',
            self::TRIBUNAL => 'The Tribunal',
            self::TUNARE => 'Tunare',
            self::VEESHAN => 'Veeshan',
            default => str($this->name)->title()->replace('_', ''),
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
