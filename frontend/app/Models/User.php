<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable as AuthenticatableTrait;
use Illuminate\Contracts\Auth\Authenticatable;

class User extends ApiModel implements Authenticatable
{
    use AuthenticatableTrait;

    protected $primaryKey = 'user_id';

    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
    ];

    protected array $apiRelations = [
        'hospital' => [Hospital::class, false],
    ];

    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }

    public function isHospitalAdmin(): bool
    {
        return $this->role === 'hospital_admin';
    }
}
