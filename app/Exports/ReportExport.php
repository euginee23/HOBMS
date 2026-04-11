<?php

namespace App\Exports;

use App\Exports\Sheets\BookingsSheet;
use App\Exports\Sheets\ReportSummarySheet;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ReportExport implements WithMultipleSheets
{
    public function __construct(
        public Carbon $from,
        public Carbon $to,
    ) {}

    /**
     * @return array<int, BookingsSheet|ReportSummarySheet>
     */
    public function sheets(): array
    {
        return [
            new ReportSummarySheet($this->from, $this->to),
            new BookingsSheet($this->from, $this->to),
        ];
    }
}
