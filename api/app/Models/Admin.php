<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

/**
 * A CareConnect platform administrator (uses the admin panel).
 */
class Admin extends Authenticatable
{
    use HasApiTokens;

    protected $table = 'admins_table';
    protected $primaryKey = 'admin_id';

    protected $fillable = ['username', 'email', 'password_hash', 'profile_picture'];

    protected $hidden = ['password_hash'];

    public function getAuthPassword(): string
    {
        return $this->password_hash;
    }
}
