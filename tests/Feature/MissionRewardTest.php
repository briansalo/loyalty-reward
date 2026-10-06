<?php

use App\Models\Business;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('admin can configure a monthly mission and a customer can claim it after meeting the goal', function () {
    $business = Business::create([
        'name' => 'Moon Cafe',
        'token' => 'moon-cafe-token',
        'loyalty_type' => 0,
        'amount_per_points' => 50,
        'status' => true,
    ]);

    $business->monthlyMissions()->create([
        'month' => now()->format('Y-m'),
        'purchase_goal' => 4,
        'points_reward' => 250,
        'enabled' => true,
    ]);

    $user = User::factory()->create();
    $this->actingAs($user);
    session(['business_id' => $business->id, 'business_token' => $business->token]);

    foreach (range(1, 4) as $index) {
        Transaction::create([
            'business_id' => $business->id,
            'user_id' => $user->id,
            'amount' => 100 + $index,
            'transaction_type' => 'add_points',
            'status' => 1,
            'submitted_at' => now(),
            'approved_at' => now()->subDay($index - 1),
        ]);
    }

    $this->post('/admin/dashboard', [
        'business_id' => $business->id,
        'amount_per_points' => 50,
        'monthly_purchase_goal' => 4,
        'monthly_points_reward' => 250,
        'monthly_mission_enabled' => true,
        'reward_name' => [],
        'reward_points' => [],
    ]);

    $this->assertDatabaseHas('monthly_missions', [
        'business_id' => $business->id,
        'purchase_goal' => 4,
        'points_reward' => 250,
    ]);

    $this->post('/mission/claim')
        ->assertRedirect(route('dashboard'));

    $this->assertDatabaseHas('transactions', [
        'business_id' => $business->id,
        'user_id' => $user->id,
        'transaction_type' => 'mission_reward',
        'status' => 1,
    ]);

    expect($user->fresh()->points)->toBe(250);
});

test('mission counts one purchase day per day even if the user buys multiple times in the same day', function () {
    $business = Business::create([
        'name' => 'Daily Count Cafe',
        'token' => 'daily-count-token',
        'loyalty_type' => 0,
        'amount_per_points' => 50,
        'status' => true,
    ]);

    $business->monthlyMissions()->create([
        'month' => now()->format('Y-m'),
        'purchase_goal' => 2,
        'points_reward' => 100,
        'enabled' => true,
    ]);

    $user = User::factory()->create();

    Transaction::create([
        'business_id' => $business->id,
        'user_id' => $user->id,
        'amount' => 100,
        'transaction_type' => 'add_points',
        'status' => 1,
        'submitted_at' => now(),
        'approved_at' => now(),
    ]);

    Transaction::create([
        'business_id' => $business->id,
        'user_id' => $user->id,
        'amount' => 150,
        'transaction_type' => 'add_points',
        'status' => 1,
        'submitted_at' => now(),
        'approved_at' => now(),
    ]);

    Transaction::create([
        'business_id' => $business->id,
        'user_id' => $user->id,
        'amount' => 200,
        'transaction_type' => 'add_points',
        'status' => 1,
        'submitted_at' => now(),
        'approved_at' => now()->addDay(),
    ]);

    expect($business->monthlyMissionProgressFor($user)['count'])->toBe(2);
});

test('admin can disable the monthly mission from the checkbox', function () {
    $business = Business::create([
        'name' => 'Disabled Mission Cafe',
        'token' => 'disable-mission-token',
        'loyalty_type' => 0,
        'amount_per_points' => 50,
        'status' => true,
    ]);

    $business->monthlyMissions()->create([
        'month' => now()->format('Y-m'),
        'purchase_goal' => 3,
        'points_reward' => 150,
        'enabled' => true,
    ]);

    $this->post('/admin/dashboard', [
        'business_id' => $business->id,
        'amount_per_points' => 50,
        'monthly_purchase_goal' => 3,
        'monthly_points_reward' => 150,
        'monthly_mission_enabled' => false,
        'reward_name' => [],
        'reward_points' => [],
    ]);

    expect($business->fresh()->currentMonthlyMission()->enabled)->toBeFalse();
});

test('admin can configure the monthly social share mission and a customer can claim it', function () {
    $business = Business::create([
        'name' => 'Share Cafe',
        'token' => 'share-cafe-token',
        'loyalty_type' => 0,
        'amount_per_points' => 50,
        'status' => true,
    ]);

    $user = User::factory()->create();
    $this->actingAs($user);
    session(['business_id' => $business->id, 'business_token' => $business->token]);

    $this->post('/admin/dashboard', [
        'business_id' => $business->id,
        'amount_per_points' => 50,
        'monthly_purchase_goal' => 4,
        'monthly_points_reward' => 250,
        'social_share_points_reward' => 75,
        'social_share_mission_enabled' => true,
        'reward_name' => [],
        'reward_points' => [],
    ]);

    $this->assertDatabaseHas('social_share_missions', [
        'business_id' => $business->id,
        'points_reward' => 75,
        'enabled' => true,
    ]);

    $this->post('/social-share/claim')
        ->assertRedirect(route('dashboard'));

    $this->assertDatabaseHas('transactions', [
        'business_id' => $business->id,
        'user_id' => $user->id,
        'transaction_type' => 'social_share_reward',
        'status' => 1,
    ]);

    expect($user->fresh()->points)->toBe(75);
});
