<?php

namespace App\Services;

use App\Models\Reservation;

class PaymentService
{
    public function payReservation(Reservation $reservation): Reservation
    {
        // Here we would integrate with MercadoPago or another provider
        // For now we simply mark the reservation as paid
        $reservation->payment_status = 'paid';
        $reservation->save();

        return $reservation;
    }
}
