<?php

namespace App\Traits;

use App\Contracts\Reservable;
use App\Models\Reservation;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

/**
 * Trait HasStock
 *
 * Provides dynamic stock calculation for models that can be reserved or sold.
 * Linked to the Reservation model and filtered by the Checkout 'holdingStock' scope.
 *
 * @mixin Model
 * @mixin Reservable
 */
trait HasStock
{
    /**
     * Get the total number of units currently reserved or completed.
     *
     * This includes all reservations linked to this model that are part of
     * a checkout session that is either active, processing, or paid.
     *
     * @return Attribute<int, never>
     */
    protected function reservedStock(): Attribute
    {
        return Attribute::get(function (): mixed {
            return Reservation::where('reservable_type', get_class($this))
                ->where('reservable_id', $this->getKey())
                ->whereHas('checkout', fn($q) => $q->holdingStock())
                ->sum('quantity');
        });
    }

    /**
     * Get the remaining available stock.
     *
     * Returns null if the model has no capacity limit (infinite stock).
     * Otherwise, returns the capacity minus the reserved stock, with a minimum of 0.
     *
     * @return Attribute<int|null, never>
     */
    protected function availableStock(): Attribute
    {
        return Attribute::get(function (): int|null {
            if (!$this->capacity) return null;
            return max(0, $this->capacity - $this->reserved_stock);
        });
    }

    /**
     * Determine if the model has enough available stock for a specific quantity.
     *
     * @param int $requestedQty The quantity to check against availability.
     * @return bool True if stock is infinite or sufficient, false otherwise.
     */
    public function hasStockFor(int $requestedQty): bool
    {
        if (!$this->capacity) return true;
        return $requestedQty <= ($this->capacity - $this->reserved_stock);
    }
}
