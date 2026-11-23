<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Subscription extends Model
{
    public const PLANS = ['basic', 'premium', 'enterprise'];
    public const STATUSES = ['active', 'expired', 'cancelled'];

    protected $primaryKey = 'subscription_id';

    protected $fillable = [
        'hospital_id', 'plan_type', 'price', 'start_date', 'end_date', 'status', 'auto_renew',
    ];

    protected $casts = [
        'start_date' => 'date:Y-m-d',
        'end_date' => 'date:Y-m-d',
        'auto_renew' => 'boolean',
        'price' => 'decimal:2',
    ];

    public function hospital(): BelongsTo
    {
        return $this->belongsTo(Hospital::class, 'hospital_id');
    }
}
