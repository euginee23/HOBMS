<?php

namespace App\Enums;

enum BedType: string
{
    case Single = 'single';
    case Double = 'double';
    case Twin = 'twin';
    case Queen = 'queen';
    case King = 'king';

    public function label(): string
    {
        return match ($this) {
            self::Single => 'Single',
            self::Double => 'Double',
            self::Twin => 'Twin',
            self::Queen => 'Queen',
            self::King => 'King',
        };
    }
}
