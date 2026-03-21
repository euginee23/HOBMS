<?php

namespace App\Enums;

enum RoomStatus: string
{
    case Available = 'available';
    case Occupied = 'occupied';
    case Maintenance = 'maintenance';
    case OutOfOrder = 'out_of_order';

    public function label(): string
    {
        return match ($this) {
            self::Available => 'Available',
            self::Occupied => 'Occupied',
            self::Maintenance => 'Under Maintenance',
            self::OutOfOrder => 'Out of Order',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Available => 'lime',
            self::Occupied => 'blue',
            self::Maintenance => 'yellow',
            self::OutOfOrder => 'red',
        };
    }
}
