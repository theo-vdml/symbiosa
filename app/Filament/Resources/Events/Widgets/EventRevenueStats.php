<?php

namespace App\Filament\Resources\Events\Widgets;

use App\Models\TicketType;
use App\Models\EventAddon;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class EventRevenueStats extends StatsOverviewWidget
{
    public ?\App\Models\Event $record = null;

    public function getColumns(): int | array
    {
        return 2;
    }

    protected function getStats(): array
    {
        // 1. Base des réservations terminées
        $completedReservations = $this->record->reservations()
            ->whereNotNull('completed_at');

        // Totaux généraux actuels (le chiffre final affiché)
        $totalRevenue = (float) ((clone $completedReservations)->selectRaw('SUM(quantity * unit_price) as total')->value('total') ?? 0) / 100;
        $ticketRevenue = (float) ((clone $completedReservations)->where('reservable_type', TicketType::class)->selectRaw('SUM(quantity * unit_price) as total')->value('total') ?? 0) / 100;
        $addonsRevenue = (float) ((clone $completedReservations)->where('reservable_type', EventAddon::class)->selectRaw('SUM(quantity * unit_price) as total')->value('total') ?? 0) / 100;
        $pendingRevenue = (float) ($this->record->reservations()->whereHas('checkout', fn($q) => $q->isPending())->selectRaw('SUM(quantity * unit_price) as total')->value('total') ?? 0) / 100;

        // --- CALCUL DE L'ÉVOLUTION CUMULÉE (14 DERNIERS JOURS) ---

        $startDate = Carbon::now()->subDays(13)->startOfDay();
        $days = collect(range(13, 0))->map(fn($i) => Carbon::now()->subDays($i)->format('Y-m-d'));

        // A. Valeurs de départ (Somme de tout ce qui a été vendu AVANT les 14 derniers jours)
        $startingTotal = (float) ((clone $completedReservations)->where('completed_at', '<', $startDate)->selectRaw('SUM(quantity * unit_price) as total')->value('total') ?? 0) / 100;
        $startingTicket = (float) ((clone $completedReservations)->where('reservable_type', TicketType::class)->where('completed_at', '<', $startDate)->selectRaw('SUM(quantity * unit_price) as total')->value('total') ?? 0) / 100;
        $startingAddons = (float) ((clone $completedReservations)->where('reservable_type', EventAddon::class)->where('completed_at', '<', $startDate)->selectRaw('SUM(quantity * unit_price) as total')->value('total') ?? 0) / 100;

        // B. Ventes quotidiennes durant les 14 derniers jours
        $totalDaily = (clone $completedReservations)->where('completed_at', '>=', $startDate)->selectRaw('DATE(completed_at) as date, SUM(quantity * unit_price) as total')->groupBy('date')->pluck('total', 'date');
        $ticketDaily = (clone $completedReservations)->where('reservable_type', TicketType::class)->where('completed_at', '>=', $startDate)->selectRaw('DATE(completed_at) as date, SUM(quantity * unit_price) as total')->groupBy('date')->pluck('total', 'date');
        $addonsDaily = (clone $completedReservations)->where('reservable_type', EventAddon::class)->where('completed_at', '>=', $startDate)->selectRaw('DATE(completed_at) as date, SUM(quantity * unit_price) as total')->groupBy('date')->pluck('total', 'date');

        // C. Construction des tableaux cumulés (Chaque jour = jour précédent + ventes du jour)
        $totalChart = [];
        $ticketChart = [];
        $addonsChart = [];

        foreach ($days as $date) {
            // Cumul Général
            $dayTotalSales = ((float) ($totalDaily->get($date) ?? 0)) / 100;
            $startingTotal += $dayTotalSales; // On ajoute au cumul existant
            $totalChart[] = $startingTotal;

            // Cumul Billets
            $dayTicketSales = ((float) ($ticketDaily->get($date) ?? 0)) / 100;
            $startingTicket += $dayTicketSales;
            $ticketChart[] = $startingTicket;

            // Cumul Extras
            $dayAddonsSales = ((float) ($addonsDaily->get($date) ?? 0)) / 100;
            $startingAddons += $dayAddonsSales;
            $addonsChart[] = $startingAddons;
        }

        return [
            Stat::make('total_revenue', number_format($totalRevenue, 2, ',', ' ') . ' €')
                ->label('Chiffre d\'affaires')
                ->icon(Heroicon::OutlinedCurrencyEuro)
                ->color('success')
                ->chart($totalChart), // Graphique cumulé (ne fait que monter)

            Stat::make('ticket_revenue', number_format($ticketRevenue, 2, ',', ' ') . ' €')
                ->label('Revenus des billets')
                ->icon(Heroicon::OutlinedTicket)
                ->color('info')
                ->chart($ticketChart), // Graphique cumulé

            Stat::make('addons_revenue', number_format($addonsRevenue, 2, ',', ' ') . ' €')
                ->label('Revenus des extras')
                ->icon(Heroicon::OutlinedShoppingCart)
                ->color('primary')
                ->chart($addonsChart), // Graphique cumulé

            Stat::make('pending_revenue', number_format($pendingRevenue, 2, ',', ' ') . ' €')
                ->label('Revenus en attente')
                ->icon(Heroicon::OutlinedClock)
                ->description('Montant des réservations en cours de paiement')
                ->color('warning'),
        ];
    }
}
