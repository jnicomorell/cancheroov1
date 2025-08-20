<?php

namespace App\Services;

use App\Models\Reservation;
use Illuminate\Support\Facades\Http;

class PaymentService
{
    public function payReservation(Reservation $reservation): Reservation
    {
        $response = Http::withToken(config('services.mercadopago.token'))
            ->post('https://api.mercadopago.com/v1/payments', [
                'transaction_amount' => $reservation->price,
                'description' => 'Reservation ' . $reservation->id,
                'payment_method_id' => 'account_money',
                'payer' => [
                    'email' => $reservation->user->email,
                ],
            ]);

        if ($response->successful() && $response->json('status') === 'approved') {
            $reservation->payment_status = 'paid';
        } else {
            $reservation->payment_status = 'failed';
        }

        $reservation->save();

        return $reservation;
    }
}
