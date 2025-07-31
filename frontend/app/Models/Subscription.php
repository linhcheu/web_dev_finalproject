<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Subscription extends Model
{
    use HasFactory;

    protected $primaryKey = 'subscription_id';

    protected $fillable = [
        'hospital_id',
        'plan_type',
        'price',
        'start_date',
        'end_date',
        'status',
        'auto_renew',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'auto_renew' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function hospital()
    {
        return $this->belongsTo(Hospital::class, 'hospital_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeExpired($query)
    {
        return $query->where('status', 'expired');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    // Methods
    public function isActive()
    {
        return $this->status === 'active';
    }

    public function isExpired()
    {
        return $this->status === 'expired';
    }

    public function isCancelled()
    {
        return $this->status === 'cancelled';
    }

    public function isExpiringSoon($days = 30)
    {
        return $this->end_date->diffInDays(now(), false) <= $days;
    }

    public function getDaysUntilExpiry()
    {
        return $this->end_date->diffInDays(now(), false);
    }

    public function getPlanNameAttribute()
    {
        return ucfirst($this->plan_type);
    }

    public function getFormattedPriceAttribute()
    {
        return '$' . number_format($this->price, 2);
    }
}
