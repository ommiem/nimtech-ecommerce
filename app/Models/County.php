<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class County extends Model
{
    protected $fillable = [
        'country_id',
        'name',
        'sort_order',
    ];

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }
}

