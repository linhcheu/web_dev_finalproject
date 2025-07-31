<?php

namespace App\Models\frontendModels;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'feedback';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'feedback_id';

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
        'comments'
    ];

    /**
     * Get the user who created this feedback.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    /**
     * Get the hospital through appointments.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOneThrough
     */
    public function hospital()
    {
        // This creates a relationship through appointments table
        return $this->hasOneThrough(
            Hospital::class,
            Appointment::class,
            'user_id', // Foreign key on appointments table
            'hospital_id', // Foreign key on hospitals table
            'user_id', // Local key on feedback table
            'hospital_id' // Local key on appointments table
        );
    }
}


