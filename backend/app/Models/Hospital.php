<?php

namespace App\Models;

class Hospital extends ApiModel
{
    protected $primaryKey = 'hospital_id';

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected array $apiRelations = [
        'subscription' => [Subscription::class, false],
        'owner' => [User::class, false],
    ];

    public function getSubscriptionPlan(): string
    {
        return $this->subscription?->plan_type ?? 'basic';
    }
}
