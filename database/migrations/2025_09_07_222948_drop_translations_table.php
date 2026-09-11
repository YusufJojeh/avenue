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
        Schema::dropIfExists('translations');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('translations', function (Blueprint $table) {
            $table->id();
            $table->string('locale', 5)->index();
            $table->string('group_name')->index();
            $table->string('key_name')->index();
            $table->text('value');
            $table->boolean('needs_review')->default(false);
            $table->timestamp('reviewed_at')->nullable();
            $table->unsignedBigInteger('reviewed_by')->nullable();
            $table->timestamps();
            
            $table->unique(['locale', 'group_name', 'key_name']);
            $table->foreign('reviewed_by')->references('id')->on('users')->onDelete('set null');
        });
    }
};