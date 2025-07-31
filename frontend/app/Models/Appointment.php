<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Appointment extends Model
{
    use HasFactory;

    protected $primaryKey = 'appointment_id';

    protected $fillable = [
        'user_id',
        'hospital_id',
        'appointment_date',
        'appointment_time',
        'patient_phone',
        'symptom',
        'status',
    ];

    protected $casts = [
        'appointment_date' => 'date',
        'appointment_time' => 'datetime:H:i',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function hospital()
    {
        return $this->belongsTo(Hospital::class, 'hospital_id');
    }

    // Scopes
    public function scopeUpcoming($query)
    {
        return $query->where('appointment_date', '>=', now()->toDateString());
    }

    public function scopePast($query)
    {
        return $query->where('appointment_date', '<', now()->toDateString());
    }

    public function scopeByHospital($query, $hospitalId)
    {
        return $query->where('hospital_id', $hospitalId);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    // Methods
    public function isUpcoming()
    {
        return $this->appointment_date >= now()->toDateString();
    }

    public function isPast()
    {
        return $this->appointment_date < now()->toDateString();
    }

    public function getFormattedDateAttribute()
    {
        return $this->appointment_date->format('F j, Y');
    }

    public function getFormattedTimeAttribute()
    {
        if ($this->appointment_time) {
            return \Carbon\Carbon::parse($this->appointment_time)->format('g:i A');
        }
        return null;
    }

    public function getTimeUntilAttribute()
    {
        $days = now()->diffInDays($this->appointment_date, false);
        
        if ($days > 0) {
            return $days . ' day' . ($days > 1 ? 's' : '') . ' from now';
        } elseif ($days < 0) {
            return abs($days) . ' day' . (abs($days) > 1 ? 's' : '') . ' ago';
        } else {
            return 'Today';
        }
    }
}
