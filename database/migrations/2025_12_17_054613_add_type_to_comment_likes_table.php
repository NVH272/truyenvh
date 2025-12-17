<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('comment_likes', function (Blueprint $table) {
            // like | dislike
            $table->string('type', 10)->default('like')->after('user_id');

            // tránh 1 user thả nhiều lần trên cùng comment
            $table->unique(['comment_id', 'user_id']);
        });

        Schema::table('comments', function (Blueprint $table) {
            if (!Schema::hasColumn('comments', 'dislikes_count')) {
                $table->unsignedInteger('dislikes_count')->default(0)->after('likes_count');
            }
        });
    }

    public function down(): void
    {
        Schema::table('comment_likes', function (Blueprint $table) {
            $table->dropUnique(['comment_id', 'user_id']);
            $table->dropColumn('type');
        });

        Schema::table('comments', function (Blueprint $table) {
            if (Schema::hasColumn('comments', 'dislikes_count')) {
                $table->dropColumn('dislikes_count');
            }
        });
    }
};
