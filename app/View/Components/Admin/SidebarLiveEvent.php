<?php

namespace App\View\Components\Admin;

use App\Filament\Resources\Events\EventResource;
use App\Models\Event;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

/**
 * Sidebar footer card: the next event that is not finished yet, with its ticket sales.
 */
class SidebarLiveEvent extends Component
{
    public ?Event $event;

    public int $sold = 0;

    public int $capacity = 0;

    public function __construct()
    {
        $this->event = Event::query()
            ->notFinished()
            ->withSum('ticketTypes as tickets_sold', 'sold_count')
            ->withSum('ticketTypes as tickets_capacity', 'capacity')
            ->first();

        if ($this->event) {
            $this->sold = (int) $this->event->tickets_sold;
            $this->capacity = (int) $this->event->tickets_capacity;
        }
    }

    public function shouldRender(): bool
    {
        return $this->event !== null;
    }

    public function percentage(): int
    {
        if ($this->capacity <= 0) {
            return 0;
        }

        return (int) min(100, round($this->sold / $this->capacity * 100));
    }

    public function statusLabel(): string
    {
        return match ($this->event->ticketing_status) {
            'open' => 'Ventes ouvertes',
            'coming_soon' => 'Ventes bientôt ouvertes',
            'closed' => 'Ventes clôturées',
            default => 'Prochain événement',
        };
    }

    public function url(): string
    {
        return EventResource::getUrl('dashboard', ['record' => $this->event]);
    }

    public function render(): View|Closure|string
    {
        return view('components.admin.sidebar-live-event');
    }
}
