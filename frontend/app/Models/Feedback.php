<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Feedback extends Model
{
    use HasFactory;

    protected $primaryKey = 'feedback_id';

    protected $table = 'feedback';

    protected $fillable = [
        'user_id',
        'comments',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Scopes
    public function scopeRecent($query, $limit = 10)
    {
        return $query->latest()->limit($limit);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    // Methods
    public function getExcerptAttribute($length = 100)
    {
        return strlen($this->comments) > $length 
            ? substr($this->comments, 0, $length) . '...' 
            : $this->comments;
    }

    public function getFormattedDateAttribute()
    {
        return $this->created_at->format('F j, Y');
    }
}
