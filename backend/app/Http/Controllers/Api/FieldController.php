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
        $fields = Field::query()
            ->with('club')
            ->withAvg('reviews as average_rating', 'rating');

        $this->applyFilters($fields, $request);

        return response()->json($fields->paginate());
    }

    public function map(Request $request)
    {
        $fields = Field::query()->with('club');
        $this->applyFilters($fields, $request);

        $data = $fields->get()->map(function ($field) {
            return [
                'id' => $field->id,
                'name' => $field->name,
                'latitude' => $field->latitude ?? optional($field->club)->latitude,
                'longitude' => $field->longitude ?? optional($field->club)->longitude,
            ];
        });

        return response()->json($data);
    }

    private function applyFilters($fields, Request $request): void
    {
        if ($request->filled('sport')) {
            $fields->where('sport', $request->sport);
        }

        if ($request->filled('city')) {
            $fields->whereHas('club', function ($q) use ($request) {
                $q->where('city', $request->city);
            });
        }

        if ($request->filled('surface')) {
            $fields->where('surface', $request->surface);
        }

        if ($request->filled('is_indoor')) {
            $fields->where('is_indoor', filter_var($request->is_indoor, FILTER_VALIDATE_BOOLEAN));
        }

        if ($request->filled('min_price')) {
            $fields->where('price_per_hour', '>=', $request->min_price);
        }

        if ($request->filled('max_price')) {
            $fields->where('price_per_hour', '<=', $request->max_price);
        }

        if ($request->filled('features')) {
            $features = is_array($request->features)
                ? $request->features
                : explode(',', $request->features);
            foreach ($features as $feature) {
                $fields->whereJsonContains('features', $feature);
            }
        }

        if ($request->filled(['latitude', 'longitude', 'radius'])) {
            $lat = $request->latitude;
            $lng = $request->longitude;
            $radius = $request->radius; // kilometers
            $haversine = "(6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude))))";

            $fields->select('*')
                ->selectRaw("$haversine AS distance", [$lat, $lng, $lat])
                ->having('distance', '<=', $radius)
                ->orderBy('distance');
        }

        if ($request->filled(['start_time', 'end_time'])) {
            $fields->whereDoesntHave('reservations', function ($q) use ($request) {
                $q->where(function ($query) use ($request) {
                    $query->where('start_time', '<', $request->end_time)
                        ->where('end_time', '>', $request->start_time);
                });
            });
        }
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
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
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
        $field->load('club')
            ->loadAvg('reviews as average_rating', 'rating');
        return response()->json($field);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Field $field)
    {
        $this->authorize('update', $field);

        $data = $request->validate([
            'club_id' => 'sometimes|exists:clubs,id',
            'name' => 'sometimes|string',
            'sport' => 'sometimes|in:futbol,padel',
            'surface' => 'nullable|string',
            'is_indoor' => 'boolean',
            'price_per_hour' => 'sometimes|numeric',
            'features' => 'array',
        ]);

        $field->update($data);

        return response()->json($field);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Field $field)
    {
        $this->authorize('delete', $field);

        $field->delete();

        return response()->json(null, 204);
    }
}
