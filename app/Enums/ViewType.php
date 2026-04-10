<?php

namespace App\Enums;

enum ViewType: string
{
    case City = 'city';
    case Garden = 'garden';
    case Ocean = 'ocean';
    case Pool = 'pool';
    case Mountain = 'mountain';
    case None = 'none';

    public function label(): string
    {
        return match ($this) {
            self::City => 'City View',
            self::Garden => 'Garden View',
            self::Ocean => 'Ocean View',
            self::Pool => 'Pool View',
            self::Mountain => 'Mountain View',
            self::None => 'No View',
        };
    }
}
