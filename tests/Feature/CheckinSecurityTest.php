<?php

use App\Models\CheckinList;
use App\Models\Checkout;
use App\Models\Event;
use App\Models\IssuedTicket;
use App\Models\TicketType;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

beforeEach(function () {
    $this->withoutVite();
});

function makeTicket(Event $event, TicketType $ticketType): IssuedTicket
{
    $checkout = Checkout::create([
        'uuid' => (string) Str::uuid(),
        'event_id' => $event->id,
        'customer_name' => 'Jane Buyer',
        'customer_email' => 'jane@example.com',
        'expires_at' => now()->addMinutes(15),
        'completed_at' => now(),
    ]);

    return IssuedTicket::create([
        'event_id' => $event->id,
        'checkout_id' => $checkout->id,
        'reservable_type' => TicketType::class,
        'reservable_id' => $ticketType->id,
        'price_paid' => 1000,
    ]);
}

/**
 * @return array{event: Event, list: CheckinList, ticket: IssuedTicket}
 */
function makeCheckinSetup(?string $password = null): array
{
    $event = makeEvent();
    $ticketType = TicketType::create(['event_id' => $event->id, 'name' => 'Standard', 'capacity' => 100]);

    $list = CheckinList::create([
        'event_id' => $event->id,
        'name' => 'Entrée',
        'public_url_password' => $password,
    ]);
    $list->ticketTypes()->attach($ticketType);

    return ['event' => $event, 'list' => $list, 'ticket' => makeTicket($event, $ticketType)];
}

describe('S1 — ticket toggle/status are scoped to a check-in list', function () {
    it('no longer exposes the unscoped ticket routes', function () {
        ['ticket' => $ticket] = makeCheckinSetup();

        $this->getJson("/checkin/tickets/{$ticket->id}/status")->assertNotFound();
        $this->postJson("/checkin/tickets/{$ticket->id}/toggle")->assertNotFound();

        expect($ticket->fresh()->checked_in_at)->toBeNull();
    });

    it('requires authentication on the admin list routes', function () {
        ['list' => $list, 'ticket' => $ticket] = makeCheckinSetup();

        $this->getJson("/checkin/list/{$list->id}/tickets/{$ticket->id}/status")->assertUnauthorized();
        $this->postJson("/checkin/list/{$list->id}/tickets/{$ticket->id}/toggle")->assertUnauthorized();

        expect($ticket->fresh()->checked_in_at)->toBeNull();
    });

    it('redirects guests opening an admin list to the admin login', function () {
        ['list' => $list] = makeCheckinSetup();

        $this->get("/checkin/list/{$list->id}")->assertRedirect(route('filament.admin.auth.login'));
    });

    it('lets an authenticated user read and toggle a ticket of the list', function () {
        ['list' => $list, 'ticket' => $ticket] = makeCheckinSetup();
        $this->actingAs(User::factory()->create());

        $this->getJson("/checkin/list/{$list->id}/tickets/{$ticket->id}/status")
            ->assertOk()
            ->assertJson(['id' => $ticket->id, 'buyer_name' => 'Jane Buyer', 'checked_in_at' => null]);

        $this->postJson("/checkin/list/{$list->id}/tickets/{$ticket->id}/toggle")->assertOk();
        expect($ticket->fresh()->checked_in_at)->not->toBeNull();

        $this->postJson("/checkin/list/{$list->id}/tickets/{$ticket->id}/toggle")->assertOk();
        expect($ticket->fresh()->checked_in_at)->toBeNull();
    });

    it('returns 404 for a ticket that does not belong to the list', function () {
        ['list' => $list] = makeCheckinSetup();
        ['ticket' => $otherEventTicket] = makeCheckinSetup();

        $this->actingAs(User::factory()->create());

        $this->getJson("/checkin/list/{$list->id}/tickets/{$otherEventTicket->id}/status")->assertNotFound();
        $this->postJson("/checkin/list/{$list->id}/tickets/{$otherEventTicket->id}/toggle")->assertNotFound();

        expect($otherEventTicket->fresh()->checked_in_at)->toBeNull();
    });

    it('returns 404 for a ticket of the same event whose type is not covered by the list', function () {
        ['event' => $event, 'list' => $list] = makeCheckinSetup();
        $vipType = TicketType::create(['event_id' => $event->id, 'name' => 'VIP', 'capacity' => 10]);
        $vipTicket = makeTicket($event, $vipType);

        $this->actingAs(User::factory()->create());

        $this->getJson("/checkin/list/{$list->id}/tickets/{$vipTicket->id}/status")->assertNotFound();
    });

    it('allows the public routes of a list without password', function () {
        ['list' => $list, 'ticket' => $ticket] = makeCheckinSetup();
        $token = $list->public_url_token;

        $this->getJson("/checkin/{$token}/tickets/{$ticket->id}/status")->assertOk();
        $this->postJson("/checkin/{$token}/tickets/{$ticket->id}/toggle")->assertOk();

        expect($ticket->fresh()->checked_in_at)->not->toBeNull();
    });

    it('refuses the public routes of a password-protected list without a session', function () {
        ['list' => $list, 'ticket' => $ticket] = makeCheckinSetup('secret-pass');
        $token = $list->public_url_token;

        $this->getJson("/checkin/{$token}/tickets/{$ticket->id}/status")->assertUnauthorized();
        $this->postJson("/checkin/{$token}/tickets/{$ticket->id}/toggle")->assertUnauthorized();

        expect($ticket->fresh()->checked_in_at)->toBeNull();
    });

    it('scopes the public routes to the list of the token', function () {
        ['list' => $list] = makeCheckinSetup();
        ['ticket' => $otherEventTicket] = makeCheckinSetup();

        $this->getJson("/checkin/{$list->public_url_token}/tickets/{$otherEventTicket->id}/status")->assertNotFound();
    });
});

