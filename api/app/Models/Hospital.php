<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Hospital extends Model
{
    public const STATUSES = ['pending', 'active', 'rejected'];

    protected $primaryKey = 'hospital_id';

    protected $fillable = [
        'name', 'province', 'location', 'contact_info',
        'information', 'profile_picture', 'owner_id', 'status',
    ];

    protected static function booted(): void
    {
        // A hospital's administrator account only exists for that hospital, so remove it too.
        // Appointments and subscriptions are removed by ON DELETE CASCADE.
        static::deleted(function (Hospital $hospital) {
            $hospital->owner?->delete();
        });
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id', 'user_id');
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class, 'hospital_id');
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class, 'hospital_id');
    }

    /** The hospital's current (latest) subscription. */
    public function subscription(): HasOne
    {
        return $this->hasOne(Subscription::class, 'hospital_id')->latestOfMany('subscription_id');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    /** Hospitals patients can book: approved and with a running subscription. */
    public function scopeBookable(Builder $query): Builder
    {
        return $query->active()->whereHas('subscriptions', fn (Builder $q) => $q
            ->where('status', 'active')
            ->whereDate('end_date', '>=', now()));
    }
}
