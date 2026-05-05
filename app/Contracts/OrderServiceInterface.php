<?php

namespace App\Contracts;

interface OrderServiceInterface
{
    public function getByUser(int $userId): array;
    public function getById(int $orderId, int $userId): ?object;
    public function getItems(int $orderId): object;
}
