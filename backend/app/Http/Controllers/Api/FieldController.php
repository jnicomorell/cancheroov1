<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Field;
use Illuminate\Http\Request;

class FieldController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $fields = Field::query()->with('club');

        if ($request->filled('sport')) {
            $fields->where('sport', $request->sport);
        }

        if ($request->filled('city')) {
            $fields->whereHas('club', function ($q) use ($request) {
                $q->where('city', $request->city);
            });
        }

        if ($request->filled(['start_time', 'end_time'])) {
            $fields->whereDoesntHave('reservations', function ($q) use ($request) {
                $q->where(function ($query) use ($request) {
                    $query->where('start_time', '<', $request->end_time)
                        ->where('end_time', '>', $request->start_time);
                });
            });
        }

        return response()->json($fields->paginate());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Field::class);

        $data = $request->validate([
            'club_id' => 'required|exists:clubs,id',
            'name' => 'required|string',
            'sport' => 'required|in:futbol,padel',
            'surface' => 'nullable|string',
            'is_indoor' => 'boolean',
            'price_per_hour' => 'required|numeric',
            'features' => 'array',
        ]);

        $field = Field::create($data);

        return response()->json($field, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Field $field)
    {
        $field->load('club');
        return response()->json($field);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
