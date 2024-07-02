<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;

class UniqueViews extends ChartWidget
{
    protected static ?string $heading = 'Unique Views';
    protected int|string|array $columnSpan = 1;
    // protected static ?string $maxHeight = '300px';

    protected function getData(
    ): array {
        return [
            'datasets' => [
                [
                    'label' => 'Blog posts created',
                    'data' => [0, 10, 5, 21, 20, 30, 40, 22, 20, 12, 8],
                    'fill' => 'start',
                    'borderColor' => '#10b981',
                    'backgroundColor' => 'rgba(16, 185, 129, .2)'
                ],
            ],
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
