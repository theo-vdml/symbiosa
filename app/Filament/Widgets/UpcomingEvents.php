<?php

namespace App\Filament\Widgets;

use App\Enums\PublicationStatus;
use App\Filament\Resources\Events\EventResource;
use App\Models\Event;
use Filament\Widgets\Widget;
use Illuminate\Support\Collection;

/**
 * Home dashboard: the events that are not finished yet, with their ticket sales.
 */
class UpcomingEvents extends Widget
{
    protected string $view = 'filament.widgets.upcoming-events';

    // Rendered with the page: cheap to compute, and avoids a layout jump while loading.
    protected static bool $isLazy = false;

    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 1;

    protected function getViewData(): array
    {
        return [
            'events' => $this->getEvents(),
            'createUrl' => EventResource::getUrl('create'),
            'indexUrl' => EventResource::getUrl('index'),
        ];
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    protected function getEvents(): Collection
    {
        return Event::query()
            ->notFinished()
            ->withSum('ticketTypes as tickets_sold', 'sold_count')
            ->withSum('ticketTypes as tickets_capacity', 'capacity')
            ->limit(4)
            ->get()
            ->map(function (Event $event): array {
                $status = $event->status ?? PublicationStatus::Draft;
                $sold = (int) $event->tickets_sold;
                $capacity = (int) $event->tickets_capacity;

                return [
                    'title' => $event->title,
                    'startsAt' => $event->start_at,
                    'city' => $event->city,
                    'statusLabel' => $status->getDynamicLabel($event->published_at),
                    'statusColor' => $status->getDynamicColor($event->published_at),
                    'sold' => $sold,
                    'capacity' => $capacity,
                    'percentage' => $capacity > 0 ? (int) min(100, round($sold / $capacity * 100)) : 0,
                    'url' => EventResource::getUrl('dashboard', ['record' => $event]),
                ];
            });
    }
}
