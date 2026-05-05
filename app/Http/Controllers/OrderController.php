<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $userId = Auth::id();
        
        $orders = DB::table('orders')
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();

        $orderIds = $orders->pluck('id')->toArray();

        // Lấy tất cả order items 1 lần thay vì N queries
        $allItems = DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->whereIn('order_items.order_id', $orderIds)
            ->select('order_items.*', 'products.name', 'products.image')
            ->get()
            ->groupBy('order_id');

        $processedOrders = [];
        foreach ($orders as $order) {
            $processedOrders[] = [
                'details' => $order,
                'items' => $allItems[$order->id] ?? collect([])
            ];
        }

        return view('orders', ['orders' => $processedOrders]);
    }

    public function show($id)
    {
        if (!Auth::check()) return redirect()->route('login');

        $order = DB::table('orders')
            ->where('id', $id)
            ->where('user_id', Auth::id())
            ->first();

        if (!$order) abort(404);

        $items = DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->where('order_items.order_id', $order->id)
            ->select('order_items.*', 'products.name', 'products.image')
            ->get();

        return view('order-detail', compact('order', 'items'));
    }
}
