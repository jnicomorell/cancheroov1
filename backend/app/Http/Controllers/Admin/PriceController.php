<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Price;
use Illuminate\Http\Request;

class PriceController extends Controller
{
    public function index()
    {
        return Price::all();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'field_id' => 'required|exists:fields,id',
            'amount' => 'required|numeric',
        ]);
        $price = Price::create($data);
        return response()->json($price, 201);
    }

    public function show(Price $price)
    {
        return $price;
    }

    public function update(Request $request, Price $price)
    {
        $data = $request->validate([
            'field_id' => 'sometimes|exists:fields,id',
            'amount' => 'sometimes|numeric',
        ]);
        $price->update($data);
        return $price;
    }

    public function destroy(Price $price)
    {
        $price->delete();
        return response()->noContent();
    }
}
