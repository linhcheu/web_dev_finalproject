<?php

namespace App\Models\frontendModels;

use Illuminate\Database\Eloquent\Model;

class Admin extends Model
{
    protected $table = 'admins_table';
    protected $primaryKey = 'admin_id';

    protected $fillable = [
        'username',
        'email',
        'password_hash',
        'profile_picture',
    ];

    protected $hidden = [
        'password_hash',
    ];

    public function getAuthPassword()
    {
        return $this->password_hash;
    }
}
