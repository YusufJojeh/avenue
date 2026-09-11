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
        Schema::table('products', function (Blueprint $table) {
            // Add multilanguage fields
            $table->string('name_en')->nullable()->after('name');
            $table->string('name_ar')->nullable()->after('name_en');
            $table->string('slug_en')->nullable()->after('slug');
            $table->string('slug_ar')->nullable()->after('slug_en');
            $table->text('short_description_en')->nullable()->after('short_description');
            $table->text('short_description_ar')->nullable()->after('short_description_en');
            $table->text('description_en')->nullable()->after('description');
            $table->text('description_ar')->nullable()->after('description_en');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Remove multilanguage fields
            $table->dropColumn([
                'name_en', 'name_ar',
                'slug_en', 'slug_ar',
                'short_description_en', 'short_description_ar',
                'description_en', 'description_ar',
            ]);
        });
    }
};
