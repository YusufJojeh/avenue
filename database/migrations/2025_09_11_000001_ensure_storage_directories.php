<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\File;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $directories = [
            storage_path('app/public/products'),
            storage_path('app/public/brands'),
            storage_path('app/public/slides'),
            storage_path('app/public/categories'),
            storage_path('app/backups/database'),
            storage_path('app/backups/files'),
            storage_path('framework/cache'),
            storage_path('framework/sessions'),
            storage_path('framework/views'),
            storage_path('logs'),
        ];

        foreach ($directories as $directory) {
            if (!File::exists($directory)) {
                File::makeDirectory($directory, 0755, true);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // We don't remove directories in down migration for safety
    }
};
