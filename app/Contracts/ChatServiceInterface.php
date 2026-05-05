<?php

namespace App\Contracts;

interface ChatServiceInterface
{
    public function sendMessage(int $userId, string $message, string $sender): bool;
    public function getMessages(int $userId, int $lastId = 0): object;
    public function getUsers(): object;
    public function markRead(int $userId, string $sender): void;
    public function getUnreadCount(): int;
}
