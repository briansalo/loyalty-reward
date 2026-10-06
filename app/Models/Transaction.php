<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'business_id',
        'user_id',
        'amount',
        'points',
        'transaction_type',
        'status',
        'submitted_at',
        'approved_at',
    ];

    protected $casts = [
        'amount'       => 'decimal:2',
        'submitted_at' => 'datetime',
        'approved_at'  => 'datetime',
    ];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}