<?php

namespace App\Models;

use App\Enums\ComplaintStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Complaint extends Model
{
    use HasFactory;

    protected $fillable = [
        'complaint_reference',
        'booking_id',
        'subject',
        'description',
        'complaint_status',
        'admin_response',
        'resolved_by',
        'resolved_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'complaint_status' => ComplaintStatus::class,
            'resolved_at' => 'datetime',
        ];
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Complaint $complaint): void {
            if (empty($complaint->complaint_reference)) {
                $complaint->complaint_reference = static::generateComplaintReference();
            }
        });
    }

    /**
     * Generate a unique complaint reference (CMP-YYYYMM-XXXX).
     */
    public static function generateComplaintReference(): string
    {
        $prefix = 'CMP-'.date('Ym').'-';
        $seq = static::whereYear('created_at', date('Y'))
            ->whereMonth('created_at', date('m'))
            ->count() + 1;

        do {
            $candidate = $prefix.str_pad((string) $seq, 4, '0', STR_PAD_LEFT);
            $exists = static::where('complaint_reference', $candidate)->exists();

            if ($exists) {
                $seq++;
            }
        } while ($exists && $seq < 9999);

        if ($seq >= 9999) {
            $candidate = $prefix.uniqid();
        }

        return $candidate;
    }

    /**
     * Get the booking this complaint belongs to.
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    /**
     * Get the user who resolved this complaint.
     */
    public function resolvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }
}
