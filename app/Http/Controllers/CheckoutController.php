<?php

namespace App\Http\Controllers;

use App\Enums\ReservableStatus;
use App\Models\Checkout;
use App\Models\Event;
use App\Models\EventAddon;
use App\Models\TicketPrice;
use App\Models\TicketType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Str;

class CheckoutController extends Controller
{
    public function store(Request $request, Event $event)
    {

        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.qty' => 'required|integer|min:1|max:99',
            'items.*.type' => 'required|in:ticket,addon',
            'items.*.productId' => 'required|integer',
            'items.*.priceId' => 'nullable|required_if:items.*.type,ticket|integer',
        ]);

        $checkout = DB::transaction(function () use ($request, $event) {

            $checkout = Checkout::create([
                'uuid' => (string) Str::uuid(),
                'expires_at' => now()->addMinutes(15),
            ]);

            foreach ($request->items as $item) {
                if ($item['type'] === 'ticket') {
                    $this->handleTicketReservation($checkout, $item);
                } else {
                    $this->handleAddonReservation($checkout, $item);
                }
            }

            return $checkout;
        });

        // Redirect to the checkout page
    }

    private function handleTicketReservation(Checkout $checkout, array $item)
    {
        $price = TicketPrice::with('ticketType')->findOrFail($item['priceId']);
        /** @var TicketType $ticketType */
        $ticketType = $price->ticketType;

        if ($price->status !== ReservableStatus::OPEN) {
            throw ValidationException::withMessages([
                'cart' => "Le tarif pour '{$ticketType->name}' a changé ou n'est plus disponible."
            ]);
        }

        if (!$ticketType->hasStockFor($item['qty'])) {
            throw ValidationException::withMessages([
                'cart' => "Il ne reste plus assez de places pour '{$ticketType->name}'."
            ]);
        }

        $checkout->reservations()->create([
            'reservable_id' => $ticketType->id,
            'reservable_type' => TicketType::class,
            'quantity' => $item['qty'],
            'unit_price' => $price->price,
        ]);
    }

    private function handleAddonReservation(Checkout $checkout, array $item)
    {
        $addon = EventAddon::findOrFail($item['productId']);

        if ($addon->status !== ReservableStatus::OPEN) {
            throw ValidationException::withMessages([
                'cart' => "L'addon '{$addon->name}' n'est plus disponible."
            ]);
        }

        if (!$addon->hasStockFor($item['qty'])) {
            throw ValidationException::withMessages([
                'cart' => "Il ne reste plus assez de stock pour l'addon '{$addon->name}'."
            ]);
        }

        $checkout->reservations()->create([
            'reservable_id' => $addon->id,
            'reservable_type' => EventAddon::class,
            'quantity' => $item['qty'],
            'unit_price' => $addon->price,
        ]);
    }
}
