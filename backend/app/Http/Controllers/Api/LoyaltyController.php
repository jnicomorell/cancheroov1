<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LoyaltyPoint;
use App\Models\Promotion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoyaltyController extends Controller
{
    public function balance()
    {
        $balance = Auth::user()->loyaltyPoints()->sum('points');
        return response()->json(['balance' => $balance]);
    }

    public function redeem(Request $request)
    {
        $data = $request->validate([
            'promotion_id' => 'required|exists:promotions,id',
        ]);

        $promotion = Promotion::findOrFail($data['promotion_id']);
        if (!$promotion->is_active) {
            return response()->json(['message' => 'Promoción no disponible'], 422);
        }

        $balance = Auth::user()->loyaltyPoints()->sum('points');
        if ($balance < $promotion->points_required) {
            return response()->json(['message' => 'Saldo insuficiente'], 422);
        }

        LoyaltyPoint::create([
            'user_id' => Auth::id(),
            'points' => -$promotion->points_required,
            'description' => 'Redeemed: ' . $promotion->name,
        ]);

        return response()->json(['balance' => $balance - $promotion->points_required]);
    }
}
