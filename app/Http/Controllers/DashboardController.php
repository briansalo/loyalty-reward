<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Business;
use App\Models\User;


class DashboardController extends Controller
{
    public function home()
    {
        $business = Business::where('id', session('business_id'))
            ->where('status', 1)
            ->first();

        if (! $business) {
            $business = Business::where('status', 1)->first();
        }

        if ($business && ! session('business_id')) {
            session(['business_id' => $business->id]);
        }

        $mission = null;
        $socialShareMission = null;

        if ($business) {
            $mission = $business->monthlyMissionProgressFor(auth()->user());
            $socialShareMission = $business->socialShareMissionProgressFor(auth()->user());
        }

        return view('dashboard', [
            'points' => auth()->user()->points ?? 0,
            'business' => $business,
            'mission' => $mission,
            'socialShareMission' => $socialShareMission,
        ]);
    }
}
