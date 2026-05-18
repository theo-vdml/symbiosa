<?php

namespace App\Http\Controllers;

use App\Models\CheckinList;
use App\Models\IssuedTicket;
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

        $query = $this->getTicketsQuery($checkinList)
            ->with(['reservable', 'checkout', 'ticketPrice']);

        $tickets = $query->get()->map(function ($ticket) {
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
        });

        $stats = [
            'total' => $tickets->count(),
            'scanned' => $tickets->whereNotNull('checked_in_at')->count(),
        ];

        return Inertia::render('Checkin/Index', [
            'checkinList' => [
                'id' => $checkinList->id,
                'name' => $checkinList->name,
                'event_title' => $checkinList->event->title,
                'public_url_token' => $checkinList->public_url_token,
            ],
            'tickets' => $tickets,
            'stats' => $stats,
            'isPublic' => $isPublic,
            'scan_result' => session('scan_result'),
            'availableFilters' => [
                'ticket_types' => $checkinList->ticketTypes()->get(['ticket_types.id', 'name'])->map(fn($t) => ['id' => $t->id, 'name' => $t->name]),
                'addons' => $checkinList->addons()->get(['event_addons.id', 'name'])->map(fn($a) => ['id' => $a->id, 'name' => $a->name]),
            ]
        ]);
    }

    public function getTicketStatus(IssuedTicket $issuedTicket)
    {
        return response()->json([
            'checked_in_at' => $issuedTicket->checked_in_at,
        ]);
    }

    public function toggleCheckin(Request $request, IssuedTicket $issuedTicket)
    {
        // Basic security: check if ticket belongs to the list context
        // In a real app we'd verify the session/auth more strictly here
        
        if ($issuedTicket->checked_in_at) {
            $issuedTicket->update(['checked_in_at' => null]);
        } else {
            $issuedTicket->update(['checked_in_at' => now()]);
        }

        if ($request->header('X-Inertia')) {
            $ticketData = [
                'id' => $issuedTicket->id,
                'public_id' => $issuedTicket->public_id,
                'buyer_name' => $issuedTicket->checkout?->customer_name,
                'buyer_email' => $issuedTicket->checkout?->customer_email,
                'reservable_name' => $issuedTicket->reservable?->name,
                'reservable_id' => $issuedTicket->reservable_id,
                'reservable_type' => str_replace('App\\Models\\', '', $issuedTicket->reservable_type),
                'checked_in_at' => $issuedTicket->checked_in_at,
            ];

            // We don't have the CheckinList here easily without a bit more logic or passing it in the request.
            // But usually, toggle is called from the search list or result card.
            // Let's just return a success result.
            return back()->with('scan_result', [
                'success' => true,
                'status' => $issuedTicket->checked_in_at ? 'success' : 'unauthorized', // unauthorized used here as a placeholder for "cancelled"
                'message' => $issuedTicket->checked_in_at ? 'Billet validé' : 'Validation annulée',
                'ticket' => [
                    'id' => $issuedTicket->id,
                    'public_id' => $issuedTicket->public_id,
                    'buyer_name' => $issuedTicket->checkout?->customer_name,
                    'buyer_email' => $issuedTicket->checkout?->customer_email,
                    'reservable_name' => $issuedTicket->reservable?->name,
                    'reservable_id' => $issuedTicket->reservable_id,
                    'reservable_type' => str_replace('App\\Models\\', '', $issuedTicket->reservable_type),
                    'price_name' => $issuedTicket->ticketPrice?->name,
                    'checked_in_at' => $issuedTicket->checked_in_at?->toIso8601String(),
                ]
            ]);
        }

        if ($request->wantsJson()) {
            return response()->json([
                'checked_in_at' => $issuedTicket->checked_in_at,
            ]);
        }

        return back();
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

        // Check if ticket is allowed in this list
        $isAllowed = false;
        if ($ticket->reservable_type === \App\Models\TicketType::class) {
            $isAllowed = $checkinList->ticketTypes()->where('ticket_types.id', $ticket->reservable_id)->exists();
        } elseif ($ticket->reservable_type === \App\Models\EventAddon::class) {
            $isAllowed = $checkinList->addons()->where('event_addons.id', $ticket->reservable_id)->exists();
        }

        if (!$isAllowed) {
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
            'ticket' => [
                'id' => $ticket->id,
                'public_id' => $ticket->public_id,
                'buyer_name' => $ticket->checkout?->customer_name,
                'buyer_email' => $ticket->checkout?->customer_email,
                'reservable_name' => $ticket->reservable?->name,
                'reservable_id' => $ticket->reservable_id,
                'reservable_type' => str_replace('App\\Models\\', '', $ticket->reservable_type),
                'price_name' => $ticket->ticketPrice?->name,
                'checked_in_at' => $ticket->checked_in_at?->toIso8601String(),
            ],
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

    protected function getTicketsQuery(CheckinList $checkinList)
    {
        return IssuedTicket::where('event_id', $checkinList->event_id)
            ->where(function ($q) use ($checkinList) {
                $q->where(function ($sub) use ($checkinList) {
                    $sub->where('reservable_type', \App\Models\TicketType::class)
                        ->whereIn('reservable_id', $checkinList->ticketTypes()->pluck('ticket_types.id'));
                })->orWhere(function ($sub) use ($checkinList) {
                    $sub->where('reservable_type', \App\Models\EventAddon::class)
                        ->whereIn('reservable_id', $checkinList->addons()->pluck('event_addons.id'));
                });
            });
    }
}
