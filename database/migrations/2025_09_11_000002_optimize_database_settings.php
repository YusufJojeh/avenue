<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Only run these optimizations in production environment
        if (env('APP_ENV') === 'production') {
            $this->optimizeDatabaseSettings();
        }
    }

    /**
     * Optimize database settings for production
     */
    private function optimizeDatabaseSettings(): void
    {
        try {
            // Set default character set and collation (only if we have privileges)
            $this->safeExecute('ALTER DATABASE `' . env('DB_DATABASE') . '` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');

            // Enable strict mode for better data integrity
            $this->safeExecute("SET GLOBAL sql_mode='STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION'");

            // Set innodb_buffer_pool_size if possible (requires privileges)
            $this->safeExecute('SET GLOBAL innodb_buffer_pool_size=2147483648'); // 2GB

        } catch (\Exception $e) {
            // Log warning but continue
            \Log::warning('Database optimization failed: ' . $e->getMessage());
        }
    }

    /**
     * Safely execute SQL commands
     */
    private function safeExecute(string $sql): void
    {
        try {
            DB::unprepared($sql);
        } catch (\Exception $e) {
            \Log::warning("Could not execute SQL: {$sql} - " . $e->getMessage());
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No need to reverse these settings
    }
};
