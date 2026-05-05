<?php

namespace App\Contracts;

interface WishlistServiceInterface
{
    public function getByUser(int $userId): object;
    public function toggle(int $userId, int $productId): array;
    public function isInWishlist(int $userId, int $productId): bool;
}
