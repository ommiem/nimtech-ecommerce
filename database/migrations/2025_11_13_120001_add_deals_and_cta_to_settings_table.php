<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->decimal('deals_under_threshold', 10, 2)->nullable()->after('theme_color');
            $table->string('homepage_cta_heading')->nullable()->after('deals_under_threshold');
            $table->string('homepage_cta_subtext')->nullable()->after('homepage_cta_heading');
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn(['deals_under_threshold', 'homepage_cta_heading', 'homepage_cta_subtext']);
        });
    }
};

