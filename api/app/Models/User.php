<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

/**
 * A patient ("user") or a hospital administrator ("hospital_admin").
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory;

    public const ROLE_USER = 'user';
    public const ROLE_HOSPITAL_ADMIN = 'hospital_admin';

    protected $table = 'users_table';
    protected $primaryKey = 'user_id';

    protected $fillable = [
        'first_name', 'last_name', 'email', 'password_hash',
        'phone', 'role', 'profile_picture', 'is_active',
    ];

    protected $hidden = ['password_hash'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected $appends = ['full_name'];

    protected static function booted(): void
    {
        // Appointments, feedback and hospitals are removed by the database's ON DELETE CASCADE.
        static::deleting(fn (User $user) => $user->tokens()->delete());
    }

    public function getAuthPassword(): string
    {
        return $this->password_hash;
    }

    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }

    public function hospital(): HasOne
    {
        return $this->hasOne(Hospital::class, 'owner_id', 'user_id');
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class, 'user_id');
    }

    public function feedback(): HasMany
    {
        return $this->hasMany(Feedback::class, 'user_id');
    }

    public function isHospitalAdmin(): bool
    {
        return $this->role === self::ROLE_HOSPITAL_ADMIN;
    }
}
