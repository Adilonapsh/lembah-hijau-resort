<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Room;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class RoomTotal extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Kamar', function () {
                return Room::count();
            })
                ->icon('heroicon-o-eye')
                ->color("bg-primary-500"),
            Stat::make('Jumlah Kamar Terisi', function () {
                return Room::query()
                    ->whereHas('guests', function ($query) {
                        $query->where('id', '>', 0);
                    })
                    ->count();
            })
                ->icon('heroicon-o-eye')
                ->color("bg-primary-500"),
            Stat::make('Jumlah Kamar Kosong', function () {
                $has_guests = Room::query()
                    ->whereHas('guests', function ($query) {
                        $query->where('id', '>', 0);
                    })
                    ->count();
                return Room::count() - $has_guests;
            })
                ->icon('heroicon-o-eye')
                ->color("bg-primary-500"),
        ];
    }
}
