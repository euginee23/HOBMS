<?php

namespace App\Exports\Sheets;

use App\Enums\BookingStatus;
use App\Enums\ComplaintStatus;
use App\Models\Booking;
use App\Models\Complaint;
use App\Models\Payment;
use App\Models\Room;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ReportSummarySheet implements FromArray, WithStyles, WithTitle
{
    public function __construct(
        public Carbon $from,
        public Carbon $to,
    ) {}

    public function title(): string
    {
        return 'Summary';
    }

    /**
     * @return array<int, array<int, mixed>>
     */
    public function array(): array
    {
        $from = $this->from->copy()->startOfDay();
        $to = $this->to->copy()->endOfDay();

        $totalRevenue = Payment::whereBetween('paid_at', [$from, $to])->sum('amount');
        $totalPayments = Payment::whereBetween('paid_at', [$from, $to])->count();
        $outstandingBalance = Booking::whereBetween('created_at', [$from, $to])
            ->whereIn('booking_status', [BookingStatus::Confirmed, BookingStatus::CheckedIn, BookingStatus::CheckedOut])
            ->selectRaw('SUM(total_amount - amount_paid) as balance')
            ->value('balance') ?? 0;

        $totalBookings = Booking::whereBetween('created_at', [$from, $to])->count();
        $totalRooms = Room::count();
        $occupiedRooms = Room::whereHas('bookings', fn ($q) => $q->where('booking_status', BookingStatus::CheckedIn))->count();
        $occupancyRate = $totalRooms > 0 ? round(($occupiedRooms / $totalRooms) * 100, 1) : 0;

        $rows = [];

        $rows[] = ['Report Period', $this->from->format('M d, Y').' — '.$this->to->format('M d, Y')];
        $rows[] = [];
        $rows[] = ['KEY METRICS'];
        $rows[] = ['Total Revenue', $totalRevenue];
        $rows[] = ['Total Payments', $totalPayments];
        $rows[] = ['Outstanding Balance', $outstandingBalance];
        $rows[] = ['Total Bookings', $totalBookings];
        $rows[] = ['Occupancy Rate', $occupancyRate.'%'];
        $rows[] = ['Occupied Rooms', $occupiedRooms.' / '.$totalRooms];

        $rows[] = [];
        $rows[] = ['BOOKING STATUS BREAKDOWN'];
        $rows[] = ['Status', 'Count'];

        foreach (BookingStatus::cases() as $status) {
            $count = Booking::whereBetween('created_at', [$from, $to])
                ->where('booking_status', $status)->count();
            $rows[] = [$status->label(), $count];
        }

        $rows[] = [];
        $rows[] = ['REVENUE BY PAYMENT METHOD'];
        $rows[] = ['Method', 'Total', 'Count'];

        $revenueByMethod = Payment::whereBetween('paid_at', [$from, $to])
            ->selectRaw('payment_method, SUM(amount) as total, COUNT(*) as count')
            ->groupBy('payment_method')
            ->get();

        foreach ($revenueByMethod as $method) {
            $rows[] = [$method->payment_method->label(), $method->total, $method->count];
        }

        $rows[] = [];
        $rows[] = ['COMPLAINTS SUMMARY'];
        $totalComplaints = Complaint::whereBetween('created_at', [$from, $to])->count();
        $resolvedComplaints = Complaint::whereBetween('created_at', [$from, $to])
            ->whereIn('complaint_status', [ComplaintStatus::Resolved, ComplaintStatus::Closed])->count();

        $rows[] = ['Total Complaints', $totalComplaints];
        $rows[] = ['Resolved / Closed', $resolvedComplaints];

        return $rows;
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
            3 => ['font' => ['bold' => true]],
            11 => ['font' => ['bold' => true]],
            12 => ['font' => ['bold' => true]],
        ];
    }
}