describe('S7 — check-in list passwords are hashed', function () {
    it('stores the password as a hash and never exposes it', function () {
        ['list' => $list] = makeCheckinSetup('secret-pass');

        $stored = DB::table('checkin_lists')->where('id', $list->id)->value('public_url_password');

        expect($stored)->not->toBe('secret-pass')
            ->and(Hash::check('secret-pass', $stored))->toBeTrue()
            ->and($list->toArray())->not->toHaveKey('public_url_password');
    });

    it('rejects a wrong password', function () {
        ['list' => $list] = makeCheckinSetup('secret-pass');

        $this->post("/checkin/{$list->public_url_token}/auth", ['password' => 'wrong'])
            ->assertSessionHasErrors('password');

        $this->getJson("/checkin/{$list->public_url_token}/tickets/1/status")->assertUnauthorized();
    });

    it('grants access with the right password', function () {
        ['list' => $list, 'ticket' => $ticket] = makeCheckinSetup('secret-pass');
        $token = $list->public_url_token;

        $this->post("/checkin/{$token}/auth", ['password' => 'secret-pass'])
            ->assertRedirect(route('checkin.public', ['token' => $token]));

        $this->getJson("/checkin/{$token}/tickets/{$ticket->id}/status")->assertOk();
    });

    it('revokes existing sessions when the password changes', function () {
        ['list' => $list, 'ticket' => $ticket] = makeCheckinSetup('secret-pass');
        $token = $list->public_url_token;

        $this->post("/checkin/{$token}/auth", ['password' => 'secret-pass']);
        $this->getJson("/checkin/{$token}/tickets/{$ticket->id}/status")->assertOk();

        $list->update(['public_url_password' => 'new-pass']);

        $this->getJson("/checkin/{$token}/tickets/{$ticket->id}/status")->assertUnauthorized();
    });

    it('does not accept a forged session flag', function () {
        ['list' => $list, 'ticket' => $ticket] = makeCheckinSetup('secret-pass');

        $this->withSession(["checkin_auth_{$list->id}" => true])
            ->getJson("/checkin/{$list->public_url_token}/tickets/{$ticket->id}/status")
            ->assertUnauthorized();
    });

    it('hashes plain-text passwords left by the previous schema', function () {
        ['list' => $plain] = makeCheckinSetup();
        ['list' => $empty] = makeCheckinSetup();
        ['list' => $hashed] = makeCheckinSetup('already-hashed');
        $existingHash = DB::table('checkin_lists')->where('id', $hashed->id)->value('public_url_password');

        DB::table('checkin_lists')->where('id', $plain->id)->update(['public_url_password' => 'legacy-pass']);
        DB::table('checkin_lists')->where('id', $empty->id)->update(['public_url_password' => '']);

        (require database_path('migrations/2026_11_26_000000_hash_checkin_list_public_passwords.php'))->up();

        $rows = DB::table('checkin_lists')->pluck('public_url_password', 'id');

        expect(Hash::check('legacy-pass', $rows[$plain->id]))->toBeTrue()
            ->and($rows[$empty->id])->toBeNull()
            ->and($rows[$hashed->id])->toBe($existingHash);
    });
});

describe('S6 — check-in password attempts are rate limited', function () {
    it('blocks the 6th attempt within a minute, even with the right password', function () {
        ['list' => $list] = makeCheckinSetup('secret-pass');
        $url = "/checkin/{$list->public_url_token}/auth";

        foreach (range(1, 5) as $_) {
            $this->post($url, ['password' => 'wrong'])->assertSessionHasErrors('password');
        }

        $this->post($url, ['password' => 'secret-pass'])
            ->assertSessionHasErrors(['password' => 'Trop de tentatives. Réessayez dans une minute.']);
    });
});
