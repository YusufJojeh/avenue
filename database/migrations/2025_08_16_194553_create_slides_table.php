<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::create('slides', function (Blueprint $table) {
      $table->id();
      $table->enum('position', ['main','slider'])->default('slider'); // hero vs carousel
      $table->string('title')->nullable();
      $table->string('subtitle')->nullable();
      $table->string('image_path');              // e.g. banners/hero.jpg (disk=public)
      $table->string('cta_label')->nullable();
      $table->string('cta_url')->nullable();
      $table->unsignedInteger('sort_order')->default(0);
      $table->timestamp('starts_at')->nullable();
      $table->timestamp('ends_at')->nullable();
      $table->boolean('is_active')->default(true);
      $table->timestamps();
    });
  }
  public function down(): void { Schema::dropIfExists('slides'); }
};
