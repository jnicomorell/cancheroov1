<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;

class UserController extends Controller
{
    public function ranking()
    {
        $users = User::orderByDesc('points')->get(['id', 'name', 'points']);

        return response()->json($users);
    }
}
