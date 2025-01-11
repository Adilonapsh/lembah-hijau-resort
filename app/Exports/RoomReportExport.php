<?php

namespace App\Exports;

use App\Models\RoomReportHistory;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class RoomReportExport implements FromCollection, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $room = RoomReportHistory::query();
        $room->select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('SUM(CAST(COALESCE(json_extract_path_text(data_history, \'guests_count\'), \'0\') AS INTEGER)) as total_terisi'),
            DB::raw('SUM(CAST(COALESCE(json_extract_path_text(data_history, \'tersisa\'), \'0\') AS INTEGER)) as total_tersisa'),
        )
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date', 'asc');

        return $room->get();
    }

    public function headings(): array
    {
        return [
            'Date',
            'Total Terisi',
            'Total Tersisa',
        ];
    }
}
