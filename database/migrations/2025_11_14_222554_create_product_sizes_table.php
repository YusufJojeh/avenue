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
        Schema::create('product_sizes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->string('size'); // e.g., "S", "M", "L", "XL", "42", "43", etc.
            $table->decimal('price', 10, 2)->nullable(); // Optional: different price for this size
            $table->decimal('sale_price', 10, 2)->nullable(); // Optional: different sale price
            $table->unsignedInteger('stock_qty')->default(0); // Stock quantity for this size
            $table->string('sku')->nullable(); // Optional: unique SKU for this size
            $table->unsignedInteger('sort_order')->default(0); // For ordering sizes
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Indexes
            $table->index('product_id');
            $table->index('is_active');
            $table->index('sort_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_sizes');
    }
};
