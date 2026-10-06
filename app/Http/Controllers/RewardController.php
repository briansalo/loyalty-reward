<?php

namespace App\Http\Controllers;

use App\Models\Business;
use Illuminate\Http\Request;

class RewardController extends Controller
{
    public function index()
    {
        $business = Business::where('id', session('business_id'))->first();

        if (! $business) {
            $business = Business::query()->first();
        }

        return view('rewards', [
            'business' => $business,
            'rewards' => $business ? $business->rewards()->get() : collect(),
        ]);
    }
}
