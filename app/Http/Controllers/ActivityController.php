<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\Transaction;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    public function index()
    {
        $business = Business::where('id', session('business_id'))->first();

        if (! $business) {
            $business = Business::query()->first();
        }

        $transactions = Transaction::where('user_id', auth()->id())
            ->with('business')
            ->orderByDesc('created_at')
            ->paginate(10);

        $mission = $business ? $business->monthlyMissionProgressFor(auth()->user()) : null;
        $socialShareMission = $business ? $business->socialShareMissionProgressFor(auth()->user()) : null;

        return view('activity', [
            'business' => $business,
            'transactions' => $transactions,
            'mission' => $mission,
            'socialShareMission' => $socialShareMission,
        ]);
    }
}
