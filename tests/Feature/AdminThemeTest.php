<?php

use App\Enums\PublicationStatus;
use App\Models\Event;
use App\Models\TicketType;
use App\Models\User;
use Illuminate\Support\Str;

beforeEach(function () {
    $this->withoutVite();
});

function themeEvent(array $attributes = []): Event
{
    return Event::create([
        'title' => 'Nuit Verte',
        'slug' => 'nuit-verte-'.Str::lower(Str::random(6)),
        'start_at' => now()->addWeeks(2),
        'end_at' => now()->addWeeks(2)->addHours(8),
        'city' => 'Liège',
        'country' => 'Belgique',
        'address' => 'Hangar 58',
        'description' => 'Description',
        'status' => PublicationStatus::Published,
        'ticketing_starts_at' => now()->subWeek(),
        ...$attributes,
    ]);
}

function themeTicketType(Event $event, int $capacity, int $sold): TicketType
{
    return TicketType::create([
        'event_id' => $event->id,
        'name' => 'Prévente',
        'capacity' => $capacity,
        'sold_count' => $sold,
        'reserved_count' => 0,
        'max_per_order' => 10,
        'sort_order' => 0,
    ]);
}

it('shows the brand panel on the login page', function () {
    $this->get('/admin/login')
        ->assertOk()
        ->assertSee('sy-auth-aside', false)
        ->assertSee('Préparer la prochaine');
});

it('shows upcoming events and the next event card on the home dashboard', function () {
    $event = themeEvent();
    themeTicketType($event, 600, 412);

    $this->actingAs(User::factory()->create())
        ->get('/admin')
        ->assertOk()
        ->assertSee('Prochains événements')
        ->assertSee('Nuit Verte')
        ->assertSee('412 / 600 billets')
        ->assertSee('Ventes ouvertes');
});

it('shows an empty state and no sidebar card when no event is planned', function () {
    themeEvent([
        'start_at' => now()->subMonth(),
        'end_at' => now()->subMonth()->addHours(8),
    ]);

    $this->actingAs(User::factory()->create())
        ->get('/admin')
        ->assertOk()
        ->assertSee('Aucun événement à venir')
        ->assertDontSee('sy-live-event', false);
});

it('shows the event banner on the event dashboard', function () {
    $event = themeEvent();
    themeTicketType($event, 200, 50);

    $this->actingAs(User::factory()->create())
        ->get("/admin/events/{$event->getKey()}/dashboard")
        ->assertOk()
        ->assertSee('sy-event-hero', false)
        ->assertSee('Hangar 58, Liège')
        ->assertSee('Billetterie ouverte')
        ->assertSee(route('events.show', $event->slug), false);
});

it('flags non-production environments in the topbar', function () {
    $this->actingAs(User::factory()->create())
        ->get('/admin')
        ->assertOk()
        ->assertSee('sy-env-badge', false)
        ->assertSee('TESTING');
});
