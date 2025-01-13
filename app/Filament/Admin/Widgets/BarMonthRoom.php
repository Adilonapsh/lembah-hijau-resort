<?php

namespace App\Filament\Admin\Widgets;

use App\Models\RoomReportHistory;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class BarMonthRoom extends ChartWidget
{
    protected static ?string $heading = 'Total Kamar Terisi dan Kosong';



    protected function getData(): array
    {
        // $kamar_terisi = RoomReportHistory::groupBy('created_at','id')
        //     ->get()
        //     // ->pluck('total_terisi', 'month')
        //     ->toArray();
        $kamarTerisi = RoomReportHistory::selectRaw('
                EXTRACT(MONTH FROM created_at) as month,
                SUM((data_history->>\'guests_count\')::INTEGER) as total_guests_count,
                SUM((data_history->>\'tersisa\')::INTEGER) as total_tersisa
            ')
            ->groupBy(DB::raw('EXTRACT(MONTH FROM created_at)'))
            ->get();

        $kamar_terisi = $kamarTerisi->pluck("total_guests_count", 'month')->toArray();
        $kamar_tersisa = $kamarTerisi->pluck("total_tersisa", 'month')->toArray();

        $bulan_terisi = [];
        $bulan_tersisa = [];
        for ($i = 1; $i <= 12; $i++) {
            $bulan_terisi[$i] = 0;
            $bulan_tersisa[$i] = 0;
        }
        foreach ($kamar_terisi as $key => $value){
            $bulan_terisi[$key] = $value;
        }
        foreach ($kamar_tersisa as $key => $value){
            $bulan_tersisa[$key] = $value;
        }

        $kamar_terisi = array_values($bulan_terisi);
        $kamar_tersisa = array_values($bulan_tersisa);

        return [
            'labels' => ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
            'datasets' => [
                [
                    'label' => 'Terisi',
                    'data' =>  $kamar_terisi,
                ],
                [
                    'label' => 'Kosong',
                    'data' => $kamar_tersisa,
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
