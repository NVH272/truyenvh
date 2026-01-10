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
        Schema::create('banned_words', function (Blueprint $table) {
            $table->id();
            $table->string('word')->unique();       // từ bị cấm
            $table->boolean('is_active')->default(true);
            $table->text('note')->nullable();       // ghi chú (tuỳ)
            $table->timestamps();
            $table->index(['is_active', 'word']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('banned_words');
    }
};
