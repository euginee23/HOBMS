<?php

use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
use App\Models\Booking;
use App\Models\Complaint;
use App\Models\Payment;
use App\Models\Room;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Reports')] class extends Component {
    public string $period = 'this_month';
    public string $from = '';
    public string $to = '';

    public function mount(): void
    {
        $this->applyPeriod();
    }

    public function updatedPeriod(): void
    {
        $this->applyPeriod();
    }

    private function applyPeriod(): void
    {
        match ($this->period) {
            'today' => [$this->from, $this->to] = [today()->toDateString(), today()->toDateString()],
            'this_week' => [$this->from, $this->to] = [today()->startOfWeek()->toDateString(), today()->endOfWeek()->toDateString()],
            'this_month' => [$this->from, $this->to] = [today()->startOfMonth()->toDateString(), today()->endOfMonth()->toDateString()],
            'last_month' => [$this->from, $this->to] = [today()->subMonth()->startOfMonth()->toDateString(), today()->subMonth()->endOfMonth()->toDateString()],
            'this_year' => [$this->from, $this->to] = [today()->startOfYear()->toDateString(), today()->endOfYear()->toDateString()],
            'custom' => null,
            default => null,
        };
    }

    public function with(): array
    {
        $from = Carbon::parse($this->from)->startOfDay();
        $to = Carbon::parse($this->to)->endOfDay();

        // Booking stats
        $totalBookings = Booking::whereBetween('created_at', [$from, $to])->count();
        $confirmedBookings = Booking::whereBetween('created_at', [$from, $to])
            ->where('booking_status', BookingStatus::Confirmed)->count();
        $checkedInBookings = Booking::whereBetween('created_at', [$from, $to])
            ->where('booking_status', BookingStatus::CheckedIn)->count();
        $checkedOutBookings = Booking::whereBetween('created_at', [$from, $to])
            ->where('booking_status', BookingStatus::CheckedOut)->count();
        $cancelledBookings = Booking::whereBetween('created_at', [$from, $to])
            ->where('booking_status', BookingStatus::Cancelled)->count();
        $noShowBookings = Booking::whereBetween('created_at', [$from, $to])
            ->where('booking_status', BookingStatus::NoShow)->count();

        // Revenue stats
        $totalRevenue = Payment::whereBetween('paid_at', [$from, $to])->sum('amount');
        $totalPayments = Payment::whereBetween('paid_at', [$from, $to])->count();
        $outstandingBalance = Booking::whereBetween('created_at', [$from, $to])
            ->whereIn('booking_status', [BookingStatus::Confirmed, BookingStatus::CheckedIn, BookingStatus::CheckedOut])
            ->selectRaw('SUM(total_amount - amount_paid) as balance')
            ->value('balance') ?? 0;

        // Occupancy
        $totalRooms = Room::count();
        $occupiedRooms = Room::whereHas('bookings', fn ($q) => $q->where('booking_status', BookingStatus::CheckedIn))->count();
        $occupancyRate = $totalRooms > 0 ? round(($occupiedRooms / $totalRooms) * 100, 1) : 0;

        // Complaints
        $totalComplaints = Complaint::whereBetween('created_at', [$from, $to])->count();
        $resolvedComplaints = Complaint::whereBetween('created_at', [$from, $to])
            ->whereIn('complaint_status', [\App\Enums\ComplaintStatus::Resolved, \App\Enums\ComplaintStatus::Closed])->count();

        // Recent bookings for the table
        $recentBookings = Booking::with('room.roomCategory')
            ->whereBetween('created_at', [$from, $to])
            ->latest()
            ->limit(20)
            ->get();

        // Revenue by payment method
        $revenueByMethod = Payment::whereBetween('paid_at', [$from, $to])
            ->selectRaw('payment_method, SUM(amount) as total, COUNT(*) as count')
            ->groupBy('payment_method')
            ->get();

        return compact(
            'totalBookings', 'confirmedBookings', 'checkedInBookings', 'checkedOutBookings',
            'cancelledBookings', 'noShowBookings', 'totalRevenue', 'totalPayments',
            'outstandingBalance', 'occupancyRate', 'occupiedRooms', 'totalRooms',
            'totalComplaints', 'resolvedComplaints', 'recentBookings', 'revenueByMethod',
        );
    }
}; ?>

