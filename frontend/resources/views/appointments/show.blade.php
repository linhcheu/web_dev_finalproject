@extends('layouts.app')

@section('title', 'Appointment Details - CareConnect')

@section('content')
<div class="appointment-detail-container">
    <div class="container">
        <!-- Header: Blue, with texture -->
        <div class="appointment-detail-header section-blue section-texture" style="text-align:center;margin-bottom:2rem;padding:2.5rem 1.5rem 2rem 1.5rem;border-radius:1.2rem;color:var(--primary-color);">
            <h1 style="margin-bottom:0.5rem;"><i class="fas fa-calendar-check" style="margin-right:0.7rem;"></i>Appointment Details</h1>
        </div>
        <!-- Detail Card: White, glassmorphism -->
        <div class="appointment-detail-card section-white" style="background:rgba(255,255,255,0.85);backdrop-filter:blur(8px);border-radius:1.2rem;box-shadow:0 4px 24px 0 rgba(0,0,0,0.10);padding:2.5rem 2rem;max-width:600px;margin:0 auto;">
            <div class="detail-list" style="margin-top:2rem;margin-bottom:2rem;">
                <div class="detail-item" style="margin-bottom:1.25rem;font-size:1.1rem;"><strong><i class="fas fa-hospital"></i> Hospital:</strong> {{ $appointment->hospital->name ?? 'N/A' }}</div>
                <div class="detail-item" style="margin-bottom:1.25rem;font-size:1.1rem;"><strong><i class="fas fa-calendar-day"></i> Date:</strong> {{ $appointment->appointment_date ? $appointment->appointment_date->format('F j, Y') : 'N/A' }}</div>
                <div class="detail-item" style="margin-bottom:1.25rem;font-size:1.1rem;"><strong><i class="fas fa-phone"></i> Phone:</strong> {{ $appointment->patient_phone ?? 'N/A' }}</div>
                <div class="detail-item" style="margin-bottom:1.25rem;font-size:1.1rem;"><strong><i class="fas fa-clock"></i> Time:</strong> {{ $appointment->formatted_time ?? 'TBD' }}</div>
                <div class="detail-item" style="margin-bottom:1.25rem;font-size:1.1rem;"><strong><i class="fas fa-info-circle"></i> Status:</strong> <span class="status-badge status-{{ $appointment->status }}">{{ ucfirst($appointment->status) }}</span></div>
                <div class="detail-item" style="margin-bottom:1.25rem;font-size:1.1rem;"><strong><i class="fas fa-notes-medical"></i> Symptoms/Reason:</strong> {{ $appointment->symptom }}</div>
                <div class="detail-item" style="margin-bottom:1.25rem;font-size:1.1rem;"><strong><i class="fas fa-calendar-plus"></i> Booked At:</strong> {{ $appointment->created_at->format('F j, Y, g:i A') }}</div>
            </div>
            <div class="actions mt-4" style="text-align:center;">
                <a href="{{ route('appointments') }}" class="btn btn-outline" style="font-size:1.1rem;padding:0.7em 2.2em;border-radius:2em;box-shadow:0 2px 8px rgba(100,116,139,0.07);font-weight:600;transition:transform 0.12s;"><i class="fas fa-arrow-left"></i> Back to Appointments</a>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
.section-blue {
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    color: white;
    background-image: url('https://www.transparenttextures.com/patterns/cubes.png');
    background-blend-mode: lighten;
}
.section-white {
    background: #fff;
    color: var(--dark-color);
}
.btn-outline {
    background: transparent;
    color: var(--primary-color);
    border: 2px solid var(--primary-color);
    font-weight: 600;
    border-radius: 2em;
    padding: 0.7em 2em;
    box-shadow: 0 2px 8px rgba(0,0,0,0.07);
    transition: background 0.2s, color 0.2s;
}
.btn-outline:hover {
    background: var(--primary-color);
    color: #fff;
}
.status-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 1rem;
    font-size: 0.9rem;
    font-weight: 500;
    background: #e0e7ff;
    color: #3730a3;
}
.status-upcoming { background: #d1fae5; color: #065f46; }
.status-completed { background: #e0e7ff; color: #3730a3; }
.status-cancelled { background: #fee2e2; color: #991b1b; }
.status-no_show { background: #fef3c7; color: #d97706; }
@media (max-width: 700px) {
    .appointment-detail-header, .appointment-detail-card {
        padding: 1.2rem 0.5rem !important;
    }
}
</style>
@endsection 