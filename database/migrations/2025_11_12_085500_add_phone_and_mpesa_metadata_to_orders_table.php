<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('phone')->nullable()->after('email');
            $table->string('mpesa_phone')->nullable()->after('mpesa_checkout_request_id');
            $table->decimal('mpesa_amount', 10, 2)->nullable()->after('mpesa_phone');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['phone', 'mpesa_phone', 'mpesa_amount']);
        });
    }
};

