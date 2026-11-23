<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable as AuthenticatableTrait;
use Illuminate\Contracts\Auth\Authenticatable;

/** The logged-in platform administrator. */
class Admin extends ApiModel implements Authenticatable
{
    use AuthenticatableTrait;

    protected $primaryKey = 'admin_id';

    protected $casts = [
        'created_at' => 'datetime',
    ];

    /** The layout and profile page show "name"; admins only have a username. */
    public function getNameAttribute(): string
    {
        return (string) $this->username;
    }
}
