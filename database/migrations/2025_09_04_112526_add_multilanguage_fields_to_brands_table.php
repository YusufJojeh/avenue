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
        Schema::table('brands', function (Blueprint $table) {
            // Add multilanguage fields
            $table->string('name_en')->nullable()->after('name');
            $table->string('name_ar')->nullable()->after('name_en');
            $table->string('slug_en')->nullable()->after('slug');
            $table->string('slug_ar')->nullable()->after('slug_en');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('brands', function (Blueprint $table) {
            // Remove multilanguage fields
            $table->dropColumn([
                'name_en', 'name_ar',
                'slug_en', 'slug_ar',
            ]);
        });
    }
};
