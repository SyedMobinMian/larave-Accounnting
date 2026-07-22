<?php

namespace App\Filament\Resources\NoneResource\Widgets;

use Filament\Widgets\ChartWidget;

class RevenueVsExpenseChart extends ChartWidget
{
    protected static ?string $heading = 'Chart';

    protected function getData(): array
    {
        return [
            //
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
