<?php

namespace App\Models;

/** A patient or hospital administrator, as shown in the admin panel. */
class User extends ApiModel
{
    protected $primaryKey = 'user_id';

    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected array $apiRelations = [
        'hospital' => [Hospital::class, false],
    ];

    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }

    public function getNameAttribute(): string
    {
        return $this->full_name;
    }
}
