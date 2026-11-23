<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Appointment extends Model
{
    public const STATUSES = ['upcoming', 'completed', 'no_show'];

    protected $primaryKey = 'appointment_id';

    protected $fillable = [
        'user_id', 'hospital_id', 'appointment_date', 'appointment_time',
        'patient_phone', 'symptom', 'status',
    ];

    protected $casts = [
        'appointment_date' => 'date:Y-m-d',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function hospital(): BelongsTo
    {
        return $this->belongsTo(Hospital::class, 'hospital_id');
    }

    public function scopeUpcoming(Builder $query): Builder
    {
        return $query->whereDate('appointment_date', '>=', now());
    }
}
