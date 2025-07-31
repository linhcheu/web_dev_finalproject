<?php

namespace App\Models\frontendModels;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'appointments';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'appointment_id';

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
        'user_id',
        'hospital_id',
        'appointment_date',
        'appointment_time',
        'patient_phone',
        'symptom',
        'status'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'appointment_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Get the user that owns the appointment.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function hospital()
    {
        return $this->belongsTo(Hospital::class, 'hospital_id', 'hospital_id');
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
