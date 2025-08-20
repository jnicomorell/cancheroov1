<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Field;
use App\Models\Reservation;
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
