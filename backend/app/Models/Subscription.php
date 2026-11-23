<?php

namespace App\Models;

class Subscription extends ApiModel
{
    protected $primaryKey = 'subscription_id';

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'auto_renew' => 'boolean',
    ];

    protected array $apiRelations = [
        'hospital' => [Hospital::class, false],
    ];

    public function getPlanNameAttribute(): string
    {
        return ucfirst((string) $this->plan_type);
    }
}
