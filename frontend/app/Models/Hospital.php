<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Hospital extends Model
{
    use HasFactory;

    protected $primaryKey = 'hospital_id';

    protected $fillable = [
        'name',
        'location',
        'contact_info',
        'information',
        'owner_id',
        'status',
        'profile_picture',
        'province',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        // When a hospital is deleted, also delete related appointments, subscriptions, and the owner user
        static::deleting(function ($hospital) {
            \Log::info('Hospital deleting event triggered', [
                'hospital_id' => $hospital->hospital_id,
                'hospital_name' => $hospital->name,
                'owner_id' => $hospital->owner_id
            ]);
            
            $hospital->appointments()->delete();
            $hospital->subscriptions()->delete();
            
            // Delete the owner user (admin)
            if ($hospital->owner) {
                \Log::info('Deleting hospital owner', [
                    'user_id' => $hospital->owner->user_id,
                    'user_email' => $hospital->owner->email
                ]);
                $hospital->owner->delete();
            } else {
                \Log::info('No owner found for hospital');
            }
        });
    }

    // Relationships
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'hospital_id');
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class, 'hospital_id', 'hospital_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopeWithActiveSubscription($query)
    {
        return $query->whereHas('subscriptions', function($q) {
            $q->where('status', 'active')->where('end_date', '>', now());
        });
    }

    // Methods
    public function isActive()
    {
        return $this->status === 'active';
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isRejected()
    {
        return $this->status === 'rejected';
    }

    public function getSubscriptionPlan()
    {
        return $this->subscriptions->first()?->plan_type ?? 'basic';
    }

    public function hasActiveSubscription()
    {
        return $this->subscriptions->where('status', 'active')->count() > 0;
    }
}