<div>
    <div class="space-y-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <flux:heading size="xl">Reports</flux:heading>
            <div class="flex items-center gap-3">
                <flux:select wire:model.live="period" class="w-40">
                    <flux:select.option value="today">Today</flux:select.option>
                    <flux:select.option value="this_week">This Week</flux:select.option>
                    <flux:select.option value="this_month">This Month</flux:select.option>
                    <flux:select.option value="last_month">Last Month</flux:select.option>
                    <flux:select.option value="this_year">This Year</flux:select.option>
                    <flux:select.option value="custom">Custom Range</flux:select.option>
                </flux:select>
            </div>
        </div>

        @if($period === 'custom')
            <div class="flex items-end gap-3">
                <flux:input wire:model.live.debounce="from" label="From" type="date" />
                <flux:input wire:model.live.debounce="to" label="To" type="date" />
            </div>
        @endif

        <p class="text-sm text-zinc-500 dark:text-zinc-400">
            Showing data from {{ \Carbon\Carbon::parse($from)->format('M d, Y') }} to {{ \Carbon\Carbon::parse($to)->format('M d, Y') }}
        </p>

        {{-- Key Metrics --}}
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div class="rounded-xl border border-zinc-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-900">
                <div class="text-sm text-zinc-500 dark:text-zinc-400">Total Revenue</div>
                <div class="mt-1 text-2xl font-bold text-green-600 dark:text-green-400">₱{{ number_format($totalRevenue, 2) }}</div>
                <div class="mt-1 text-xs text-zinc-400">{{ $totalPayments }} payments</div>
            </div>
            <div class="rounded-xl border border-zinc-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-900">
                <div class="text-sm text-zinc-500 dark:text-zinc-400">Outstanding Balance</div>
                <div class="mt-1 text-2xl font-bold text-red-600 dark:text-red-400">₱{{ number_format($outstandingBalance, 2) }}</div>
            </div>
            <div class="rounded-xl border border-zinc-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-900">
                <div class="text-sm text-zinc-500 dark:text-zinc-400">Total Bookings</div>
                <div class="mt-1 text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $totalBookings }}</div>
            </div>
            <div class="rounded-xl border border-zinc-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-900">
                <div class="text-sm text-zinc-500 dark:text-zinc-400">Occupancy Rate</div>
                <div class="mt-1 text-2xl font-bold text-amber-600 dark:text-amber-400">{{ $occupancyRate }}%</div>
                <div class="mt-1 text-xs text-zinc-400">{{ $occupiedRooms }}/{{ $totalRooms }} rooms</div>
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-2">
            {{-- Booking Status Breakdown --}}
            <div class="rounded-xl border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900">
                <h3 class="text-sm font-medium text-zinc-900 dark:text-white">Booking Status Breakdown</h3>
                <div class="mt-4 space-y-3">
                    @php
                        $statusData = [
                            ['label' => 'Confirmed', 'value' => $confirmedBookings, 'color' => 'bg-blue-500'],
                            ['label' => 'Checked In', 'value' => $checkedInBookings, 'color' => 'bg-lime-500'],
                            ['label' => 'Checked Out', 'value' => $checkedOutBookings, 'color' => 'bg-zinc-400'],
                            ['label' => 'Cancelled', 'value' => $cancelledBookings, 'color' => 'bg-red-500'],
                            ['label' => 'No Show', 'value' => $noShowBookings, 'color' => 'bg-orange-500'],
                        ];
                    @endphp
                    @foreach($statusData as $stat)
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <div class="size-3 rounded-full {{ $stat['color'] }}"></div>
                                <span class="text-sm text-zinc-600 dark:text-zinc-400">{{ $stat['label'] }}</span>
                            </div>
                            <span class="text-sm font-semibold text-zinc-900 dark:text-white">{{ $stat['value'] }}</span>
                        </div>
                        @if($totalBookings > 0)
                            <div class="ml-5 h-2 overflow-hidden rounded-full bg-zinc-100 dark:bg-zinc-800">
                                <div class="{{ $stat['color'] }} h-2 rounded-full" style="width: {{ ($stat['value'] / $totalBookings) * 100 }}%"></div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>

            {{-- Revenue by Payment Method --}}
            <div class="rounded-xl border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900">
                <h3 class="text-sm font-medium text-zinc-900 dark:text-white">Revenue by Payment Method</h3>
                <div class="mt-4 space-y-4">
                    @forelse($revenueByMethod as $method)
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-zinc-900 dark:text-white">{{ \App\Enums\PaymentMethod::from($method->payment_method)->label() }}</p>
                                <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ $method->count }} {{ Str::plural('payment', $method->count) }}</p>
                            </div>
                            <span class="text-sm font-bold text-zinc-900 dark:text-white">₱{{ number_format($method->total, 2) }}</span>
                        </div>
                    @empty
                        <p class="text-sm text-zinc-500 dark:text-zinc-400">No payments in this period.</p>
                    @endforelse
                </div>

                {{-- Complaints Summary --}}
                <div class="mt-6 border-t border-zinc-200 pt-4 dark:border-zinc-700">
                    <h3 class="text-sm font-medium text-zinc-900 dark:text-white">Complaints</h3>
                    <div class="mt-3 flex items-center justify-between text-sm">
                        <span class="text-zinc-600 dark:text-zinc-400">Total</span>
                        <span class="font-semibold text-zinc-900 dark:text-white">{{ $totalComplaints }}</span>
                    </div>
                    <div class="mt-1 flex items-center justify-between text-sm">
                        <span class="text-zinc-600 dark:text-zinc-400">Resolved/Closed</span>
                        <span class="font-semibold text-green-600 dark:text-green-400">{{ $resolvedComplaints }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Recent Bookings Table --}}
        <div class="rounded-xl border border-zinc-200 bg-white dark:border-zinc-700 dark:bg-zinc-900">
            <div class="border-b border-zinc-200 px-6 py-4 dark:border-zinc-700">
                <h3 class="text-sm font-medium text-zinc-900 dark:text-white">Bookings in Period ({{ $recentBookings->count() }}{{ $totalBookings > 20 ? ' of ' . $totalBookings : '' }})</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                    <thead class="bg-zinc-50 dark:bg-zinc-800">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium tracking-wider text-zinc-500 uppercase dark:text-zinc-400">Reference</th>
                            <th class="px-4 py-3 text-left text-xs font-medium tracking-wider text-zinc-500 uppercase dark:text-zinc-400">Guest</th>
                            <th class="px-4 py-3 text-left text-xs font-medium tracking-wider text-zinc-500 uppercase dark:text-zinc-400">Room</th>
                            <th class="px-4 py-3 text-left text-xs font-medium tracking-wider text-zinc-500 uppercase dark:text-zinc-400">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-medium tracking-wider text-zinc-500 uppercase dark:text-zinc-400">Payment</th>
                            <th class="px-4 py-3 text-right text-xs font-medium tracking-wider text-zinc-500 uppercase dark:text-zinc-400">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                        @forelse($recentBookings as $booking)
                            <tr>
                                <td class="whitespace-nowrap px-4 py-3 text-sm">
                                    <a href="{{ route('bookings.show', $booking) }}" class="text-blue-600 hover:underline dark:text-blue-400" wire:navigate>{{ $booking->booking_reference }}</a>
                                </td>
                                <td class="whitespace-nowrap px-4 py-3 text-sm text-zinc-900 dark:text-white">{{ $booking->guest_name }}</td>
                                <td class="whitespace-nowrap px-4 py-3 text-sm text-zinc-600 dark:text-zinc-400">{{ $booking->room->roomCategory->name }}</td>
                                <td class="whitespace-nowrap px-4 py-3"><flux:badge :color="$booking->booking_status->color()" size="sm">{{ $booking->booking_status->label() }}</flux:badge></td>
                                <td class="whitespace-nowrap px-4 py-3"><flux:badge :color="$booking->payment_status->color()" size="sm">{{ $booking->payment_status->label() }}</flux:badge></td>
                                <td class="whitespace-nowrap px-4 py-3 text-right text-sm font-medium text-zinc-900 dark:text-white">₱{{ number_format($booking->total_amount, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-sm text-zinc-500">No bookings in this period.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
