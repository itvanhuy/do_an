<?php

namespace App\Services;

use App\Contracts\ChatServiceInterface;
use Illuminate\Support\Facades\DB;

class ChatService implements ChatServiceInterface
{
    public function sendMessage(int $userId, string $message, string $sender): bool
    {
        return DB::table('chat_messages')->insert([
            'user_id'    => $userId,
            'message'    => $message,
            'sender'     => $sender,
            'is_read'    => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function getMessages(int $userId, int $lastId = 0): object
    {
        $query = DB::table('chat_messages')->where('user_id', $userId);

        if ($lastId > 0) {
            $query->where('id', '>', $lastId);
        }

        return $query->orderBy('created_at', 'asc')
            ->get(['id', 'message', 'sender', 'created_at']);
    }

    public function markRead(int $userId, string $sender): void
    {
        DB::table('chat_messages')
            ->where('user_id', $userId)
            ->where('sender', $sender)
            ->where('is_read', false)
            ->update(['is_read' => true]);
    }

    public function getUsers(): object
    {
        return DB::table('chat_messages')
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
    }

    public function getUnreadCount(): int
    {
        return DB::table('chat_messages')
            ->where('sender', 'user')
            ->where('is_read', false)
            ->count();
    }
}
