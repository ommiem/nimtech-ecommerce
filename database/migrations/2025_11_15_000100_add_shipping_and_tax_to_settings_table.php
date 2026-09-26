<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->decimal('shipping_flat_rate', 10, 2)->nullable()->after('enable_cod');
            $table->decimal('tax_rate', 5, 2)->nullable()->after('shipping_flat_rate');
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn(['shipping_flat_rate', 'tax_rate']);
        });
    }
};

