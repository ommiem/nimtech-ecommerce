<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\SafaricomDarajaHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MpesaToolsController extends Controller
{
    public function index()
    {
        $balance = session('mpesa_balance');
        $b2cResult = session('mpesa_b2c_result');

        return view('admin.mpesa.tools', compact('balance', 'b2cResult'));
    }

    public function balance(Request $request)
    {
        $result = SafaricomDarajaHelper::getPaybillBalance();

        return redirect()
            ->route('admin.mpesa.tools')
            ->with('mpesa_balance', $result);
    }

    public function b2c(Request $request)
    {
        $data = $request->validate([
            'phone' => ['required', 'string', 'max:20'],
            'amount' => ['required', 'numeric', 'min:1'],
            'reference' => ['required', 'string', 'max:100'],
        ]);

        $phone = preg_replace('/\s+/', '', (string) $data['phone']);
        if (str_starts_with($phone, '0')) {
            $phone = '254' . substr($phone, 1);
        }

        $result = SafaricomDarajaHelper::initiateB2CPayment(
            $phone,
            (float) $data['amount'],
            $data['reference']
        );

        return redirect()
            ->route('admin.mpesa.tools')
            ->with('mpesa_b2c_result', $result);
    }
}

