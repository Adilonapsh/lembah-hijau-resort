<?php

namespace App\Filament\Admin\Widgets;

use Filament\Widgets\ChartWidget;

class StatusRoomData extends ChartWidget
{
    protected static ?string $heading = 'Status Data Kamar';

    protected static ?int $height = 20;

    protected function getData(): array
    {
        return [
            'labels' => ['Available', 'Occupied', 'Reserved'],
            'datasets' => [
                [
                    'data' => [10, 5, 3],
                    'backgroundColor' => ['#4CAF50', '#FF9800', '#F44336'],
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
