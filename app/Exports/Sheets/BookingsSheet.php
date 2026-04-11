<?php

namespace App\Exports\Sheets;

use App\Models\Booking;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BookingsSheet implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    public function __construct(
        public Carbon $from,
        public Carbon $to,
    ) {}

    public function title(): string
    {
        return 'Bookings';
    }

    public function collection(): Collection
    {
        return Booking::with('room.roomCategory')
            ->whereBetween('created_at', [
                $this->from->copy()->startOfDay(),
                $this->to->copy()->endOfDay(),
            ])
            ->latest()
            ->get();
    }

    /**
     * @return array<int, string>
     */
    public function headings(): array
    {
        return [
            'Reference',
            'Guest Name',
            'Guest Email',
            'Guest Phone',
            'Room Category',
            'Check-in',
            'Check-out',
            'Nights',
            'Guests',
            'Booking Status',
            'Payment Status',
            'Total Amount',
            'Amount Paid',
            'Balance',
        ];
    }

    /**
     * @param  Booking  $booking
     * @return array<int, mixed>
     */
    public function map($booking): array
    {
        return [
            $booking->booking_reference,
            $booking->guest_name,
            $booking->guest_email,
            $booking->guest_phone,
            $booking->room?->roomCategory?->name ?? '—',
            $booking->check_in_date?->format('Y-m-d'),
            $booking->check_out_date?->format('Y-m-d'),
            $booking->nights,
            $booking->num_guests,
            $booking->booking_status->label(),
            $booking->payment_status->label(),
            $booking->total_amount,
            $booking->amount_paid,
            $booking->balance_remaining,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
