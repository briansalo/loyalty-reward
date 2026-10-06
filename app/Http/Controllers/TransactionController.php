<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Business;
use App\Models\Transaction;
use App\Models\User;
use App\Models\SocialShareMission;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function showPointsFormValidation(string $businessToken)
    {
        if (!$businessToken) {
            abort(403, 'Business token is required.');
        }
  

        $business = Business::where('token', $businessToken)->where('status', 1)->first();


        if (!$business) {
            abort(403, 'Invalid business token.');
        }

        session([
            'business_token' => $businessToken,
            'business_id'    => $business->id,
        ]);

        // Redirect to the points form route
        return redirect()->route('transaction.save.points.form');
    }

    public function showPointsForm()
    {
        // Retrieve the business token from the session
        Transaction::where('user_id', auth()->id())
            ->where('business_id', session('business_id'))
            ->where('status', 0)
            ->delete();

        $business = Business::where('token', session('business_token'))
            ->where('status', 1)
            ->first();
             
        
        return view('show-points-form', [
            'points' => auth()->user()->points,
            'business' => $business,
        ]);

    }

    public function submit(Request $request)
    {
        $validated = $request->validate([
            'purchase_amount' => ['required', 'numeric'],
        ]);

        Transaction::create([
            'business_id'     => session('business_id'),
            'user_id'         => auth()->id(),
            'amount'          => $validated['purchase_amount'],
            'transaction_type' => 'add_points',
            'status'          => 0,
            'submitted_at'    => now(),
            'approved_at'     => null,
        ]);

        return redirect()->route('transaction.confirmation');
    }

    public function claimMonthlyMission()
    {
        $business = Business::where('id', session('business_id'))
            ->where('status', 1)
            ->first();

        if (! $business) {
            return redirect()->route('dashboard')->with('error', 'Please select a valid business before claiming a mission reward.');
        }

        $mission = $business->monthlyMissionProgressFor(auth()->user());

        if (! ($mission['enabled'] ?? true)) {
            return redirect()->route('dashboard')->with('error', 'This monthly mission is currently disabled.');
        }

        if ($mission['claimed']) {
            return redirect()->route('dashboard')->with('success', 'You already claimed this month’s mission reward.');
        }

        if ($mission['count'] < $mission['goal']) {
            $remaining = $mission['goal'] - $mission['count'];

            return redirect()->route('dashboard')->with('error', "You need {$remaining} more purchase(s) to claim this month’s reward.");
        }

        DB::transaction(function () use ($business, $mission) {
            Transaction::create([
                'business_id' => $business->id,
                'user_id' => auth()->id(),
                'points' => (float) $mission['reward'],
                'transaction_type' => 'mission_reward',
                'status' => 1,
                'submitted_at' => now(),
                'approved_at' => now(),
            ]);

            User::whereKey(auth()->id())
                ->increment('points', $mission['reward']);
        });

        return redirect()->route('dashboard')->with('success', 'Monthly mission reward claimed successfully.');
    }

    public function missionValidation(string $businessToken)
    {

        if (!$businessToken) {
            abort(403, 'Business token is required.');
        }
  

        $business = Business::where('token', $businessToken)->where('status', 1)->first();


        if (!$business) {
            abort(403, 'Invalid business token.');
        }

        session([
            'business_token' => $businessToken,
            'business_id'    => $business->id,
        ]);
        $missionType = session('mission_type');

        if($missionType === 'social_share_reward') {
            return redirect()->route('transaction.social.share.claim');
        }
                
    }

    public function SubmitSocialShareMission()
    {
        Transaction::where('user_id', auth()->id())
            ->where('business_id', session('business_id'))
            ->where('status', 0)
            ->where('transaction_type', 'social_share_reward')
            ->delete();
            
        $socialShare = SocialShareMission::where('business_id', session('business_id'))
            ->where('enabled', true)
            ->firstOrFail();
        Transaction::create([
            'business_id'     => session('business_id'),
            'user_id'         => auth()->id(),
            'points'          => $socialShare->points_reward,
            'transaction_type' => 'social_share_reward',
            'status'          => 0,
            'submitted_at'    => now(),
            'approved_at'     => null,
        ]);

        session([
            'mission_type' => 'social_share_reward'
        ]);

        return redirect()->route('social.share.confirmation');
    }
    public function ConfirmationSocialShareMission()
    {

        $transaction = Transaction::where('user_id', auth()->id())
            ->where('business_id', session('business_id'))
            ->where('status', 0)
            ->where('transaction_type', 'social_share_reward')
            ->lockForUpdate()
            ->firstOrFail();
        
        return view('mission-confirmation', [

            'points' => $transaction->points,
   
        ]);
    }
    public function claimSocialShareMission()
    {
        session()->forget([
            'business_token'
        ]);

        $business = Business::where('id', session('business_id'))
            ->where('status', 1)
            ->first();

        if (! $business) {
            return redirect()->route('dashboard')->with('error', 'Please select a valid business before claiming the social share reward.');
        }

        // $mission = $business->socialShareMissionProgressFor(auth()->user());

        // if (! ($mission['enabled'] ?? true)) {
        //     return redirect()->route('dashboard')->with('error', 'This social share mission is currently disabled.');
        // }

        // if ($mission['claimed']) {
        //     return redirect()->route('dashboard')->with('success', 'You already claimed this month’s social share reward.');
        // }

        DB::transaction(function () {
            $transaction = Transaction::where('user_id', auth()->id())
                ->where('business_id', session('business_id'))
                ->where('status', 0)
                ->lockForUpdate()
                ->firstOrFail();
            $transaction->update([
                'status' => 1,
                'approved_at' => now(),
            ]);

            User::where('id', auth()->id())
                ->increment('points', $transaction->points);   
        });
   

        return redirect()->route('dashboard')->with('success', 'Social share reward claimed successfully.');
    }

    public function redeemConfirmation($rewardId)
    {
        $reward = \App\Models\Reward::findOrFail($rewardId);

        if ((auth()->user()->points ?? 0) < $reward->points) {
            return redirect()->route('rewards')->with('error', 'You do not have enough points to redeem this reward.');
        }

        return view('redeem-confirmation', [
            'reward' => $reward,
            'business' => $reward->business,
            'points' => $reward->points,
        ]);
    }

    public function redeemConfirmed($rewardId)
    {
        $reward = \App\Models\Reward::findOrFail($rewardId);

        $userPoints = auth()->user()->points ?? 0;
        if ($userPoints < $reward->points) {
            return redirect()->route('rewards')->with('error', 'You do not have enough points to redeem this reward.');
        }

        Transaction::create([
            'business_id'     => $reward->business_id,
            'user_id'         => auth()->id(),
            'points'          => $reward->points,
            'transaction_type' => 'redeem',
            'status'          => 1,
            'submitted_at'    => now(),
            'approved_at'     => now(),
        ]);

        \App\Models\User::where('id', auth()->id())->decrement('points', $reward->points);

        return redirect()->route('rewards')->with('success', 'Reward redeemed successfully.');
    }

    public function confirmationView()
    {
        $transaction = Transaction::where('user_id', auth()->id())
            ->where('business_id', session('business_id'))
            ->where('status', 0)
            ->where('transaction_type', 'add_points')
            ->lockForUpdate()
            ->firstOrFail();

        $business = Business::where('token', session('business_token'))
            ->where('status', 1)
            ->firstOrFail();

        $points = floor(
            $transaction->amount / $business->amount_per_points
        );
  
        return view('confirmation', [
            'amount' => $transaction->amount,
            'points' => $points,
            'transactionType' => 'add_points',
        ]);
    }

    public function cancel()
    {
        Transaction::where('user_id', auth()->id())
            ->where('business_id', session('business_id'))
            ->where('status', 0)
            ->delete();
        return redirect()->route('dashboard');
    }

    public function confirmed()
    {
        DB::transaction(function () {

            $transaction = Transaction::where('user_id', auth()->id())
                ->where('business_id', session('business_id'))
                ->where('status', 0)
                ->lockForUpdate()
                ->firstOrFail();

            $business = Business::where('token', session('business_token'))
                ->where('status', 1)
                ->firstOrFail();

            $points = floor(
                $transaction->amount / $business->amount_per_points
            );

            $transaction->update([
                'status' => 1,
                'approved_at' => now(),
            ]);

            User::where('id', auth()->id())
                ->increment('points', $points);
        });

        session()->forget([
            'business_token'
        ]);

        return redirect()->route('dashboard');
    }
}
