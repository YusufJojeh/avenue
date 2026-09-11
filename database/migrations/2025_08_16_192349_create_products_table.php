<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::create('products', function (Blueprint $table) {
      $table->id();

      // Relations
      $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
      $table->foreignId('brand_id')->nullable()->constrained('brands')->nullOnDelete();

      // Main fields
      $table->string('name');
      $table->string('slug')->unique();
      $table->text('short_description')->nullable();
      $table->longText('description')->nullable();
      $table->string('sku')->unique()->nullable();

      // Pricing & inventory
      $table->decimal('price', 10, 2);
      $table->decimal('sale_price', 10, 2)->nullable();
      $table->unsignedInteger('stock_qty')->default(0);

      // Status for homepage sections
      $table->boolean('is_active')->default(true);
      $table->boolean('is_featured')->default(false); // "Special"
      $table->timestamp('published_at')->nullable()->index();

      $table->timestamps();
    });
  }

  public function down(): void {
    Schema::dropIfExists('products');
  }
};
