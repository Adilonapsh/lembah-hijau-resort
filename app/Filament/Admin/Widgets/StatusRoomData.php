<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Room;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class StatusRoomData extends ChartWidget
{
    protected static ?string $heading = 'Status Data Kamar';

    protected static ?int $height = 20;

    protected function getData(): array
    {

        $query = Room::query()
            ->select(
                'rooms.*',
                DB::raw('MIN(guests.tanggal_checkin) as tanggal_checkin_awal'),
                DB::raw('MAX(guests.tanggal_checkout) as tanggal_checkout_terakhir'),
                DB::raw('(rooms.kapasitas - COUNT(guests.id)) as tersisa'),
            )
            ->leftJoin('guests', function ($join) {
                $join->on('rooms.id', '=', 'guests.id_kamar')
                    ->whereNotNull('guests.tanggal_checkin')
                    ->whereNull('guests.tanggal_checkout');
            })
            ->groupBy('rooms.id')
            ->with(['guests' => function ($query) {
                $query->select(
                    'id',
                    'nama',
                    'id_kamar',
                    'id_kelas',
                    'batch',
                    'tanggal_checkin',
                    'tanggal_checkout'
                )
                    ->whereNotNull('guests.tanggal_checkin')
                    ->whereNull('guests.tanggal_checkout')
                    ->orderBy('tanggal_checkin');
            }])
            ->withCount(['guests' => function ($query) {
                $query->whereNotNull('tanggal_checkin')
                    ->whereNull('tanggal_checkout');
            }]);

        $kamarTerisi = $query->get()->sum('guests_count') ?? 0;
        $kamarKosong = Room::sum('kapasitas') - $kamarTerisi ?? 0;
        return [
            'labels' => ['Kamar Terisi', 'Kamar Kosong'],
            'datasets' => [
                [
                    'data' => [$kamarTerisi, $kamarKosong],
                    'backgroundColor' => ['#4CAF50', '#FF9800'],
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
