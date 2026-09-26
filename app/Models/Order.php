<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        // shipping
        'full_name', 'email', 'phone', 'address', 'city', 'state', 'postal_code',
        // billing
        'billing_full_name', 'billing_email', 'billing_address', 'billing_city', 'billing_state', 'billing_postal_code',
        'total', 'status', 'payment_method', 'mpesa_merchant_request_id', 'mpesa_checkout_request_id', 'mpesa_phone', 'mpesa_amount', 'mpesa_receipt', 'mpesa_result_code', 'mpesa_result_desc', 'paid_at',
    ];

    protected $casts = [
        'total' => 'float',
        'paid_at' => 'datetime',
        'mpesa_amount' => 'float',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
