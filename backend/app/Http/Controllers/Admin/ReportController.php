<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Reservation;

class ReportController extends Controller
{
    public function reservations()
    {
        return ['count' => Reservation::count()];
    }

    public function income()
    {
        return ['total' => Payment::sum('amount')];
    }
}
