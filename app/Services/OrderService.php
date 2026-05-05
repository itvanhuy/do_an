<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class OrderService
{
    public function getByUser(int $userId): array
    {
        $orders = DB::table('orders')
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();

        $orderIds = $orders->pluck('id')->toArray();

        $allItems = DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->whereIn('order_items.order_id', $orderIds)
            ->select('order_items.*', 'products.name', 'products.image')
            ->get()
            ->groupBy('order_id');

        return $orders->map(function ($order) use ($allItems) {
            return [
                'details' => $order,
                'items'   => $allItems[$order->id] ?? collect([]),
            ];
        })->toArray();
    }

    public function getById(int $orderId, int $userId): ?object
    {
        return DB::table('orders')
            ->where('id', $orderId)
            ->where('user_id', $userId)
            ->first();
    }

    public function getItems(int $orderId): object
    {
        return DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->where('order_items.order_id', $orderId)
            ->select('order_items.*', 'products.name', 'products.image')
            ->get();
    }
}
