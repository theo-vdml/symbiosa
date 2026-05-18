<?php

namespace App\Filament\Resources\Events\Widgets;

use App\Models\TicketType;
use App\Models\EventAddon;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class EventAttendeeStats extends StatsOverviewWidget
{

    public ?\App\Models\Event $record = null;

    protected function getStats(): array
    {
        $completedReservations = $this->record->reservations()
            ->whereNotNull('completed_at');

        $pendingReservations = $this->record->reservations()
            ->whereHas('checkout', fn($q) => $q->isPending());

        $ticketsSold = (int) (clone $completedReservations)
            ->where('reservable_type', TicketType::class)
            ->sum('quantity');

        $reservedTickets = (int) (clone $pendingReservations)
            ->where('reservable_type', TicketType::class)
            ->sum('quantity');

        $addonsSold = (int) (clone $completedReservations)
            ->where('reservable_type', EventAddon::class)
            ->sum('quantity');

        $reservedAddons = (int) (clone $pendingReservations)
            ->where('reservable_type', EventAddon::class)
            ->sum('quantity');

        $totalCapacity = (int) $this->record->ticketTypes()->sum('capacity');

        $addonCapacity = (int) $this->record->addons()->sum('capacity');

        $checkins = $this->record->issuedTickets()
            ->whereNotNull('checked_in_at')
            ->count();

        $occupancyRate = $totalCapacity > 0 ? round(($ticketsSold / $totalCapacity) * 100, 1) : 0;

        return [
            Stat::make('total_tickets_sold', "{$ticketsSold} / {$totalCapacity}")
                ->label('Entrées vendues')
                ->icon('heroicon-o-ticket')
                ->description('Tout type de billet confondu')
                ->color('info'),

            Stat::make('pending_tickets', $reservedTickets)
                ->label('Entrées réservées')
                ->icon(Heroicon::OutlinedClock)
                ->description('Billets en attente de paiement')
                ->color('warning'),

            Stat::make('occupancy_rate', $occupancyRate . ' %')
                ->label('Taux de remplissage')
                ->icon('heroicon-o-chart-bar')
                ->color($occupancyRate > 80 ? 'success' : ($occupancyRate > 50 ? 'warning' : 'danger')),

            Stat::make('addons_sold', "{$addonsSold} / {$addonCapacity}")
                ->label('Extras vendus')
                ->icon('heroicon-o-shopping-cart')
                ->description('Tout type d\'extra confondu')
                ->color('info'),

            Stat::make('pending_addons', $reservedAddons)
                ->label('Extras réservés')
                ->icon(Heroicon::OutlinedClock)
                ->description('Extras en attente de paiement')
                ->color('warning'),

            Stat::make('checkins', $checkins)
                ->label('Check-ins')
                ->description($ticketsSold > 0 ? round(($checkins / $ticketsSold) * 100, 1) . ' % de présence' : null)
                ->icon('heroicon-o-check-circle')
                ->color('secondary'),
        ];
    }
}
