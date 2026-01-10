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
            if (!Schema::hasColumn('chapters', 'images_path')) {
                $table->string('images_path')->nullable()->after('title');
            }
            if (!Schema::hasColumn('chapters', 'page_count')) {
                $table->unsignedInteger('page_count')->default(0)->after('images_path');
            }
            if (!Schema::hasColumn('chapters', 'views')) {
                $table->unsignedBigInteger('views')->default(0)->after('page_count');
            }
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
