<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('counties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('country_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['country_id', 'name']);
            $table->index(['country_id', 'sort_order']);
        });

        $kenyaId = DB::table('countries')->where('code', 'KE')->value('id');
        if (!$kenyaId) {
            $kenyaId = DB::table('countries')->insertGetId([
                'code' => 'KE',
                'name' => 'Kenya',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $counties = [
            'Baringo',
            'Bomet',
            'Bungoma',
            'Busia',
            'Elgeyo-Marakwet',
            'Embu',
            'Garissa',
            'Homa Bay',
            'Isiolo',
            'Kajiado',
            'Kakamega',
            'Kericho',
            'Kiambu',
            'Kilifi',
            'Kirinyaga',
            'Kisii',
            'Kisumu',
            'Kitui',
            'Kwale',
            'Laikipia',
            'Lamu',
            'Machakos',
            'Makueni',
            'Mandera',
            'Marsabit',
            'Meru',
            'Migori',
            'Mombasa',
            "Murang'a",
            'Nairobi',
            'Nakuru',
            'Nandi',
            'Narok',
            'Nyamira',
            'Nyandarua',
            'Nyeri',
            'Samburu',
            'Siaya',
            'Taita-Taveta',
            'Tana River',
            'Tharaka-Nithi',
            'Trans Nzoia',
            'Turkana',
            'Uasin Gishu',
            'Vihiga',
            'Wajir',
            'West Pokot',
        ];

        $now = now();
        $rows = [];
        foreach ($counties as $index => $county) {
            $rows[] = [
                'country_id' => $kenyaId,
                'name' => $county,
                'sort_order' => $index + 1,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        DB::table('counties')->insert($rows);
    }

    public function down(): void
    {
        Schema::dropIfExists('counties');
    }
};

