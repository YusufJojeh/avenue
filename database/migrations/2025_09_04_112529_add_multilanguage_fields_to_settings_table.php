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
        Schema::table('settings', function (Blueprint $table) {
            // Add locale field for multilingual settings
            $table->string('locale', 5)->nullable()->after('value');
            $table->index(['key', 'locale'], 'settings_key_locale_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropIndex('settings_key_locale_index');
            $table->dropColumn('locale');
        });
    }
};
