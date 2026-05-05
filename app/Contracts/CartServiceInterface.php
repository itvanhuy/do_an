<?php

namespace App\Contracts;

interface CartServiceInterface
{
    public function getCount(int $userId): int;
    public function getItems(int $userId): object;
    public function add(int $userId, int $productId, int $quantity): array;
    public function update(int $userId, int $cartId, int $quantity): array;
    public function remove(int $userId, int $cartId): bool;
}
