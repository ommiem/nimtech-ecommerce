<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->boolean('enable_mpesa')->default(true)->after('homepage_cta_subtext');
            $table->boolean('enable_cod')->default(true)->after('enable_mpesa');
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn(['enable_mpesa', 'enable_cod']);
        });
    }
};

