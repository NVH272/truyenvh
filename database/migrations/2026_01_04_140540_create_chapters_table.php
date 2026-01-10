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
        Schema::create('chapters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('comic_id')->constrained('comics')->onDelete('cascade');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->string('title');
            $table->string('images_path')->nullable();
            $table->unsignedInteger('page_count')->default(0);
            $table->string('chapter_number', 20);
            $table->string('slug')->nullable();
            $table->unsignedBigInteger('views')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            // Đảm bảo mỗi comic không có chapter_number trùng lặp
            $table->unique(['comic_id', 'chapter_number']);
            $table->index(['comic_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chapters');
    }
};
