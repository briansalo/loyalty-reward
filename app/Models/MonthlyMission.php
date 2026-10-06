<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonthlyMission extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'month',
        'purchase_goal',
        'points_reward',
        'enabled',
    ];

    protected $casts = [
        'enabled' => 'boolean',
    ];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }
}
