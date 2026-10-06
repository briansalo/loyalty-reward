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
        Schema::create('monthly_missions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->string('month');
            $table->unsignedInteger('purchase_goal')->default(4);
            $table->unsignedInteger('points_reward')->default(100);
            $table->boolean('enabled')->default(true);
            $table->timestamps();

            $table->unique(['business_id', 'month']);
        });

        Schema::table('businesses', function (Blueprint $table) {
            if (Schema::hasColumn('businesses', 'monthly_purchase_goal')) {
                $table->dropColumn(['monthly_purchase_goal', 'monthly_points_reward', 'monthly_mission_enabled']);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monthly_missions');

        Schema::table('businesses', function (Blueprint $table) {
            if (! Schema::hasColumn('businesses', 'monthly_purchase_goal')) {
                $table->unsignedInteger('monthly_purchase_goal')->default(4)->after('amount_per_points');
                $table->unsignedInteger('monthly_points_reward')->default(100)->after('monthly_purchase_goal');
                $table->boolean('monthly_mission_enabled')->default(true)->after('monthly_points_reward');
            }
        });
    }
};
