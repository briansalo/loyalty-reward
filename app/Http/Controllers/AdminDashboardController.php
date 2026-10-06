<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\Reward;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $business = Business::where('id', session('business_id'))->first();

        if (! $business) {
            $business = Business::query()->first();
        }

        if (! $business) {
            $business = Business::create([
                'name' => 'Business Name',
                'token' => Str::random(20),
                'loyalty_type' => 0,
                'amount_per_points' => 50,
                'status' => true,
            ]);
        }

        $business->load('rewards');
        session(['business_id' => $business->id]);

        $users = User::with(['transactions' => function ($query) use ($business) {
            $query->where('business_id', $business->id)
                ->orderByDesc('created_at');
        }])->paginate(10);

        return view('admin-dashboard', [
            'business' => $business,
            'rewards' => $business->rewards()->get(),
            'users' => $users,
        ]);
    }

    public function showUserTransactions(User $user)
    {

        $business = Business::where('id', session('business_id'))->first();

        if (! $business) {
            $business = Business::query()->firstOrFail();
        }

        $user->load(['transactions' => function ($query) use ($business) {
            $query->where('business_id', $business->id)
                ->orderByDesc('created_at');
        }]);

        $transactions = $user->transactions()->orderByDesc('created_at')->paginate(10);

        return view('admin-user-transactions', [
            'business' => $business,
            'user' => $user,
            'transactions' => $transactions,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'amount_per_points' => ['required', 'numeric', 'min:1'],
            'monthly_purchase_goal' => ['nullable', 'integer', 'min:1'],
            'monthly_points_reward' => ['nullable', 'integer', 'min:1'],
            'monthly_mission_enabled' => ['nullable', 'boolean'],
            'social_share_points_reward' => ['nullable', 'integer', 'min:1'],
            'social_share_mission_enabled' => ['nullable', 'boolean'],
            'social_share_mission_text' => ['nullable', 'string', 'max:255'],
            'business_id' => ['nullable', 'integer', 'exists:businesses,id'],
            'reward_name' => ['nullable', 'array'],
            'reward_name.*' => ['nullable', 'string'],
            'reward_points' => ['nullable', 'array'],
            'reward_points.*' => ['nullable', 'integer', 'min:1'],
        ]);

        $business = Business::findOrFail($validated['business_id'] ?? session('business_id') ?? 1);
        $business->update([
            'amount_per_points' => $validated['amount_per_points'],
        ]);

        $mission = $business->monthlyMissions()
            ->where('month', now()->format('Y-m'))
            ->firstOrNew();

        $mission->business_id = $business->id;
        $mission->month = now()->format('Y-m');
        $mission->purchase_goal = (int) ($validated['monthly_purchase_goal'] ?? $mission->purchase_goal ?? 4);
        $mission->points_reward = (int) ($validated['monthly_points_reward'] ?? $mission->points_reward ?? 100);
        $mission->enabled = $request->has('monthly_mission_enabled')
            ? filter_var($request->input('monthly_mission_enabled'), FILTER_VALIDATE_BOOLEAN)
            : false;
        $mission->save();

        $socialShareMission = $business->socialShareMissions()
            ->where('month', now()->format('Y-m'))
            ->firstOrNew();

        $socialShareMission->business_id = $business->id;
        $socialShareMission->month = now()->format('Y-m');
        $socialShareMission->description = trim((string) ($validated['social_share_mission_text'] ?? $socialShareMission->description ?? 'Post a photo featuring your item purchase, tag our page, and earn points.'));
        $socialShareMission->description = $socialShareMission->description !== ''
            ? $socialShareMission->description
            : 'Post a photo featuring your purchase, tag our page, and earn points. You can claim this reward once per month.';
        $socialShareMission->points_reward = (int) ($validated['social_share_points_reward'] ?? $socialShareMission->points_reward ?? 5);
        $socialShareMission->enabled = $request->has('social_share_mission_enabled')
            ? filter_var($request->input('social_share_mission_enabled'), FILTER_VALIDATE_BOOLEAN)
            : false;
        $socialShareMission->save();

        $business->rewards()->delete();

        $names = $request->input('reward_name', []);
        $points = $request->input('reward_points', []);

        foreach ($names as $index => $name) {
            $cleanName = trim((string) $name);
            $rewardPoints = (int) ($points[$index] ?? 0);

            if ($cleanName !== '' && $rewardPoints > 0) {
                Reward::create([
                    'business_id' => $business->id,
                    'name' => $cleanName,
                    'points' => $rewardPoints,
                ]);
            }
        }

        return redirect()->route('admin.dashboard')->with('success', 'Loyalty settings updated successfully.');
    }
}
