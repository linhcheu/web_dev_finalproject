<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Hash;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    protected $table = 'users_table';
    protected $primaryKey = 'user_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password_hash',
        'phone',
        'role',
        'profile_picture',
        'is_active'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password_hash',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    protected static function boot()
    {
        parent::boot();

        // When a user is deleted, also delete related data
        static::deleting(function ($user) {
            // Note: Hospitals are not deleted here to avoid circular dependency
            // Hospitals will delete their owner user when they are deleted
            
            // Delete appointments made by this user
            $user->appointments()->delete();
            
            // Delete feedback submitted by this user
            $user->feedback()->delete();
            
            // Note: Subscriptions are not deleted here as they belong to hospitals, not users
        });
    }

    /**
     * Get the password for the user.
     */
    public function getAuthPassword()
    {
        return $this->password_hash;
    }

    /**
     * Set the password attribute.
     */
    public function setPasswordAttribute($value)
    {
        $this->attributes['password_hash'] = Hash::make($value);
    }

    /**
     * Get the password attribute.
     */
    public function getPasswordAttribute()
    {
        return $this->password_hash;
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'user_id', 'user_id');
    }

    public function feedback()
    {
        return $this->hasMany(Feedback::class, 'user_id', 'user_id');
    }

    public function hospitals()
    {
        return $this->hasMany(Hospital::class, 'owner_id', 'user_id');
    }
}
