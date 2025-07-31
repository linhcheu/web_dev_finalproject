<?php

namespace App\Models\frontendModels;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hospital extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'hospitals';  // Changed from hospitals_table to match migration

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'hospital_id';

    /**
     * Indicates if the model's ID is auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'province',
        'location',
        'contact_info',
        'information',
        'profile_picture',
        'owner_id',
        'status'
    ];

    /**
     * Get the owner of the hospital.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id', 'user_id');
    }

/**
 * Get the appointments for the hospital.
 *
 * @return \Illuminate\Database\Eloquent\Relations\HasMany
 */
public function appointments()
{
    return $this->hasMany(Appointment::class, 'hospital_id', 'hospital_id');
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
