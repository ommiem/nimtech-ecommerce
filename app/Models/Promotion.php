<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Promotion extends Model
{
    protected $fillable = [
        'code','type','value','min_subtotal','usage_limit','used','starts_at','ends_at','active'
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'value' => 'float',
        'min_subtotal' => 'float',
    ];

    public function isActive(): bool
    {
        if(!$this->active) return false;
        $now = now();
        if($this->starts_at && $now->lt($this->starts_at)) return false;
        if($this->ends_at && $now->gt($this->ends_at)) return false;
        if($this->usage_limit && $this->used >= $this->usage_limit) return false;
        return true;
    }
}

