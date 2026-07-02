<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use App\Filament\Widgets\DailyStatisticsWidget;
use App\Filament\Widgets\SchoolOverviewWidget;
use App\Filament\Widgets\SchoolRatesWidget;
use App\Filament\Widgets\StudentStatusWidget;
use App\Filament\Widgets\SubjectSuccessChart;
use App\Filament\Widgets\SuccessRateBySection;
use App\Filament\Widgets\TopStudentsWidget;

class Dashboard extends BaseDashboard
{
    public function getWidgets(): array
    {
        return [
            SchoolOverviewWidget::class,
            DailyStatisticsWidget::class,
            SchoolRatesWidget::class,
            SubjectSuccessChart::class,
            TopStudentsWidget::class,
            SuccessRateBySection::class,
            StudentStatusWidget::class,
        ];
    }
}
