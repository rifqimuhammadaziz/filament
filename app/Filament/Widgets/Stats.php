<?php

namespace App\Filament\Widgets;

use App\Models\Department;
use App\Models\Employee;
use App\Models\Position;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class Stats extends BaseWidget
{
    protected static ?int $sort = 2;
    protected function getStats(): array
    {
        return [
            Stat::make('Employees', Employee::count())
                ->icon('heroicon-o-user-group')
                ->description('Total number of employees.'),
            Stat::make('Departments', Department::count())
                ->icon('heroicon-o-home-modern')
                ->description('Total number of departments.'),
            Stat::make('Positions', Position::count())
                ->icon('heroicon-o-hashtag')
                ->description('Total number of positions.'),
        ];
    }
}
