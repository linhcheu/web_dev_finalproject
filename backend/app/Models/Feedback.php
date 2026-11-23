<?php

namespace App\Models;

class Feedback extends ApiModel
{
    protected $primaryKey = 'feedback_id';

    protected $casts = [
        'created_at' => 'datetime',
    ];

    protected array $apiRelations = [
        'user' => [User::class, false],
    ];
}
