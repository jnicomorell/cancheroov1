<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    public function redirect(string $provider)
    {
        return Socialite::driver($provider)->stateless()->redirect();
    }

    public function callback(string $provider)
    {
        $socialUser = Socialite::driver($provider)->stateless()->user();

        $user = User::updateOrCreate(
            [
                'provider' => $provider,
                'provider_id' => $socialUser->getId(),
            ],
            [
                'name' => $socialUser->getName() ?: $socialUser->getNickname() ?: '',
                'email' => $socialUser->getEmail() ?: Str::slug($provider.'_'.$socialUser->getId()).'@example.com',
                'provider_token' => $socialUser->token,
                'provider_refresh_token' => $socialUser->refreshToken,
                'provider_claims' => $socialUser->user,
            ]
        );

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json(['token' => $token]);
    }
}
