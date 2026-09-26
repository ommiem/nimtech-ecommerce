<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_price_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->decimal('price', 10, 2);
            $table->timestamp('recorded_at')->useCurrent();
            $table->index(['product_id', 'recorded_at']);
        });

        DB::table('products')->orderBy('id')->chunkById(500, function ($products) {
            DB::table('product_price_histories')->insert($products->map(fn ($product) => [
                'product_id' => $product->id,
                'price' => $product->price,
                'recorded_at' => $product->updated_at ?? now(),
            ])->all());
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_price_histories');
    }
};
