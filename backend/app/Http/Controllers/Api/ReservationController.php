<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Field;
use App\Models\Reservation;
use App\Jobs\SendReservationReminder;
use App\Jobs\SendPushNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\PaymentService;

class ReservationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $reservations = Auth::user()->reservations()->with('field.club')->get();
        return response()->json($reservations);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'field_id' => 'required|exists:fields,id',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
        ]);

        $field = Field::findOrFail($data['field_id']);
        $hours = (strtotime($data['end_time']) - strtotime($data['start_time'])) / 3600;
        $price = $field->price_per_hour * $hours;

        $reservation = Reservation::create([
            'field_id' => $field->id,
            'user_id' => Auth::id(),
            'start_time' => $data['start_time'],
            'end_time' => $data['end_time'],
            'price' => $price,
            'status' => 'confirmed',
        ]);

        SendReservationReminder::dispatch($reservation)
            ->delay($reservation->start_time->subHour());

        return response()->json($reservation, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Reservation $reservation)
    {
        if ($reservation->user_id !== Auth::id()) {
            abort(403);
        }

        $reservation->load('field.club');
        return response()->json($reservation);
    }

    public function ics(Reservation $reservation)
    {
        if ($reservation->user_id !== Auth::id()) {
            abort(403);
        }

        $reservation->load('field.club');
        $ics = "BEGIN:VCALENDAR\r\nVERSION:2.0\r\nBEGIN:VEVENT\r\n" .
            'UID:reservation-' . $reservation->id . "@canchero\r\n" .
            'DTSTAMP:' . $reservation->created_at->utc()->format('Ymd\THis\Z') . "\r\n" .
            'DTSTART:' . $reservation->start_time->utc()->format('Ymd\THis\Z') . "\r\n" .
            'DTEND:' . $reservation->end_time->utc()->format('Ymd\THis\Z') . "\r\n" .
            'SUMMARY:Partido en ' . $reservation->field->name . "\r\n" .
            'LOCATION:' . $reservation->field->club->address . "\r\nEND:VEVENT\r\nEND:VCALENDAR";

        return response($ics, 200, [
            'Content-Type' => 'text/calendar; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="reservation-' . $reservation->id . '.ics"',
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Reservation $reservation)
    {
        if ($reservation->user_id !== Auth::id()) {
            abort(403);
        }

        $data = $request->validate([
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
        ]);

        $field = $reservation->field;
        $hours = (strtotime($data['end_time']) - strtotime($data['start_time'])) / 3600;
        $price = $field->price_per_hour * $hours;

        $reservation->start_time = $data['start_time'];
        $reservation->end_time = $data['end_time'];
        $reservation->price = $price;
        $reservation->save();

        SendPushNotification::dispatch(
            $reservation->user,
            'Reserva actualizada',
            'Tu reserva fue modificada'
        );

        return response()->json($reservation);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Reservation $reservation)
    {
        if ($reservation->user_id !== Auth::id()) {
            abort(403);
        }

        $reservation->status = 'cancelled';
        $reservation->save();

        SendPushNotification::dispatch(
            $reservation->user,
            'Reserva cancelada',
            'Tu reserva fue cancelada'
        );

        return response()->json($reservation);
    }

    public function pay(Reservation $reservation, PaymentService $paymentService)
    {
        if ($reservation->user_id !== Auth::id()) {
            abort(403);
        }

        $paymentService->payReservation($reservation);

        return response()->json($reservation);
    }
}
