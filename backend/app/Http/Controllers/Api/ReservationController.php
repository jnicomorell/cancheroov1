<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Field;
use App\Models\Reservation;
use App\Models\ReservationItem;

use App\Jobs\SendReservationReminder;
use App\Jobs\NotifyWaitlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\PaymentService;
use App\Models\LoyaltyPoint;

class ReservationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $reservations = Auth::user()->reservations()->with('field.club', 'items.rentalItem')->get();
        return response()->json($reservations);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, WeatherService $weatherService)
    {
        $data = $request->validate([
            'field_id' => 'required|exists:fields,id',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'items' => 'array',
            'items.*.rental_item_id' => 'exists:rental_items,id',
            'items.*.quantity' => 'integer|min:1',
        ]);

        $field = Field::with('rentalItems')->findOrFail($data['field_id']);
        $hours = (strtotime($data['end_time']) - strtotime($data['start_time'])) / 3600;
        $basePrice = $field->price_per_hour * $hours;
        $extrasPrice = 0;

        if (!empty($data['items'])) {
            foreach ($data['items'] as $item) {
                $rental = $field->rentalItems->firstWhere('id', $item['rental_item_id']);
                if ($rental) {
                    $extrasPrice += $rental->price * ($item['quantity'] ?? 1);
                }
            }
        }

        $alert = null;
        if ($field->club && $field->club->latitude && $field->club->longitude) {
            $alert = $weatherService->getAlert(
                $field->club->latitude,
                $field->club->longitude,
                $data['start_time'],
                $data['end_time']
            );
        }

        $reservation = Reservation::create([
            'field_id' => $field->id,
            'user_id' => Auth::id(),
            'start_time' => $data['start_time'],
            'end_time' => $data['end_time'],
            'price' => $basePrice + $extrasPrice,
            'status' => 'confirmed',
            'weather_alert' => $alert,
        ]);

        if (!empty($data['items'])) {
            foreach ($data['items'] as $item) {
                $rental = $field->rentalItems->firstWhere('id', $item['rental_item_id']);
                if ($rental) {
                    ReservationItem::create([
                        'reservation_id' => $reservation->id,
                        'rental_item_id' => $rental->id,
                        'quantity' => $item['quantity'] ?? 1,
                        'price' => $rental->price,
                    ]);
                }
            }
        }

        $reservation->load('items.rentalItem');

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

        $reservation->load('field.club', 'items.rentalItem');
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
    public function update(Request $request, Reservation $reservation, WeatherService $weatherService)
    {
        if ($reservation->user_id !== Auth::id()) {
            abort(403);
        }

        $data = $request->validate([
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
        ]);

        $field = $reservation->field()->with('club')->first();
        $hours = (strtotime($data['end_time']) - strtotime($data['start_time'])) / 3600;
        $basePrice = $field->price_per_hour * $hours;
        $extras = $reservation->items->sum(fn($item) => $item->price * $item->quantity);

        $alert = null;
        if ($field->club && $field->club->latitude && $field->club->longitude) {
            $alert = $weatherService->getAlert(
                $field->club->latitude,
                $field->club->longitude,
                $data['start_time'],
                $data['end_time']
            );
        }

        $reservation->start_time = $data['start_time'];
        $reservation->end_time = $data['end_time'];
        $reservation->price = $basePrice + $extras;
        $reservation->save();

        $reservation->load('items.rentalItem');

        return response()->json($reservation);
    }

    public function addParticipant(Request $request, Reservation $reservation)
    {
        if ($reservation->user_id !== Auth::id()) {
            abort(403);
        }

        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:0',
        ]);

        $reservation->participants()->attach($data['user_id'], ['amount' => $data['amount']]);

        SharedCost::create([
            'reservation_id' => $reservation->id,
            'user_id' => $data['user_id'],
            'amount' => $data['amount'],
        ]);

        return response()->json(['message' => 'Participant added'], 201);
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

        NotifyWaitlist::dispatch($reservation);

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

    public function invite(Request $request, Reservation $reservation)
    {
        if ($reservation->user_id !== Auth::id()) {
            abort(403);
        }

        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'amount' => 'nullable|numeric',
        ]);

        $reservation->participants()->attach($data['user_id'], ['amount' => $data['amount'] ?? 0]);

        return response()->json(['message' => 'Invitation sent']);
    }

    public function confirm(Reservation $reservation)
    {
        if (! $reservation->participants()->wherePivot('user_id', Auth::id())->exists()) {
            abort(403);
        }

        return response()->json(['message' => 'Participation confirmed']);
    }
}
