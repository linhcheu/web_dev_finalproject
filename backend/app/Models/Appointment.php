<?php

namespace App\Models;

use Illuminate\Support\Carbon;

class Appointment extends ApiModel
{
    protected $primaryKey = 'appointment_id';

    protected $casts = [
        'appointment_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected array $apiRelations = [
        'user' => [User::class, false],
        'hospital' => [Hospital::class, false],
    ];

    public function isUpcoming(): bool
    {
        return $this->appointment_date?->gte(today()) ?? false;
    }

    public function getFormattedDateAttribute(): string
    {
        return $this->appointment_date?->format('F j, Y') ?? '';
    }

    public function getFormattedTimeAttribute(): ?string
    {
        return $this->appointment_time ? Carbon::parse($this->appointment_time)->format('g:i A') : null;
    }

    public function getTimeUntilAttribute(): string
    {
        $days = (int) today()->diffInDays($this->appointment_date, false);

        return match (true) {
            $days > 0 => $days.' day'.($days > 1 ? 's' : '').' from now',
            $days < 0 => abs($days).' day'.($days < -1 ? 's' : '').' ago',
            default => 'Today',
        };
    }
}
