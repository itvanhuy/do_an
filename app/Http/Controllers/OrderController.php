<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\OrderService;

class OrderController extends Controller
{
    public function __construct(private OrderService $orderService) {}

    public function index()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $orders = $this->orderService->getByUser(Auth::id());

        return view('orders', compact('orders'));
    }

    public function show($id)
    {
        if (!Auth::check()) return redirect()->route('login');

        $order = $this->orderService->getById((int)$id, Auth::id());

        if (!$order) abort(404);

        $items = $this->orderService->getItems((int)$id);

        return view('order-detail', compact('order', 'items'));
    }
}
