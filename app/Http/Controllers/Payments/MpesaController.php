<?php

namespace App\Http\Controllers\Payments;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;

class MpesaController extends Controller
{
    public function stkCallback(Request $request)
    {
        $payload = $request->all();
        Log::info('MPESA STK Callback received', $payload);

        $stk = $payload['Body']['stkCallback'] ?? null;
        if (!$stk) {
            return response()->json(['status' => 'ignored']);
        }

        $checkoutId = $stk['CheckoutRequestID'] ?? null;
        $merchantId = $stk['MerchantRequestID'] ?? null;
        $resultCode = (string)($stk['ResultCode'] ?? '');
        $resultDesc = (string)($stk['ResultDesc'] ?? '');

        $order = Order::query()
            ->where('mpesa_checkout_request_id', $checkoutId)
            ->orWhere('mpesa_merchant_request_id', $merchantId)
            ->orderByDesc('id')
            ->first();

        if (!$order) {
            Log::warning('MPESA STK Callback: Order not found for IDs', [
                'CheckoutRequestID' => $checkoutId,
                'MerchantRequestID' => $merchantId,
            ]);
            return response()->json(['status' => 'ok']);
        }

        $receipt = null;
        $paidAmount = null;
        $paidPhone = null;
        $paidAt = null;
        if (isset($stk['CallbackMetadata']['Item']) && is_array($stk['CallbackMetadata']['Item'])) {
            foreach ($stk['CallbackMetadata']['Item'] as $item) {
                if (($item['Name'] ?? '') === 'MpesaReceiptNumber') {
                    $receipt = $item['Value'] ?? null;
                } elseif (($item['Name'] ?? '') === 'Amount') {
                    $paidAmount = $item['Value'] ?? null;
                } elseif (($item['Name'] ?? '') === 'PhoneNumber') {
                    $paidPhone = $item['Value'] ?? null;
                } elseif (($item['Name'] ?? '') === 'TransactionDate') {
                    $val = (string)($item['Value'] ?? '');
                    if (strlen($val) === 14) {
                        $paidAt = \Carbon\Carbon::createFromFormat('YmdHis', $val);
                    }
                }
            }
        }

        if ($resultCode === '0') {
            $order->update([
                'status' => 'paid',
                'mpesa_receipt' => $receipt,
                'mpesa_amount' => $paidAmount ?? $order->mpesa_amount,
                'mpesa_phone' => $paidPhone ?? $order->mpesa_phone,
                'mpesa_result_code' => $resultCode,
                'mpesa_result_desc' => $resultDesc,
                'paid_at' => $paidAt ?: now(),
            ]);
            if ($order->promotion_id) {
                \App\Models\Promotion::where('id', $order->promotion_id)->increment('used');
            }
        } else {
            $order->update([
                'status' => 'failed',
                'mpesa_result_code' => $resultCode,
                'mpesa_result_desc' => $resultDesc,
            ]);
        }

        return response()->json(['status' => 'ok']);
    }
}
