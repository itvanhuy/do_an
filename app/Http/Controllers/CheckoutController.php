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
            'payment_method' => 'required|in:cod,momo'
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

            if ($request->payment_method === 'momo') {
                return $this->createMomoPayment($orderId, $total);
            }

            return redirect('checkout/success?order_id=' . $orderId);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['checkout' => 'Đặt hàng thất bại: ' . $e->getMessage()]);
        }
    }

    private function createMomoPayment($orderId, $amount)
    {
        $endpoint   = 'https://test-payment.momo.vn/v2/gateway/api/create';
        $partnerCode = env('MOMO_PARTNER_CODE', 'MOMO');
        $accessKey   = env('MOMO_ACCESS_KEY', 'F8BBA842ECF85');
        $secretKey   = env('MOMO_SECRET_KEY', 'K951B6PE1waDMi640xX08PD3vg6EkVlz');
        $returnUrl   = url('/checkout/momo_return');
        $notifyUrl   = url('/checkout/momo_notify');
        $requestId   = $partnerCode . time();
        $requestType = 'payWithMethod';
        $orderInfo   = 'TechShop - Order #' . $orderId;
        $extraData   = base64_encode(json_encode(['order_id' => $orderId]));
        $autoCapture = true;
        $lang        = 'vi';

        $rawHash = "accessKey=$accessKey&amount=$amount&extraData=$extraData&ipnUrl=$notifyUrl&orderId=$requestId&orderInfo=$orderInfo&partnerCode=$partnerCode&redirectUrl=$returnUrl&requestId=$requestId&requestType=$requestType";
        $signature = hash_hmac('sha256', $rawHash, $secretKey);

        $data = [
            'partnerCode' => $partnerCode,
            'accessKey'   => $accessKey,
            'requestId'   => $requestId,
            'amount'      => (int) $amount,
            'orderId'     => $requestId,
            'orderInfo'   => $orderInfo,
            'redirectUrl' => $returnUrl,
            'ipnUrl'      => $notifyUrl,
            'extraData'   => $extraData,
            'requestType' => $requestType,
            'signature'   => $signature,
            'lang'        => $lang,
            'autoCapture' => $autoCapture,
        ];

        $ch = curl_init($endpoint);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        $result = curl_exec($ch);
        curl_close($ch);

        $response = json_decode($result, true);

        if (isset($response['payUrl'])) {
            // Lưu requestId để đối chiếu khi callback
            DB::table('orders')->where('id', $orderId)->update(['payment_ref' => $requestId]);
            return redirect($response['payUrl']);
        }

        return back()->withErrors(['checkout' => 'Không thể kết nối MoMo. Vui lòng thử lại.']);
    }

    public function momoReturn(Request $request)
    {
        $resultCode = $request->resultCode;
        $extraData  = json_decode(base64_decode($request->extraData), true);
        $orderId    = $extraData['order_id'] ?? null;

        if ($resultCode == 0) {
            DB::table('orders')->where('id', $orderId)->update(['status' => 'confirmed']);
            return redirect('checkout/success?order_id=' . $orderId);
        }

        DB::table('orders')->where('id', $orderId)->update(['status' => 'cancelled']);
        return redirect('checkout/success?order_id=' . $orderId . '&momo_error=1');
    }

    public function momoNotify(Request $request)
    {
        // IPN callback từ MoMo server
        $secretKey  = env('MOMO_SECRET_KEY', 'K951B6PE1waDMi640xX08PD3vg6EkVlz');
        $rawHash    = "accessKey=" . env('MOMO_ACCESS_KEY', 'F8BBA842ECF85') .
                      "&amount={$request->amount}&extraData={$request->extraData}" .
                      "&message={$request->message}&orderId={$request->orderId}" .
                      "&orderInfo={$request->orderInfo}&orderType={$request->orderType}" .
                      "&partnerCode={$request->partnerCode}&payType={$request->payType}" .
                      "&requestId={$request->requestId}&responseTime={$request->responseTime}" .
                      "&resultCode={$request->resultCode}&transId={$request->transId}";

        $signature = hash_hmac('sha256', $rawHash, $secretKey);

        if ($signature === $request->signature && $request->resultCode == 0) {
            $extraData = json_decode(base64_decode($request->extraData), true);
            $orderId   = $extraData['order_id'] ?? null;
            if ($orderId) {
                DB::table('orders')->where('id', $orderId)->update(['status' => 'confirmed']);
            }
        }

        return response()->json(['status' => 'ok']);
    }
    
    public function success(Request $request)
    {
        return view('checkout_success', ['orderId' => $request->order_id, 'message' => 'Đặt hàng COD thành công!']);
    }
}
