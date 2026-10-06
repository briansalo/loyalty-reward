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
        Schema::table('social_share_missions', function (Blueprint $table) {
            if (! Schema::hasColumn('social_share_missions', 'description')) {
                $table->text('description')->default('Post a photo featuring your item purchase, tag our page, and earn points.')->after('month');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('social_share_missions', function (Blueprint $table) {
            if (Schema::hasColumn('social_share_missions', 'description')) {
                $table->dropColumn('description');
            }
        });
    }
};
