<?php

namespace App\Filament\Resources\Events\Pages;

use App\Filament\Resources\Events\EventResource;
use App\Filament\Resources\Events\Widgets\EventAttendeeStats;
use App\Filament\Resources\Events\Widgets\EventDashboardTitle;
use App\Filament\Resources\Events\Widgets\EventRevenueStats;
use BackedEnum;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;

class EventDashboard extends Page
{
    use InteractsWithRecord;

     protected static string $resource = EventResource::class;

    protected static ?string $navigationLabel = 'Dashboard';

    protected static ?string $breadcrumb = 'Dashboard';

    public function getTitle(): string|Htmlable
    {
        return $this->record->title . ' - Dashboard';
    }

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPresentationChartLine;

    public function mount(int|string $record): void
    {
        $this->record = $this->resolveRecord($record);
    }

    protected function getHeaderWidgets(): array
{
    return [
        EventDashboardTitle::make(['title' => 'Finances', 'icon' => Heroicon::OutlinedCurrencyDollar]),
        EventRevenueStats::class,
        EventDashboardTitle::make(['title' => 'Statistiques', 'icon' => Heroicon::OutlinedUsers]),
        EventAttendeeStats::class,
    ];
}
}
