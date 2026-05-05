<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('member_sections', function (Blueprint $table) {
            if (! Schema::hasColumn('member_sections', 'anchor')) {
                $table->string('anchor', 120)->nullable()->after('title');
            }
        });

        Schema::table('member_modules', function (Blueprint $table) {
            if (! Schema::hasColumn('member_modules', 'anchor')) {
                $table->string('anchor', 120)->nullable()->after('title');
            }
        });
    }

    public function down(): void
    {
        Schema::table('member_modules', function (Blueprint $table) {
            if (Schema::hasColumn('member_modules', 'anchor')) {
                $table->dropColumn('anchor');
            }
        });

        Schema::table('member_sections', function (Blueprint $table) {
            if (Schema::hasColumn('member_sections', 'anchor')) {
                $table->dropColumn('anchor');
            }
        });
    }
};
