<?php

namespace App\Filament\Resources\Projects\Widgets;

use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\Widget;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class ProjectOverview extends BaseWidget
{
    //protected string $view = 'filament.resources.projects.widgets.project-overview';


    protected function getStats(): array
    {
        return [
            Stat::make('En ejecución', '18')
                ->description('3 iniciados esta semana')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),

            Stat::make('Avance promedio', '74%')
                ->description('6% más que el mes pasado')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('primary'),

            Stat::make('Presupuesto ejecutado', '$1.25M')
                ->description('82% del presupuesto total')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('info'),
        ];
    }
}
