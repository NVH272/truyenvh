<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('comics', function (Blueprint $table) {
            $table->id();

            $table->string('title');
            $table->string('slug')->unique();
            $table->longText('description')->nullable();

            $table->string('cover_image')->nullable();
            $table->string('author')->nullable();

            $table->enum('status', ['ongoing', 'completed', 'dropped'])->default('ongoing');

            $table->unsignedBigInteger('views')->default(0);
            $table->unsignedBigInteger('follows')->default(0);

            $table->float('rating')->default(0);
            $table->unsignedInteger('rating_count')->default(0);

            $table->unsignedInteger('chapter_count')->default(0);

            $table->date('published_at')->nullable();
            $table->timestamp('last_chapter_at')->nullable();

            $table->timestamps();
        });
    }
};
