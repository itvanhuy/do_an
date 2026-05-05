<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Models\Product;
use App\Mail\OrderPlaced;

class CheckoutController extends Controller
{
    public function index(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->withErrors(['email' => 'Vui lòng đăng nhập để thanh toán']);
        }

        $userId = Auth::id();
        $user = Auth::user();
        $cartItems = [];
        $subtotal = 0;
        $shippingFee = 30000;
        $discountAmount = 0;
        $isBuyNow = false;

        // Xử lý nhánh "Mua ngay"
        if ($request->has('product_id') && $request->has('quantity')) {
            $isBuyNow = true;
            $product = Product::find($request->product_id);
            if ($product && $product->is_active) {
                $finalPrice = $product->price;
                if ($product->discount > 0) {
                    $finalPrice = $product->price * (1 - $product->discount / 100);
                }
                $itemTotal = $finalPrice * $request->quantity;
                $subtotal += $itemTotal;
                
                $cartItems[] = (object) [
                    'product_id' => $product->id,
                    'name' => $product->name,
                    'current_price' => $finalPrice,
                    'image' => $product->image,
                    'quantity' => $request->quantity,
                    'total' => $itemTotal
                ];
            }
        } else {
            // Xử lý nhánh Giỏ hàng
            $items = DB::table('cart')
                ->join('products', 'cart.product_id', '=', 'products.id')
                ->where('cart.user_id', $userId)
                ->select('cart.*', 'products.name', 'products.price', 'products.image', 'products.discount')
                ->get();
                
            foreach ($items as $item) {
                $finalPrice = $item->price;
                if ($item->discount > 0) {
                    $finalPrice = $item->price * (1 - $item->discount/100);
                }
                $itemTotal = $finalPrice * $item->quantity;
                $subtotal += $itemTotal;
                
                $cartItems[] = (object) [
                    'product_id' => $item->product_id,
                    'name' => $item->name,
                    'current_price' => $finalPrice,
                    'image' => $item->image,
                    'quantity' => $item->quantity,
                    'total' => $itemTotal
                ];
            }
        }

        if (empty($cartItems)) {
            return redirect('cart');
        }

        $total = $subtotal + $shippingFee - $discountAmount;

