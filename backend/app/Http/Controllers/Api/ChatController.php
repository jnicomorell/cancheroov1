<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Chat;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function index()
    {
        return response()->json(Chat::with('users')->get());
    }

    public function show(Chat $chat)
    {
        return response()->json($chat->load('users'));
    }
}
