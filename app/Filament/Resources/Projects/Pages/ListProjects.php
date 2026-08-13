<?php

namespace App\Filament\Resources\Projects\Pages;

use App\Filament\Resources\Projects\ProjectResource;
use App\Filament\Resources\Projects\Widgets\ProjectOverview;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ListProjects extends ListRecords
{
    protected static string $resource = ProjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }


    protected function getHeaderWidgets(): array
    {

        return ProjectResource::getWidgets();

        /* return [
            Stat::make('Unique views', '192.1k')
                ->description('32k increase')
                ->descriptionIcon('heroicon-m-arrow-trending-up'),
            Stat::make('Bounce rate', '21%')
                ->description('7% decrease')
                ->descriptionIcon('heroicon-m-arrow-trending-down'),
            Stat::make('Average time on page', '3:12')
                ->description('3% increase')
                ->descriptionIcon('heroicon-m-arrow-trending-up'),
        ]; */
    }
}
