<?php

declare(strict_types=1);

namespace FieldVn\FilamentThemes\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

/**
 * Sample stats for the theme preview page.
 */
final class PreviewStats extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Revenue', '$45,231')
                ->description('+20.1% from last month')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->chart([7, 3, 10, 5, 12, 8, 15])
                ->color('success'),
            Stat::make('New customers', '1,234')
                ->description('-3% this week')
                ->descriptionIcon('heroicon-m-arrow-trending-down')
                ->chart([12, 10, 11, 8, 9, 7, 6])
                ->color('danger'),
            Stat::make('Active accounts', '45,678')
                ->description('Steady'),
        ];
    }
}
