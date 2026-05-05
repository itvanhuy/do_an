<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Contracts\ChatServiceInterface;

class ChatController extends Controller
{
    public function __construct(private ChatServiceInterface $chatService) {}

    public function send(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }
        $request->validate(['message' => 'required|string|max:1000']);
        $this->chatService->sendMessage(Auth::id(), $request->message, 'user');
        return response()->json(['success' => true]);
    }

    public function messages(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['success' => false], 401);
        }
        $lastId   = (int) $request->query('last_id', 0);
        $messages = $this->chatService->getMessages(Auth::id(), $lastId);
        $this->chatService->markRead(Auth::id(), 'admin');
        return response()->json(['success' => true, 'messages' => $messages]);
    }

    public function adminUsers()
    {
        return response()->json(['success' => true, 'users' => $this->chatService->getUsers()]);
    }

    public function adminMessages(Request $request, $userId)
    {
        $lastId   = (int) $request->query('last_id', 0);
        $messages = $this->chatService->getMessages((int)$userId, $lastId);
        $this->chatService->markRead((int)$userId, 'user');
        return response()->json(['success' => true, 'messages' => $messages]);
    }

    public function adminSend(Request $request, $userId)
    {
        $request->validate(['message' => 'required|string|max:1000']);
        $this->chatService->sendMessage((int)$userId, $request->message, 'admin');
        return response()->json(['success' => true]);
    }

    public function adminUnread()
    {
        return response()->json(['count' => $this->chatService->getUnreadCount()]);
    }
}
