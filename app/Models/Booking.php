<?php

namespace App\Models;

use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_reference',
        'portal_token',
        'guest_name',
        'guest_email',
        'guest_phone',
        'room_id',
        'check_in_date',
        'check_out_date',
        'num_guests',
        'special_requests',
        'booking_status',
        'payment_status',
        'price_per_night',
        'total_amount',
        'amount_paid',
        'confirmed_by',
        'checked_in_by',
        'checked_out_by',
        'confirmed_at',
        'checked_in_at',
        'checked_out_at',
        'cancelled_at',
        'cancellation_reason',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'check_in_date' => 'date',
            'check_out_date' => 'date',
            'booking_status' => BookingStatus::class,
            'payment_status' => PaymentStatus::class,
            'price_per_night' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'amount_paid' => 'decimal:2',
            'confirmed_at' => 'datetime',
            'checked_in_at' => 'datetime',
            'checked_out_at' => 'datetime',
            'cancelled_at' => 'datetime',
            'num_guests' => 'integer',
        ];
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Booking $booking): void {
            if (empty($booking->booking_reference)) {
                $booking->booking_reference = static::generateBookingReference();
            }

            if (empty($booking->portal_token)) {
                $booking->portal_token = bin2hex(random_bytes(32));
            }
        });
    }

    /**
     * Generate a unique booking reference (BK-YYYYMMDD-XXXX).
     */
    public static function generateBookingReference(): string
    {
        $base = 'BK-'.date('Ymd').'-';
        $seq = static::whereDate('created_at', today())->count() + 1;

        do {
            $candidate = $base.str_pad((string) $seq, 4, '0', STR_PAD_LEFT);
            $exists = static::where('booking_reference', $candidate)->exists();

            if ($exists) {
                $seq++;
            }
        } while ($exists && $seq < 9999);

        if ($seq >= 9999) {
            $candidate = $base.uniqid();
        }

        return $candidate;
    }

    /**
     * Get the number of nights for this booking.
     */
    protected function nights(): Attribute
    {
        return Attribute::get(fn () => $this->check_in_date && $this->check_out_date
            ? Carbon::parse($this->check_in_date)->diffInDays(Carbon::parse($this->check_out_date))
            : 0
        );
    }

    /**
     * Get the remaining balance.
     */
    protected function balanceRemaining(): Attribute
    {
        return Attribute::get(fn () => (float) $this->total_amount - (float) $this->amount_paid);
    }

    /**
     * Get the portal URL for this booking.
     */
    protected function portalUrl(): Attribute
    {
        return Attribute::get(fn () => route('portal.view', $this->portal_token));
    }

    /**
     * Get the room for this booking.
     */
    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    /**
     * Get the user who confirmed this booking.
     */
    public function confirmedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'confirmed_by');
    }

    /**
     * Get the user who checked in this booking.
     */
    public function checkedInBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'checked_in_by');
    }

    /**
     * Get the user who checked out this booking.
     */
    public function checkedOutBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'checked_out_by');
    }

    /**
     * Get payments for this booking.
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Get complaints for this booking.
     */
    public function complaints(): HasMany
    {
        return $this->hasMany(Complaint::class);
    }

    /**
     * Recalculate payment totals from associated payments.
     */
    public function recalculatePayments(): void
    {
        $totalPaid = $this->payments()->sum('amount');
        $this->amount_paid = $totalPaid;

        if ($totalPaid >= (float) $this->total_amount) {
            $this->payment_status = PaymentStatus::Paid;
        } elseif ($totalPaid > 0) {
            $this->payment_status = PaymentStatus::PartiallyPaid;
        } else {
            $this->payment_status = PaymentStatus::Unpaid;
        }

        $this->save();
    }
}