        return view('checkout', compact('user', 'cartItems', 'subtotal', 'shippingFee', 'total', 'isBuyNow'));
    }

    public function process(Request $request)
    {
        if (!Auth::check()) return redirect()->route('login');

        // Verify inputs
        $request->validate([
            'name' => 'required',
            'phone' => 'required',
            'shipping_address' => 'required',
            'payment_method' => 'required|in:cod,vnpay'
        ]);

        $userId = Auth::id();
        $isBuyNow = $request->input('is_buy_now') === '1';
        $cartItems = [];
        $shippingFee = 30000;
        $subtotal = 0;

        if ($isBuyNow) {
            $product = Product::find($request->buy_now_product_id);
            $qty = $request->buy_now_quantity;
            if ($product) {
                $finalPrice = $product->price;
                if ($product->discount > 0) {
                    $finalPrice = $product->price * (1 - $product->discount / 100);
                }
                $subtotal += $finalPrice * $qty;
                $cartItems[] = (object) [
                    'product_id' => $product->id,
                    'price' => $finalPrice,
                    'quantity' => $qty
                ];
            }
        } else {
            $items = DB::table('cart')
                ->join('products', 'cart.product_id', '=', 'products.id')
                ->where('cart.user_id', $userId)
                ->select('cart.*', 'products.price', 'products.discount')
                ->get();
            foreach ($items as $item) {
                $finalPrice = $item->price;
                if ($item->discount > 0) $finalPrice = $item->price * (1 - $item->discount/100);
                $subtotal += $finalPrice * $item->quantity;
                $cartItems[] = (object) [
                    'product_id' => $item->product_id,
                    'price' => $finalPrice,
                    'quantity' => $item->quantity
                ];
            }
        }

        if (empty($cartItems)) return back()->withErrors(['cart' => 'Giỏ hàng trống!']);

        // Apply coupon if provided
        $discountAmount = 0;
        $couponCode = $request->coupon_code;
        if ($couponCode) {
            $coupon = DB::table('coupons')
                ->where('code', $couponCode)
                ->where('is_active', 1)
                ->where(function($q) { $q->whereNull('expires_at')->orWhere('expires_at', '>', now()); })
                ->where(function($q) { $q->whereNull('max_uses')->orWhereRaw('used_count < max_uses'); })
                ->where('min_order', '<=', $subtotal)
                ->first();
            
            if ($coupon) {
                if ($coupon->discount_type === 'percent') {
                    $discountAmount = $subtotal * ($coupon->discount_value / 100);
                } else {
                    $discountAmount = $coupon->discount_value;
                }
            }
        }

        $total = $subtotal + $shippingFee - $discountAmount;
        $fullAddress = $request->name . ' | ' . $request->phone . ' | ' . $request->shipping_address;

        DB::beginTransaction();
        try {
            $orderId = DB::table('orders')->insertGetId([
                'user_id' => $userId,
                'total' => $total,
                'shipping_address' => $fullAddress,
                'payment_method' => $request->payment_method,
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now()
            ]);

            foreach ($cartItems as $item) {
                DB::table('order_items')->insert([
                    'order_id' => $orderId,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
                DB::table('products')->where('id', $item->product_id)->decrement('stock_quantity', $item->quantity);
            }

            if (!$isBuyNow) {
                DB::table('cart')->where('user_id', $userId)->delete();
            }

            // Update coupon used_count if a coupon was applied
            if ($request->coupon_code) {
                DB::table('coupons')->where('code', $request->coupon_code)->increment('used_count');
            }

            DB::commit();

            // Send order confirmation email
            try {
                $order = DB::table('orders')->where('id', $orderId)->first();
                $orderItems = DB::table('order_items')
                    ->join('products', 'order_items.product_id', '=', 'products.id')
                    ->where('order_items.order_id', $orderId)
                    ->select('order_items.*', 'products.name')
                    ->get();
                $customer = Auth::user();
                Mail::to($customer->email)->send(new OrderPlaced($order, $orderItems, $customer));
            } catch (\Exception $mailEx) {
                // Don't fail the order if email fails
                \Log::warning('Order email failed: ' . $mailEx->getMessage());
            }

            if ($request->payment_method === 'vnpay') {
                return $this->createVnpayPayment($orderId, $total);
            }

            return redirect('checkout/success?order_id=' . $orderId);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['checkout' => 'Đặt hàng thất bại: ' . $e->getMessage()]);
        }
    }

    private function createVnpayPayment($orderId, $amount)
    {
        $vnp_TmnCode    = env('VNPAY_TMN_CODE', 'CGXZLS0Z');
        $vnp_HashSecret = env('VNPAY_HASH_SECRET', 'XNBCJFAKRN2');
        $vnp_Url        = 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html';
        $vnp_Returnurl  = url('/checkout/vnpay_return');
        $vnp_TxnRef     = $orderId . '_' . time();
        $vnp_OrderInfo  = 'TechShop - Order #' . $orderId;
        $vnp_Amount     = $amount * 100;
        $vnp_IpAddr     = request()->ip();

        $inputData = [
            'vnp_Version'    => '2.1.0',
            'vnp_TmnCode'    => $vnp_TmnCode,
            'vnp_Amount'     => $vnp_Amount,
            'vnp_Command'    => 'pay',
            'vnp_CreateDate' => date('YmdHis'),
            'vnp_CurrCode'   => 'VND',
            'vnp_IpAddr'     => $vnp_IpAddr,
            'vnp_Locale'     => 'vn',
            'vnp_OrderInfo'  => $vnp_OrderInfo,
            'vnp_OrderType'  => 'billpayment',
            'vnp_ReturnUrl'  => $vnp_Returnurl,
            'vnp_TxnRef'     => $vnp_TxnRef,
        ];

        ksort($inputData);
        $query    = '';
        $hashdata = '';
        $i = 0;
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . '=' . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . '=' . urlencode($value);
                $i = 1;
            }
            $query .= urlencode($key) . '=' . urlencode($value) . '&';
        }

        $secureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
        $payUrl     = $vnp_Url . '?' . $query . 'vnp_SecureHash=' . $secureHash;

        DB::table('orders')->where('id', $orderId)->update(['payment_ref' => $vnp_TxnRef]);

        return redirect($payUrl);
    }

    public function vnpayReturn(Request $request)
    {
        $vnp_HashSecret = env('VNPAY_HASH_SECRET', 'XNBCJFAKRN2');
        $vnp_SecureHash = $request->vnp_SecureHash;

        $inputData = [];
        foreach ($request->all() as $key => $value) {
            if (substr($key, 0, 4) === 'vnp_') {
                $inputData[$key] = $value;
            }
        }
        unset($inputData['vnp_SecureHash']);
        ksort($inputData);

        $hashData = '';
        $i = 0;
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashData .= '&' . urlencode($key) . '=' . urlencode($value);
            } else {
                $hashData .= urlencode($key) . '=' . urlencode($value);
                $i = 1;
            }
        }

        $secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);
        $orderId    = explode('_', $request->vnp_TxnRef)[0];

        if ($secureHash === $vnp_SecureHash && $request->vnp_ResponseCode === '00') {
            DB::table('orders')->where('id', $orderId)->update(['status' => 'confirmed']);
            return redirect('checkout/success?order_id=' . $orderId);
        }

        DB::table('orders')->where('id', $orderId)->update(['status' => 'cancelled']);
        return redirect('checkout/success?order_id=' . $orderId . '&vnpay_error=1');
    }
    
    public function success(Request $request)
    {
        return view('checkout_success', ['orderId' => $request->order_id]);
    }
}
