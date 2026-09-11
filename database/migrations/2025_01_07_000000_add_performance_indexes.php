<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $this->handleProductIndexes();
        $this->handleCategoryIndexes();
        $this->handleBrandIndexes();
        $this->handleProductImageIndexes();
        $this->handleSettingIndexes();
        $this->handleUserIndexes();
        $this->handleSystemTableIndexes();
    }

    /**
     * Handle product table indexes
     */
    private function handleProductIndexes(): void
    {
        if (!Schema::hasTable('products')) {
            return;
        }

        Schema::table('products', function (Blueprint $table) {
            $this->safeCreateIndex($table, ['is_active', 'is_featured'], 'idx_products_active_featured');
            $this->safeCreateIndex($table, ['category_id', 'brand_id'], 'idx_products_category_brand');
            $this->safeCreateIndex($table, ['price', 'sale_price'], 'idx_products_price_range');
            $this->safeCreateIndex($table, ['published_at'], 'idx_products_published_at');
            $this->safeCreateIndex($table, ['slug'], 'idx_products_slug');
            $this->safeCreateIndex($table, ['stock_qty'], 'idx_products_stock_qty');
            $this->safeCreateIndex($table, ['sku'], 'idx_products_sku');
        });
    }

    /**
     * Handle category table indexes
     */
    private function handleCategoryIndexes(): void
    {
        if (!Schema::hasTable('categories')) {
            return;
        }

        Schema::table('categories', function (Blueprint $table) {
            $this->safeCreateIndex($table, ['is_active', 'sort_order'], 'idx_categories_active_sort');
            $this->safeCreateIndex($table, ['parent_id', 'is_active'], 'idx_categories_parent_active');
            $this->safeCreateIndex($table, ['slug'], 'idx_categories_slug');
            $this->safeCreateIndex($table, ['name'], 'idx_categories_name');
        });
    }

    /**
     * Handle brand table indexes
     */
    private function handleBrandIndexes(): void
    {
        if (!Schema::hasTable('brands')) {
            return;
        }

        Schema::table('brands', function (Blueprint $table) {
            $this->safeCreateIndex($table, ['is_active', 'name'], 'idx_brands_active_name');
            $this->safeCreateIndex($table, ['slug'], 'idx_brands_slug');
            $this->safeCreateIndex($table, ['is_external'], 'idx_brands_external');
        });
    }

    /**
     * Handle product image table indexes
     */
    private function handleProductImageIndexes(): void
    {
        if (!Schema::hasTable('product_images')) {
            return;
        }

        Schema::table('product_images', function (Blueprint $table) {
            $this->safeCreateIndex($table, ['product_id', 'sort_order'], 'idx_product_images_product_sort');
            $this->safeCreateIndex($table, ['product_id', 'is_primary'], 'idx_product_images_primary');
            $this->safeCreateIndex($table, ['path'], 'idx_product_images_path');
        });
    }

    /**
     * Handle settings table indexes
     */
    private function handleSettingIndexes(): void
    {
        if (!Schema::hasTable('settings')) {
            return;
        }

        Schema::table('settings', function (Blueprint $table) {
            $this->safeCreateIndex($table, ['group', 'key'], 'idx_settings_group_key');
            $this->safeCreateIndex($table, ['group'], 'idx_settings_group');
        });
    }

    /**
     * Handle user table indexes
     */
    private function handleUserIndexes(): void
    {
        if (!Schema::hasTable('users')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            $this->safeCreateIndex($table, ['email'], 'idx_users_email');

            // Only add is_active index if the column exists
            if (Schema::hasColumn('users', 'is_active')) {
                $this->safeCreateIndex($table, ['is_active'], 'idx_users_active');
            }
        });
    }

    /**
     * Handle system table indexes (sessions, cache, etc.)
     */
    private function handleSystemTableIndexes(): void
    {
        // Sessions table indexes (if using database sessions)
        if (Schema::hasTable('sessions')) {
            Schema::table('sessions', function (Blueprint $table) {
                $this->safeCreateIndex($table, ['user_id'], 'idx_sessions_user_id');
                $this->safeCreateIndex($table, ['last_activity'], 'idx_sessions_last_activity');
            });
        }

        // Cache table indexes (if using database cache)
        if (Schema::hasTable('cache')) {
            Schema::table('cache', function (Blueprint $table) {
                $this->safeCreateIndex($table, ['expiration'], 'idx_cache_expiration');
            });
        }

        // Cache locks table indexes (if using database cache locks)
        if (Schema::hasTable('cache_locks')) {
            Schema::table('cache_locks', function (Blueprint $table) {
                $this->safeCreateIndex($table, ['expiration'], 'idx_cache_locks_expiration');
            });
        }
    }

    /**
     * Safely create an index if it doesn't exist and all columns exist
     */
    private function safeCreateIndex(Blueprint $table, array $columns, string $indexName): void
    {
        try {
            // Check if all columns exist
            foreach ($columns as $column) {
                if (!Schema::hasColumn($table->getTable(), $column)) {
                    return;
                }
            }

            // Check if index already exists
            if (!$this->indexExists($table->getTable(), $indexName)) {
                $table->index($columns, $indexName);
            }
        } catch (\Exception $e) {
            // Log the error but don't throw it to prevent migration failure
            Log::warning("Failed to create index {$indexName}: " . $e->getMessage());
        }
    }

    /**
     * Check if an index exists on a table
     */
    private function indexExists(string $table, string $indexName): bool
    {
        try {
            $indexes = Schema::getConnection()
                ->getSchemaBuilder()
                ->getIndexes($table);

            return collect($indexes)->contains('name', $indexName);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $this->dropIndexesIfExist('products', [
            'idx_products_active_featured',
            'idx_products_category_brand',
            'idx_products_price_range',
            'idx_products_published_at',
            'idx_products_slug',
            'idx_products_stock_qty',
            'idx_products_sku'
        ]);

        $this->dropIndexesIfExist('categories', [
            'idx_categories_active_sort',
            'idx_categories_parent_active',
            'idx_categories_slug',
            'idx_categories_name'
        ]);

        $this->dropIndexesIfExist('brands', [
            'idx_brands_active_name',
            'idx_brands_slug',
            'idx_brands_external'
        ]);

        $this->dropIndexesIfExist('product_images', [
            'idx_product_images_product_sort',
            'idx_product_images_primary',
            'idx_product_images_path'
        ]);

        $this->dropIndexesIfExist('settings', [
            'idx_settings_group_key',
            'idx_settings_group'
        ]);

        $this->dropIndexesIfExist('users', [
            'idx_users_email',
            'idx_users_active'
        ]);

        $this->dropIndexesIfExist('sessions', [
            'idx_sessions_user_id',
            'idx_sessions_last_activity'
        ]);

        $this->dropIndexesIfExist('cache', [
            'idx_cache_expiration'
        ]);

        $this->dropIndexesIfExist('cache_locks', [
            'idx_cache_locks_expiration'
        ]);
    }

    /**
     * Safely drop multiple indexes from a table
     */
    private function dropIndexesIfExist(string $table, array $indexes): void
    {
        if (!Schema::hasTable($table)) {
            return;
        }

        Schema::table($table, function (Blueprint $table) use ($indexes) {
            foreach ($indexes as $index) {
                try {
                    if ($this->indexExists($table->getTable(), $index)) {
                        $table->dropIndex($index);
                    }
                } catch (\Exception $e) {
                    Log::warning("Failed to drop index {$index}: " . $e->getMessage());
                }
            }
        });
    }
};
