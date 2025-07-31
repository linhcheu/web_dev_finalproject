<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

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
        'name',
        'email',
        'password_hash',
        'phone',
        'role',
        'profile_picture',
        'is_active',
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
    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

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
            
            // Delete tokens for this user
            $user->tokens()->delete();
            // DO NOT delete subscriptions here!
        });
    }

    // Relationships
    public function hospitals()
    {
        return $this->hasMany(Hospital::class, 'owner_id', 'user_id');
    }

    public function hospital()
    {
        return $this->hasOne(Hospital::class, 'owner_id', 'user_id');
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'user_id');
    }

    public function feedback()
    {
        return $this->hasMany(Feedback::class, 'user_id');
    }

    public function tokens()
    {
        return $this->hasMany(Token::class, 'user_id');
    }

    // Accessors
    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function getNameAttribute()
    {
        return $this->name ?? $this->first_name . ' ' . $this->last_name;
    }

    public function setNameAttribute($value)
    {
        $this->attributes['name'] = $value;
    }

    public function getPasswordAttribute()
    {
        return $this->password_hash;
    }

    public function setPasswordAttribute($value)
    {
        $this->attributes['password_hash'] = bcrypt($value);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByRole($query, $role)
    {
        return $query->where('role', $role);
    }

    // Methods
    public function isHospitalAdmin()
    {
        return $this->role === 'hospital_admin';
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isUser()
    {
        return $this->role === 'user';
    }
}
