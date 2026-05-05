<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    // Khách gửi tin nhắn
    public function send(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $request->validate(['message' => 'required|string|max:1000']);

        DB::table('chat_messages')->insert([
            'user_id'    => Auth::id(),
            'message'    => $request->message,
            'sender'     => 'user',
            'is_read'    => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json(['success' => true]);
    }

    // Lấy tin nhắn của user hiện tại (polling) - chỉ lấy tin mới hơn lastId
    public function messages(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['success' => false], 401);
        }

        $lastId = (int) $request->query('last_id', 0);

        $query = DB::table('chat_messages')->where('user_id', Auth::id());

        if ($lastId > 0) {
            $query->where('id', '>', $lastId);
        }

        $messages = $query->orderBy('created_at', 'asc')
            ->get(['id', 'message', 'sender', 'created_at']);

        // Đánh dấu tin nhắn admin đã đọc
        DB::table('chat_messages')
            ->where('user_id', Auth::id())
            ->where('sender', 'admin')
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json(['success' => true, 'messages' => $messages]);
    }

    // ===== ADMIN =====

    // Danh sách user đang chat
    public function adminUsers()
    {
        $users = DB::table('chat_messages')
            ->join('users', 'chat_messages.user_id', '=', 'users.id')
            ->select(
                'users.id',
                'users.username',
                'users.full_name',
                DB::raw('MAX(chat_messages.created_at) as last_message_at'),
                DB::raw('SUM(chat_messages.sender = "user" AND chat_messages.is_read = 0) as unread_count'),
                DB::raw('(SELECT message FROM chat_messages cm2 WHERE cm2.user_id = users.id ORDER BY cm2.created_at DESC LIMIT 1) as last_message')
            )
            ->groupBy('users.id', 'users.username', 'users.full_name')
            ->orderBy('last_message_at', 'desc')
            ->get();

        return response()->json(['success' => true, 'users' => $users]);
    }

    // Lấy tin nhắn của 1 user (admin xem) - chỉ lấy tin mới hơn lastId
    public function adminMessages(Request $request, $userId)
    {
        $lastId = (int) $request->query('last_id', 0);

        $query = DB::table('chat_messages')->where('user_id', $userId);

        if ($lastId > 0) {
            $query->where('id', '>', $lastId);
        }

        $messages = $query->orderBy('created_at', 'asc')
            ->get(['id', 'message', 'sender', 'created_at']);

        // Đánh dấu đã đọc
        DB::table('chat_messages')
            ->where('user_id', $userId)
            ->where('sender', 'user')
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json(['success' => true, 'messages' => $messages]);
    }

    // Admin gửi tin nhắn cho user
    public function adminSend(Request $request, $userId)
    {
        $request->validate(['message' => 'required|string|max:1000']);

        DB::table('chat_messages')->insert([
            'user_id'    => $userId,
            'message'    => $request->message,
            'sender'     => 'admin',
            'is_read'    => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json(['success' => true]);
    }

    // Tổng số tin nhắn chưa đọc (admin badge)
    public function adminUnread()
    {
        $count = DB::table('chat_messages')
            ->where('sender', 'user')
            ->where('is_read', false)
            ->count();

        return response()->json(['count' => $count]);
    }
}
