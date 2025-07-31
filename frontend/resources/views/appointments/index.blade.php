@extends('layouts.app')

@section('title', 'My Appointments - CareConnect')

@section('content')
<div class="appointments-container">
    <div class="container">
        <!-- Image Placeholder: Add your static image here -->
        <div class="image-placeholder" style="width:100%;max-width:420px;height:auto;aspect-ratio:21/9;margin:0 auto 2rem auto;display:flex;align-items:center;justify-content:center;background:linear-gradient(135deg,#e0e7ef 60%,#f8fafc 100%);border-radius:1rem;box-shadow:0 2px 12px rgba(0,0,0,0.07);color:#b0b7c3;font-size:1.1rem;overflow:hidden;position:relative;">
            <img src="{{ asset('images/appointment-hero.png') }}" alt="Appointments Image" style="width:100%;height:100%;object-fit:cover;border-radius:1rem;position:absolute;top:0;left:0;" />
            
        </div>
        <!-- Header: Blue, with texture -->
        <div class="appointments-header section-blue section-texture" style="text-align:center;margin-bottom:2rem;padding:2.5rem 1.5rem 2rem 1.5rem;border-radius:1.2rem;color:white;">
            <h1 style="margin-bottom:0.5rem;"><i class="fas fa-calendar-alt" style="margin-right:0.7rem;"></i>My Appointments</h1>
            <p style="color:black;font-size:1.125rem; ">Manage and track your healthcare appointments</p>
            <div class="header-actions" style="margin-top:1.5rem;">
                <a href="{{ route('appointments.create') }}" class="btn btn-light" style="background:white;color:var(--primary-color);font-weight:600;border-radius:2em;padding:0.7em 2em;"><i class="fas fa-plus"></i> Book New Appointment</a>
            </div>
        </div>
        <!-- Stats Cards: White -->
        <div class="stats-grid section-white" style="background:white;border-radius:1.2rem;box-shadow:0 4px 24px 0 rgba(0,0,0,0.08);padding:2rem;margin-bottom:2rem;display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:1.5rem;">
            <div class="stat-card card" style="background:rgba(255,255,255,0.85);backdrop-filter:blur(6px);border-radius:1.2rem;box-shadow:0 2px 8px 0 rgba(0,0,0,0.07);padding:1.2rem;display:flex;align-items:center;gap:1rem;">
                <div class="stat-icon"><i class="fas fa-calendar-check" style="font-size:1.7rem;color:var(--primary-color);"></i></div>
                <div class="stat-content">
                    <h3>{{ $appointments->where('appointment_date', '>=', now()->toDateString())->count() }}</h3>
                    <p>Upcoming</p>
                </div>
            </div>
            <div class="stat-card card" style="background:rgba(255,255,255,0.85);backdrop-filter:blur(6px);border-radius:1.2rem;box-shadow:0 2px 8px 0 rgba(0,0,0,0.07);padding:1.2rem;display:flex;align-items:center;gap:1rem;">
                <div class="stat-icon"><i class="fas fa-history" style="font-size:1.7rem;color:#64748b;"></i></div>
                <div class="stat-content">
                    <h3>{{ $appointments->where('appointment_date', '<', now()->toDateString())->count() }}</h3>
                    <p>Past</p>
                </div>
            </div>
            <div class="stat-card card" style="background:rgba(255,255,255,0.85);backdrop-filter:blur(6px);border-radius:1.2rem;box-shadow:0 2px 8px 0 rgba(0,0,0,0.07);padding:1.2rem;display:flex;align-items:center;gap:1rem;">
                <div class="stat-icon"><i class="fas fa-calendar-day" style="font-size:1.7rem;color:#f59e42;"></i></div>
                <div class="stat-content">
                    <h3>{{ $appointments->where('appointment_date', '=', now()->toDateString())->count() }}</h3>
                    <p>Today</p>
                </div>
            </div>
        </div>
        <!-- Appointments List: Blue, with texture -->
        <div class="appointments-section section-blue section-texture" style="background:linear-gradient(135deg, var(--primary-color), var(--secondary-color));border-radius:1.2rem;padding:2rem;margin-bottom:2rem;">
            <!-- Filter by Date -->
            <form method="GET" action="" style="max-width:350px;margin-bottom:1.5rem;">
                <div style="display:flex;gap:0.5rem;align-items:center;">
                    <input type="date" name="date" value="{{ $appointment->appointment_date ?? '' }}" class="form-control" style="flex:1;padding:0.6rem 1rem;border-radius:0.5rem;border:1.5px solid #e5e7eb;">
                    <button type="submit" class="btn btn-primary" style="padding:0.6rem 1.2rem;border-radius:0.5rem;font-size:1rem;"><i class="fas fa-search"></i> Filter</button>
                    @if(request('date'))
                    <a href="{{ route('appointments') }}" class="btn btn-outline" style="padding:0.6rem 1.2rem;border-radius:0.5rem;font-size:1rem;background:#fff;"><i class="fas fa-list"></i> Show All</a>
                    @endif
                </div>
            </form>
            <div  style="margin-bottom:1.5rem;display:flex;align-items:center;gap:0.7rem;">
                <i class="fas fa-list" style="font-size:1.3rem;"></i>
                <h2 style="margin:0; color:white">All Appointments</h2>
            </div>
            @if($appointments->count() > 0)
                <div class="appointments-grid" style="color:black" >
                    @foreach($appointments as $appointment)
                        <div class="appointment-card card" data-status="{{ $appointment->status }}" data-date="{{ $appointment->appointment_date->toDateString() }}" style="background:white; backdrop-filter:blur(6px);border-radius:1.2rem;box-shadow:0 2px 8px 0 rgba(0,0,0,0.07);padding:1.2rem;">
                            <div class="appointment-header">
                                <div class="appointment-id">
                                    <span class="id-badge">#{{ $appointment->appointment_id }}</span>
                                </div>
                                <div class="appointment-status">
                                    <span class="status-badge status-{{ $appointment->status }}">{{ ucfirst(str_replace('_', ' ', $appointment->status)) }}</span>
                                </div>
                            </div>
                            <div class="appointment-content">
                                <div class="hospital-info">
                                    <h3>{{ $appointment->hospital->name }}</h3>
                                    <p><i class="fas fa-map-marker-alt"></i> {{ $appointment->hospital->location }}</p>
                                    <p><i class="fas fa-phone"></i> {{ $appointment->hospital->contact_info }}</p>
                                </div>
                                <div class="appointment-details">
                                    <div class="detail-item"><span class="detail-label" style="font-weight:bold">Date:</span><span class="detail-value">  {{ $appointment->formatted_date }}</span></div>
                                    <div class="detail-item"><span class="detail-label" style="font-weight:bold">Time:</span><span class="detail-value">  {{ $appointment->formatted_time ?? 'TBD' }}</span></div>
                                    <div class="detail-item"><span class="detail-label" style="font-weight:bold">Symptoms:</span><span class="detail-value"> {{ Str::limit($appointment->symptom, 100) }}</span></div>
                                </div>
                            </div>
                            <div class="appointment-actions" style="margin-top:1.2rem;display:flex;gap:1rem;">
                                <a href="{{ route('appointments.show', $appointment) }}" class="btn btn-light" style="border-radius:2em;"><i class="fas fa-eye"></i> View Details</a>
                            </div>
                        </div>
                    @endforeach
                </div>
             
            @else
                <div class="empty-state card" style="background:rgba(255,255,255,0.85);backdrop-filter:blur(6px);border-radius:1.2rem;box-shadow:0 2px 8px 0 rgba(0,0,0,0.07);padding:2.5rem 1.5rem;">
                    <div class="empty-icon"><i class="fas fa-calendar-times"></i></div>
                    <h3>No Appointments Found</h3>
                    <p style="margin-bottom:1rem;">You haven't booked any appointments yet. Start by booking your first appointment!</p>
                    <a href="{{ route('appointments.create') }}" class="btn btn-light" style="background:white;color:var(--primary-color);font-weight:600;border-radius:2em;padding:0.7em 2em;"><i class="fas fa-plus"></i> Book Your First Appointment</a>
                </div>
            @endif
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
.btn-light {
    background: #fff;
    color: var(--primary-color);
    border: none;
    font-weight: 600;
    border-radius: 2em;
    padding: 0.7em 2em;
    box-shadow: 0 2px 8px rgba(0,0,0,0.07);
    transition: background 0.2s, color 0.2s;
}
.btn-light:hover {
    background: var(--secondary-color);
    color: #fff;
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
@media (max-width: 900px) {
    .stats-grid {
        grid-template-columns: 1fr;
    }
}
@media (max-width: 700px) {
    .appointments-header, .appointments-section, .stats-grid {
        padding: 1.2rem 0.5rem !important;
    }
    .appointments-grid {
        grid-template-columns: 1fr !important;
        gap: 1rem !important;
    }
}
</style>
@endsection

@section('scripts')
<script>
function cancelAppointment(appointmentId) {
    if (confirm('Are you sure you want to cancel this appointment?')) {
        // AJAX call to cancel appointment
        CareConnect.ajaxRequest(`/appointments/${appointmentId}/cancel`, 'DELETE')
            .then(response => {
                CareConnect.showNotification('Appointment cancelled successfully', 'success');
                location.reload();
            })
            .catch(error => {
                CareConnect.showNotification('Error cancelling appointment', 'error');
            });
    }
}
</script>
@endsection 