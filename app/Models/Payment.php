<?php

namespace App\Models;

use App\Enums\PaymentMethod;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'receipt_number',
        'booking_id',
        'amount',
        'payment_method',
        'received_by',
        'remarks',
        'paid_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'payment_method' => PaymentMethod::class,
            'paid_at' => 'datetime',
        ];
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Payment $payment): void {
            if (empty($payment->receipt_number)) {
                $payment->receipt_number = static::generateReceiptNumber();
            }
        });

        static::created(function (Payment $payment): void {
            $payment->booking->recalculatePayments();
        });

        static::deleted(function (Payment $payment): void {
            $payment->booking->recalculatePayments();
        });
    }

    /**
     * Generate a unique receipt number (RCP-YYYY-XXXX).
     */
    public static function generateReceiptNumber(): string
    {
        $year = date('Y');
        $base = "RCP-{$year}-";
        $seq = static::whereYear('created_at', $year)->count() + 1;

        do {
            $candidate = $base.str_pad((string) $seq, 4, '0', STR_PAD_LEFT);
            $exists = static::where('receipt_number', $candidate)->exists();

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
     * Get the booking this payment belongs to.
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    /**
     * Get the user who received this payment.
     */
    public function receivedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by');
    }
}
