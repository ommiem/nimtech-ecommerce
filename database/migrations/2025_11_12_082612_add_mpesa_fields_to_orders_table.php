<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('payment_method')->nullable()->after('status');
            $table->string('mpesa_merchant_request_id')->nullable()->after('payment_method');
            $table->string('mpesa_checkout_request_id')->nullable()->after('mpesa_merchant_request_id');
            $table->string('mpesa_receipt')->nullable()->after('mpesa_checkout_request_id');
            $table->string('mpesa_result_code')->nullable()->after('mpesa_receipt');
            $table->string('mpesa_result_desc')->nullable()->after('mpesa_result_code');
            $table->timestamp('paid_at')->nullable()->after('mpesa_result_desc');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'payment_method','mpesa_merchant_request_id','mpesa_checkout_request_id','mpesa_receipt','mpesa_result_code','mpesa_result_desc','paid_at'
            ]);
        });
    }
};
