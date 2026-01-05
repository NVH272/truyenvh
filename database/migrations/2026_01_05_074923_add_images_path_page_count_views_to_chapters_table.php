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
        Schema::table('chapters', function (Blueprint $table) {
            $table->string('images_path')->nullable()->after('title');
            $table->unsignedInteger('page_count')->default(0)->after('images_path');
            $table->unsignedBigInteger('views')->default(0)->after('page_count');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chapters', function (Blueprint $table) {
            $table->dropColumn(['images_path', 'page_count', 'views']);
        });
    }
};
