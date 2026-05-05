<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SepayController extends Controller
{
    /**
     * Webhook nhận từ Sepay khi có giao dịch mới
     */
    public function webhook(Request $request)
    {
        // Verify API token từ Sepay
        $apiToken = env('SEPAY_API_TOKEN');
        $authHeader = $request->header('Authorization');

        if ($apiToken && $authHeader !== 'Apikey ' . $apiToken) {
            Log::warning('Sepay webhook: Invalid token');
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $data = $request->all();
        Log::info('Sepay webhook received', $data);

        // Nội dung chuyển khoản (transferContent / content)
        $content = $data['transferContent'] ?? $data['content'] ?? '';
        $amount  = $data['transferAmount'] ?? $data['amount'] ?? 0;

        // Tìm order ID trong nội dung chuyển khoản
        // User được yêu cầu ghi: "Order 123" hoặc "TECHSHOP 123"
        if (preg_match('/(?:order|techshop|dh|don hang)[^\d]*(\d+)/i', $content, $matches)) {
            $orderId = (int) $matches[1];

            $order = DB::table('orders')->where('id', $orderId)->first();

            if ($order && $order->status === 'pending') {
                // Kiểm tra số tiền (cho phép sai lệch 1000đ)
                if (abs($amount - $order->total) <= 1000) {
                    DB::table('orders')->where('id', $orderId)->update([
                        'status'     => 'confirmed',
                        'updated_at' => now(),
                    ]);

                    Log::info("Order #$orderId confirmed via Sepay. Amount: $amount");

                    return response()->json([
                        'success' => true,
                        'message' => "Order #$orderId confirmed",
                    ]);
                } else {
                    Log::warning("Order #$orderId amount mismatch. Expected: {$order->total}, Got: $amount");
                }
            }
        }

        return response()->json(['success' => true, 'message' => 'Webhook received']);
    }

    /**
     * Kiểm tra trạng thái đơn hàng (AJAX polling từ trang success)
     */
    public function checkOrderStatus(Request $request, $orderId)
    {
        $order = DB::table('orders')
            ->where('id', $orderId)
            ->where('user_id', auth()->id())
            ->first();

        if (!$order) {
            return response()->json(['status' => 'not_found']);
        }

        return response()->json([
            'status'  => $order->status,
            'confirmed' => in_array($order->status, ['confirmed', 'processing', 'shipped', 'delivered']),
        ]);
    }
}
