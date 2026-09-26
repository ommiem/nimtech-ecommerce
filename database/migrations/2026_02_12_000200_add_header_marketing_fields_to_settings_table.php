<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->string('header_notice_text', 255)->nullable()->after('tax_rate');
            $table->json('header_special_tiles')->nullable()->after('header_notice_text');
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn(['header_notice_text', 'header_special_tiles']);
        });
    }
};

