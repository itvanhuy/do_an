<?php

namespace App\Services;

use App\Contracts\PaymentServiceInterface;
use Illuminate\Support\Facades\DB;

class VNPayService implements PaymentServiceInterface
{
    private string $tmnCode;
    private string $hashSecret;
    private string $url;

    public function __construct()
    {
        $this->tmnCode    = env('VNPAY_TMN_CODE', '');
        $this->hashSecret = env('VNPAY_HASH_SECRET', '');
        $this->url        = 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html';
    }

    public function createPayment(int $orderId, float $amount): mixed
    {
        $returnUrl  = url('/checkout/vnpay_return');
        $txnRef     = $orderId . '_' . time();
        $orderInfo  = 'TechShop - Order #' . $orderId;

        $inputData = [
            'vnp_Version'    => '2.1.0',
            'vnp_TmnCode'    => $this->tmnCode,
            'vnp_Amount'     => (int)($amount * 100),
            'vnp_Command'    => 'pay',
            'vnp_CreateDate' => date('YmdHis'),
            'vnp_CurrCode'   => 'VND',
            'vnp_IpAddr'     => request()->ip(),
            'vnp_Locale'     => 'vn',
            'vnp_OrderInfo'  => $orderInfo,
            'vnp_OrderType'  => 'billpayment',
            'vnp_ReturnUrl'  => $returnUrl,
            'vnp_TxnRef'     => $txnRef,
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

        $secureHash = hash_hmac('sha512', $hashdata, $this->hashSecret);
        $payUrl     = $this->url . '?' . $query . 'vnp_SecureHash=' . $secureHash;

        DB::table('orders')->where('id', $orderId)->update(['payment_ref' => $txnRef]);

        return redirect($payUrl);
    }

    public function handleCallback(array $data): array
    {
        $secureHash = $data['vnp_SecureHash'] ?? '';
        $inputData  = [];

        foreach ($data as $key => $value) {
            if (str_starts_with($key, 'vnp_')) {
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

        $computed = hash_hmac('sha512', $hashData, $this->hashSecret);
        $orderId  = explode('_', $data['vnp_TxnRef'] ?? '')[0];
        $success  = $computed === $secureHash && ($data['vnp_ResponseCode'] ?? '') === '00';

        return ['success' => $success, 'order_id' => $orderId];
    }
}
