<?php

namespace App\Filament\Resources\Events\Widgets;

use App\Enums\PublicationStatus;
use App\Filament\Resources\Events\EventResource;
use App\Models\Event;
use Filament\Widgets\Widget;

/**
 * Banner at the top of the event dashboard: the event's visual, dates, venue,
 * publication / ticketing state and a live countdown.
 */
class EventHero extends Widget
{
    protected string $view = 'filament.resources.events.widgets.event-hero';

    // Rendered with the page: cheap to compute, and avoids a layout jump while loading.
    protected static bool $isLazy = false;

    protected int|string|array $columnSpan = 'full';

    public ?Event $record = null;

    protected function getViewData(): array
    {
        $event = $this->record;
        $status = $event->status ?? PublicationStatus::Draft;

        return [
            'event' => $event,
            'backgroundUrl' => $event->getFirstMediaUrl('background') ?: $event->getFirstMediaUrl('poster'),
            'genres' => $event->genres()->pluck('name')->all(),
            'statusLabel' => $status->getDynamicLabel($event->published_at),
            'statusColor' => $status->getDynamicColor($event->published_at),
            'ticketing' => match ($event->ticketing_status) {
                'open' => ['Billetterie ouverte', 'success'],
                'coming_soon' => ['Billetterie bientôt ouverte', 'info'],
                'closed' => ['Billetterie fermée', 'gray'],
                default => ['Pas de billetterie', 'gray'],
            },
            'publicUrl' => $status === PublicationStatus::Published && filled($event->slug)
                ? route('events.show', $event->slug)
                : null,
            'editUrl' => EventResource::getUrl('edit', ['record' => $event]),
        ];
    }
}
