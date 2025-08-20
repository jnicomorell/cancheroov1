<?php

namespace App\Http\Controllers\Api;

use App\Events\MessageSent;
use App\Http\Controllers\Controller;
use App\Models\Chat;
use App\Models\Message;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function index(Chat $chat)
    {
        return response()->json($chat->messages()->with('user')->get());
    }

    public function store(Request $request, Chat $chat)
    {
        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'content' => 'required|string',
        ]);

        $message = $chat->messages()->create($data);

        MessageSent::dispatch($message);

        return response()->json($message, 201);
    }
}
