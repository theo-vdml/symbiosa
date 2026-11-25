<?php

namespace App\Http\Controllers;

use App\Models\CheckinList;
use App\Models\EventAddon;
use App\Models\IssuedTicket;
use App\Models\TicketType;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CheckinController extends Controller
{
    public function show(CheckinList $checkinList)
    {
        return $this->renderCheckinPage($checkinList);
    }

    public function publicShow($token)
    {
        $checkinList = CheckinList::where('public_url_token', $token)->firstOrFail();

        if ($checkinList->public_url_password && !session("checkin_auth_{$checkinList->id}")) {
            return Inertia::render('Checkin/Password', [
                'checkinList' => [
                    'id' => $checkinList->id,
                    'name' => $checkinList->name,
                    'token' => $checkinList->public_url_token,
                ]
            ]);
        }

        return $this->renderCheckinPage($checkinList, true);
    }

    public function authenticate(Request $request, $token)
    {
        $checkinList = CheckinList::where('public_url_token', $token)->firstOrFail();

        $request->validate([
            'password' => 'required|string',
        ]);

        if ($request->password === $checkinList->public_url_password) {
            session(["checkin_auth_{$checkinList->id}" => true]);
            return redirect()->route('checkin.public', ['token' => $token]);
        }

        return back()->withErrors(['password' => 'Mot de passe incorrect.']);
    }

    protected function renderCheckinPage(CheckinList $checkinList, $isPublic = false)
    {
        $checkinList->load(['event']);

        $query = $this->getTicketsQuery($checkinList);

        $stats = [
            'total' => $query->count(),
            'scanned' => (clone $query)->whereNotNull('checked_in_at')->count(),
        ];

        return Inertia::render('Checkin/Index', [
            'checkinList' => [
                'id' => $checkinList->id,
                'name' => $checkinList->name,
                'event_title' => $checkinList->event->title,
                'public_url_token' => $checkinList->public_url_token,
            ],
            'stats' => $stats,
            'isPublic' => $isPublic,
            'scan_result' => session('scan_result'),
            'availableFilters' => [
                'ticket_types' => $checkinList->ticketTypes()->get(['ticket_types.id', 'name'])->map(fn($t) => ['id' => $t->id, 'name' => $t->name]),
                'addons' => $checkinList->addons()->get(['event_addons.id', 'name'])->map(fn($a) => ['id' => $a->id, 'name' => $a->name]),
            ]
        ]);
    }

    public function search(Request $request, CheckinList $checkinList)
    {
        $query = $request->input('q');

        if (!$query || strlen($query) < 2) {
            return response()->json([]);
        }

        $tickets = $this->getTicketsQuery($checkinList)
            ->with(['reservable', 'checkout', 'ticketPrice'])
            ->where(function ($q) use ($query) {
                $q->whereHas('checkout', function ($sub) use ($query) {
                    $sub->where('customer_name', 'like', "%{$query}%")
                        ->orWhere('customer_email', 'like', "%{$query}%");
                })->orWhere('public_id', 'like', "%{$query}%");
            })
            ->limit(50)
            ->get()
            ->map(fn(IssuedTicket $ticket) => $this->ticketPayload($ticket));

        return response()->json($tickets);
    }

    public function publicSearch(Request $request, $token)
    {
        $checkinList = CheckinList::where('public_url_token', $token)->firstOrFail();

        if ($checkinList->public_url_password && !session("checkin_auth_{$checkinList->id}")) {
            return response()->json(['success' => false, 'message' => 'Non autorisé'], 401);
        }

        return $this->search($request, $checkinList);
    }

    public function getTicketStatus(CheckinList $checkinList, IssuedTicket $issuedTicket)
    {
        $this->ensureTicketBelongsToList($checkinList, $issuedTicket);

        return response()->json($this->ticketPayload($issuedTicket));
    }

    public function publicTicketStatus($token, IssuedTicket $issuedTicket)
    {
        $checkinList = CheckinList::where('public_url_token', $token)->firstOrFail();

        if ($checkinList->public_url_password && !session("checkin_auth_{$checkinList->id}")) {
            return response()->json(['success' => false, 'message' => 'Non autorisé'], 401);
        }

        return $this->getTicketStatus($checkinList, $issuedTicket);
    }

    public function toggleCheckin(Request $request, CheckinList $checkinList, IssuedTicket $issuedTicket)
    {
        $this->ensureTicketBelongsToList($checkinList, $issuedTicket);

        if ($issuedTicket->checked_in_at) {
            $issuedTicket->update(['checked_in_at' => null]);
        } else {
            $issuedTicket->update(['checked_in_at' => now()]);
        }

        if ($request->header('X-Inertia')) {
            return back()->with('scan_result', [
                'success' => true,
                'status' => $issuedTicket->checked_in_at ? 'success' : 'cancelled_checkin',
                'message' => $issuedTicket->checked_in_at ? 'Billet validé' : 'Validation annulée',
                'ticket' => $this->ticketPayload($issuedTicket),
            ]);
        }

        if ($request->wantsJson()) {
            return response()->json([
                'checked_in_at' => $issuedTicket->checked_in_at,
            ]);
        }

        return back();
    }

    public function publicToggleCheckin(Request $request, $token, IssuedTicket $issuedTicket)
    {
        $checkinList = CheckinList::where('public_url_token', $token)->firstOrFail();

        if ($checkinList->public_url_password && !session("checkin_auth_{$checkinList->id}")) {
            return response()->json(['success' => false, 'message' => 'Non autorisé'], 401);
        }

        return $this->toggleCheckin($request, $checkinList, $issuedTicket);
    }

    public function publicScan(Request $request, $token)
    {
        $checkinList = CheckinList::where('public_url_token', $token)->firstOrFail();

        if ($checkinList->public_url_password && !session("checkin_auth_{$checkinList->id}")) {
            return response()->json(['success' => false, 'message' => 'Non autorisé'], 401);
        }

        return $this->scan($request, $checkinList);
    }

    public function scan(Request $request, CheckinList $checkinList)
    {
        $request->validate([
            'public_id' => 'required|string',
        ]);

        $ticket = IssuedTicket::where('public_id', $request->public_id)
            ->where('event_id', $checkinList->event_id)
            ->with(['reservable', 'checkout'])
            ->first();

        if (!$ticket) {
            $error = ['success' => false, 'status' => 'invalid', 'message' => 'Ticket introuvable'];
            if ($request->header('X-Inertia')) {
                return back()->with('scan_result', $error);
            }
            return response()->json($error, 404);
        }

        if (!$this->ticketBelongsToList($checkinList, $ticket)) {
            $error = ['success' => false, 'status' => 'unauthorized', 'message' => 'Ce ticket n\'est pas autorisé pour cette liste'];
            if ($request->header('X-Inertia')) {
                return back()->with('scan_result', $error);
            }
            return response()->json($error, 403);
        }

        $alreadyCheckedIn = $ticket->checked_in_at !== null;

        if (!$alreadyCheckedIn) {
            $ticket->update(['checked_in_at' => now()]);
        }

        $result = [
            'success' => true,
            'status' => $alreadyCheckedIn ? 'already_scanned' : 'success',
            'message' => $alreadyCheckedIn ? 'Déjà scanné' : 'Billet validé',
            'ticket' => $this->ticketPayload($ticket),
        ];

        if ($request->header('X-Inertia')) {
            return back()->with('scan_result', $result);
        }

        return response()->json(array_merge($result, [
            'stats' => [
                'total' => $this->getTicketsQuery($checkinList)->count(),
                'scanned' => $this->getTicketsQuery($checkinList)->whereNotNull('checked_in_at')->count(),
            ]
        ]));
    }

    /**
     * Whether the ticket is for the list's event and of a ticket type / addon covered by the list.
     */
    protected function ticketBelongsToList(CheckinList $checkinList, IssuedTicket $ticket): bool
    {
        if ((int) $ticket->event_id !== (int) $checkinList->event_id) {
            return false;
        }

        return match ($ticket->reservable_type) {
            TicketType::class => $checkinList->ticketTypes()->where('ticket_types.id', $ticket->reservable_id)->exists(),
            EventAddon::class => $checkinList->addons()->where('event_addons.id', $ticket->reservable_id)->exists(),
            default => false,
        };
    }

    /**
     * Abort with a 404 (not 403, to avoid confirming the ticket exists) when the ticket is outside the list.
     */
    protected function ensureTicketBelongsToList(CheckinList $checkinList, IssuedTicket $ticket): void
    {
        abort_unless($this->ticketBelongsToList($checkinList, $ticket), 404);
    }

    protected function ticketPayload(IssuedTicket $ticket): array
    {
        $ticket->loadMissing(['reservable', 'checkout', 'ticketPrice']);

        return [
            'id' => $ticket->id,
            'public_id' => $ticket->public_id,
            'buyer_name' => $ticket->checkout?->customer_name,
            'buyer_email' => $ticket->checkout?->customer_email,
            'reservable_name' => $ticket->reservable?->name,
            'reservable_id' => $ticket->reservable_id,
            'reservable_type' => str_replace('App\\Models\\', '', $ticket->reservable_type),
            'price_name' => $ticket->ticketPrice?->name,
            'checked_in_at' => $ticket->checked_in_at?->toIso8601String(),
        ];
    }

    protected function getTicketsQuery(CheckinList $checkinList)
    {
        return IssuedTicket::where('event_id', $checkinList->event_id)
            ->where(function ($q) use ($checkinList) {
                $q->where(function ($sub) use ($checkinList) {
                    $sub->where('reservable_type', TicketType::class)
                        ->whereIn('reservable_id', $checkinList->ticketTypes()->pluck('ticket_types.id'));
                })->orWhere(function ($sub) use ($checkinList) {
                    $sub->where('reservable_type', EventAddon::class)
                        ->whereIn('reservable_id', $checkinList->addons()->pluck('event_addons.id'));
                });
            });
    }
}
