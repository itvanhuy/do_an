<?php

namespace App\Contracts;

interface PaymentServiceInterface
{
    public function createPayment(int $orderId, float $amount): mixed;
    public function handleCallback(array $data): array;
}
