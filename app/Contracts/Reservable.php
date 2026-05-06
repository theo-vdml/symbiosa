<?php

namespace App\Contracts;

/**
 * @property-read int|null $capacity La capacité totale (définie en base)
 * @property-read int $reserved_stock La quantité réservée (calculée via le trait)
 * @property-read int|null $available_stock Le stock restant (calculé via le trait)
 */
interface Reservable {}
