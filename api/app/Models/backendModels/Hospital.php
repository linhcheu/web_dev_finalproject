<?php

namespace App\Models\backendModels;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hospital extends Model
{
    use HasFactory;

    protected $primaryKey = 'hospital_id';

    protected $fillable = [
        'name',
        'location',
        'contact_info',
        'information',
        'province',
        'owner_id',
        'status',
        'profile_picture',
    ];

    protected $casts = [
        'api_enabled' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'status' => 'string',
    ];

    protected static function boot()
    {
        parent::boot();

        // When a hospital is deleted, also delete related appointments, subscriptions, and the owner user
        static::deleting(function ($hospital) {
            \Log::info('Hospital deleting event triggered (Backend)', [
                'hospital_id' => $hospital->hospital_id,
                'hospital_name' => $hospital->name,
                'owner_id' => $hospital->owner_id
            ]);
            
            $hospital->appointments()->delete();
            $hospital->subscriptions()->delete();
            
            // Delete the owner user (admin)
            if ($hospital->owner) {
                \Log::info('Deleting hospital owner (Backend)', [
                    'user_id' => $hospital->owner->user_id,
                    'user_email' => $hospital->owner->email
                ]);
                $hospital->owner->delete();
            } else {
                \Log::info('No owner found for hospital (Backend)');
            }
        });
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'hospital_id', 'hospital_id');
    }

    public function subscription()
    {
        return $this->hasOne(Subscription::class, 'hospital_id', 'hospital_id');
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class, 'hospital_id', 'hospital_id');
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id', 'user_id');
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
} 