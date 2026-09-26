<?php
namespace App\Services\Payments;

use App\Models\Order;

class DummyGateway {
    public function charge(Order $order): void {}
}

