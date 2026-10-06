<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Business extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'token',
        'loyalty_type',
        'amount_per_points',
        'logo_url',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function rewards()
    {
        return $this->hasMany(Reward::class);
    }

    public function monthlyMissions()
    {
        return $this->hasMany(MonthlyMission::class);
    }

    public function socialShareMissions()
    {
        return $this->hasMany(SocialShareMission::class);
    }

    public function currentMonthlyMission(): MonthlyMission
    {
        $currentMonth = now()->format('Y-m');

        $mission = $this->monthlyMissions()
            ->where('month', $currentMonth)
            ->first();

        if ($mission) {
            return $mission;
        }

        $latestMission = $this->monthlyMissions()
            ->orderByDesc('month')
            ->first();

        if ($latestMission) {
            return $latestMission;
        }

        return new MonthlyMission([
            'business_id' => $this->id,
            'month' => $currentMonth,
            'purchase_goal' => 4,
            'points_reward' => 100,
            'enabled' => true,
        ]);
    }

    public function currentSocialShareMission(): SocialShareMission
    {
        $currentMonth = now()->format('Y-m');

        $mission = $this->socialShareMissions()
            ->where('month', $currentMonth)
            ->first();

        if ($mission) {
            return $mission;
        }

        $latestMission = $this->socialShareMissions()
            ->orderByDesc('month')
            ->first();

        if ($latestMission) {
            return $latestMission;
        }

        return new SocialShareMission([
            'business_id' => $this->id,
            'month' => $currentMonth,
            'description' => 'Post a photo featuring your item purchase, tag our page, and earn points.',
            'points_reward' => 5,
            'enabled' => true,
        ]);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function monthlyMissionProgressFor(User $user): array
    {
        $mission = $this->currentMonthlyMission();

        $goal = (int) ($mission->purchase_goal ?? 4);
        $reward = (int) ($mission->points_reward ?? 0);
        $enabled = (bool) ($mission->enabled ?? true);
        $monthStart = now()->startOfMonth();
        $monthEnd = now()->endOfMonth();

        $purchaseDays = $this->transactions()
            ->where('user_id', $user->id)
            ->where('transaction_type', 'add_points')
            ->where('status', 1)
            ->whereBetween('approved_at', [$monthStart, $monthEnd])
            ->select(DB::raw('DATE(approved_at) as purchase_day'))
            ->distinct()
            ->pluck('purchase_day');

        $completedPurchases = $purchaseDays->count();

        $hasClaimed = $this->transactions()
            ->where('user_id', $user->id)
            ->where('transaction_type', 'mission_reward')
            ->where('status', 1)
            ->whereBetween('approved_at', [$monthStart, $monthEnd])
            ->exists();

        return [
            'count' => $completedPurchases,
            'goal' => $goal,
            'reward' => $reward,
            'enabled' => $enabled,
            'claimed' => $hasClaimed,
            'eligible' => $enabled && $completedPurchases >= $goal && ! $hasClaimed,
        ];
    }

    public function socialShareMissionProgressFor(User $user): array
    {
        $mission = $this->currentSocialShareMission();
        $monthStart = now()->startOfMonth();
        $monthEnd = now()->endOfMonth();

        $claimed = $this->transactions()
            ->where('user_id', $user->id)
            ->where('transaction_type', 'social_share_reward')
            ->where('status', 1)
            ->whereBetween('approved_at', [$monthStart, $monthEnd])
            ->exists();

        return [
            'reward' => (int) ($mission->points_reward ?? 5),
            'description' => $mission->description ?? 'Post a photo featuring your item purchase, tag our page, and earn points.',
            'enabled' => (bool) ($mission->enabled ?? true),
            'claimed' => $claimed,
            'eligible' => (bool) ($mission->enabled ?? true) && ! $claimed,
        ];
    }
}
