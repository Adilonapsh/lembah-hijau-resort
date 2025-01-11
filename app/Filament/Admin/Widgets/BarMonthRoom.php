<?php

namespace App\Filament\Admin\Widgets;

use Filament\Widgets\ChartWidget;

class BarMonthRoom extends ChartWidget
{
    protected static ?string $heading = 'Total Kamar Terisi dan Kosong';



    protected function getData(): array
    {
        return [
            'labels' => ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
            'datasets' => [
                [
                    'label' => 'Terisi',
                    'data' => [10, 20, 30, 40, 50, 60, 70, 80, 90, 100, 110, 120],
                ],
                [
                    'label' => 'Kosong',
                    'data' => [120, 110, 100, 90, 80, 70, 60, 50, 40, 30, 20, 10],
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
