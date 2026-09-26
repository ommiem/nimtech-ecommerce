<?php

namespace App\Models;

use App\Models\Concerns\GeneratesUniqueSlugs;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Brand extends Model
{
    use GeneratesUniqueSlugs;
    protected $fillable = ['name','slug','image_path','website','active'];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
