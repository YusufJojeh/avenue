<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table): void {
            $table->string('seo_title_ar')->nullable();
            $table->string('seo_title_en')->nullable();
            $table->string('seo_description_ar', 500)->nullable();
            $table->string('seo_description_en', 500)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table): void {
            $table->dropColumn([
                'seo_title_ar', 'seo_title_en',
                'seo_description_ar', 'seo_description_en',
            ]);
        });
    }
};
