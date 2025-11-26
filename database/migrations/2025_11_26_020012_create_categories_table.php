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
        Schema::create('categories', function (Blueprint $table) {
            $table->id();

            // Tên thể loại
            $table->string('name')->unique();

            // Slug thân thiện URL (action → action-manga)
            $table->string('slug')->unique();

            // Mô tả ngắn (optional)
            $table->string('description')->nullable();

            // Trạng thái: hiển thị hoặc ẩn
            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
