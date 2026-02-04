<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Bảng authors
        Schema::create('authors', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        // Bảng pivot author_comic (N-N)
        Schema::create('author_comic', function (Blueprint $table) {
            $table->unsignedBigInteger('author_id');
            $table->unsignedBigInteger('comic_id');

            $table->primary(['author_id', 'comic_id']);

            $table->foreign('author_id')->references('id')->on('authors')->onDelete('cascade');
            $table->foreign('comic_id')->references('id')->on('comics')->onDelete('cascade');
        });

        // Di chuyển dữ liệu cũ từ cột comics.author sang bảng authors + pivot
        if (Schema::hasTable('comics') && Schema::hasColumn('comics', 'author')) {
            $comics = DB::table('comics')
                ->select('id', 'author')
                ->whereNotNull('author')
                ->where('author', '!=', '')
                ->get();

            foreach ($comics as $comic) {
                $raw = $comic->author;
                if (!$raw) {
                    continue;
                }

                $names = collect(explode(',', $raw))
                    ->map(fn ($name) => trim($name))
                    ->filter()
                    ->unique();

                if ($names->isEmpty()) {
                    continue;
                }

                $authorIds = [];

                foreach ($names as $name) {
                    $existingId = DB::table('authors')->where('name', $name)->value('id');
                    if ($existingId) {
                        $authorIds[] = $existingId;
                        continue;
                    }

                    $authorIds[] = DB::table('authors')->insertGetId([
                        'name'       => $name,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                foreach ($authorIds as $authorId) {
                    $exists = DB::table('author_comic')
                        ->where('author_id', $authorId)
                        ->where('comic_id', $comic->id)
                        ->exists();

                    if (!$exists) {
                        DB::table('author_comic')->insert([
                            'author_id' => $authorId,
                            'comic_id'  => $comic->id,
                        ]);
                    }
                }
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('author_comic');
        Schema::dropIfExists('authors');
    }
};

