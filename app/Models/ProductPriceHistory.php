<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductPriceHistory extends Model
{
    public $timestamps = false;
    protected $fillable = ['product_id', 'price', 'recorded_at'];
    protected $casts = ['price' => 'float', 'recorded_at' => 'datetime'];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
