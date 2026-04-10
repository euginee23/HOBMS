<?php

namespace App\Models;

use App\Enums\BedType;
use App\Enums\BookingStatus;
use App\Enums\RoomStatus;
use App\Enums\ViewType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_category_id',
        'room_number',
        'floor',
        'bed_type',
        'bed_count',
        'view_type',
        'is_smoking',
        'status',
        'notes',
        'last_cleaned_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => RoomStatus::class,
            'bed_type' => BedType::class,
            'view_type' => ViewType::class,
            'is_smoking' => 'boolean',
            'bed_count' => 'integer',
            'last_cleaned_at' => 'datetime',
        ];
    }

    /**
     * Get the category this room belongs to.
     */
    public function roomCategory(): BelongsTo
    {
        return $this->belongsTo(RoomCategory::class);
    }

    /**
     * Get all bookings for this room.
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Scope to available rooms.
     *
     * @param  Builder<Room>  $query
     * @return Builder<Room>
     */
    public function scopeAvailable($query)
    {
        return $query->where('status', RoomStatus::Available);
    }

    /**
     * Check if room is available for the given date range.
     */
    public function isAvailableForDates(\DateTimeInterface|string $checkIn, \DateTimeInterface|string $checkOut): bool
    {
        if ($this->status !== RoomStatus::Available) {
            return false;
        }

        return ! $this->bookings()
            ->whereNotIn('booking_status', [
                BookingStatus::Cancelled->value,
                BookingStatus::NoShow->value,
                BookingStatus::CheckedOut->value,
            ])
            ->where('check_in_date', '<', $checkOut)
            ->where('check_out_date', '>', $checkIn)
            ->exists();
    }
}
