<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SocialShareMission extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'month',
        'description',
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
