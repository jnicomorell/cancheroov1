<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function index()
    {
        return Schedule::all();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'field_id' => 'required|exists:fields,id',
            'day' => 'required|string',
            'start_time' => 'required',
            'end_time' => 'required',
        ]);
        $schedule = Schedule::create($data);
        return response()->json($schedule, 201);
    }

    public function show(Schedule $schedule)
    {
        return $schedule;
    }

    public function update(Request $request, Schedule $schedule)
    {
        $data = $request->validate([
            'field_id' => 'sometimes|exists:fields,id',
            'day' => 'sometimes|string',
            'start_time' => 'sometimes',
            'end_time' => 'sometimes',
        ]);
        $schedule->update($data);
        return $schedule;
    }

    public function destroy(Schedule $schedule)
    {
        $schedule->delete();
        return response()->noContent();
    }
}
