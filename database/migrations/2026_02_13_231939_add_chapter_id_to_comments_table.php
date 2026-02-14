<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('comments', function (Blueprint $table) {
            // Thêm chapter_id, cho phép null, khóa ngoại tới bảng chapters
            // Đặt sau comic_id cho dễ nhìn
            $table->foreignId('chapter_id')
                ->nullable()
                ->after('comic_id')
                ->constrained('chapters')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('comments', function (Blueprint $table) {
            $table->dropForeign(['chapter_id']);
            $table->dropColumn('chapter_id');
        });
    }
};
