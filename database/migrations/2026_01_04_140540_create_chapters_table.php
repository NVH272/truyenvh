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
            $table->unsignedInteger('chapter_number');
            $table->string('title')->nullable();
            $table->string('images_path'); // Đường dẫn đến thư mục chứa ảnh
            $table->unsignedInteger('page_count')->default(0); // Số trang ảnh
            $table->unsignedBigInteger('views')->default(0);
            $table->timestamps();

            // Đảm bảo mỗi comic không có chapter_number trùng lặp
            $table->unique(['comic_id', 'chapter_number']);
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
