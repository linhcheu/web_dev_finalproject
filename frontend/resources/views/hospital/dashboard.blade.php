@extends('layouts.app')

@section('title', 'Hospital Dashboard - CareConnect')

@section('content')
<div class="dashboard-container">
    <div class="container">
        <!-- Dashboard Header (Blue, with texture) -->
        <div class="dashboard-section section-blue section-texture" style="padding:2.5rem 1.5rem 2rem 1.5rem; border-radius:1.2rem; margin-bottom:2rem; position:relative; color:white; display: flex; flex-direction: column; align-items: center; justify-content: center;">
            <div class="dashboard-title" style="display:flex;align-items:center;gap:1rem; justify-content: center;">
                <i class="fas fa-hospital-alt" style="font-size:2.2rem; color:var(--primary-color);"></i>
                <div style="text-align: center;">
                    <h1 style="margin:0;font-size:2rem;font-weight:700;">Hospital Dashboard</h1>
                    <p style="margin:0;font-size:1.1rem;">Welcome back, {{ auth()->user()->first_name}} {{ auth()->user()->last_name}}!</p>
                </div>
            </div>
            <div class="dashboard-actions" style="margin-top:1.2rem;display:flex;gap:1rem;">
                <a href="{{ route('hospital.appointments') }}" class="btn btn-light" style="background:white;color:var(--primary-color);font-weight:600;">
                    <i class="fas fa-calendar"></i> Manage Appointments
                </a>
                <a href="{{ route('hospital.profile') }}" class="btn btn-outline-light" class="btn btn-light" style="background:white;color:var(--primary-color);font-weight:600;">
                    <i class="fas fa-cog"></i> Settings
                </a>
            </div>
            <div style="width:100%;max-width:420px;margin:2rem auto 0 auto;display:flex;align-items:center;justify-content:center;">
                <img src="{{ asset('images/hospital-hero.png') }}" alt="Hospital Dashboard Image" style="width:100%;height:auto;display:block;border-radius:1rem;box-shadow:0 2px 12px rgba(0,0,0,0.07);" />
            </div>
        </div>

        <!-- Stats Cards (White) -->
        <div class="dashboard-section section-white" style="background:white;padding:2rem 1.5rem;border-radius:1.2rem;margin-bottom:2rem;box-shadow:0 4px 24px 0 rgba(0,0,0,0.08);">
            <div class="stats-grid" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:1.5rem;">
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-calendar-check"></i></div>
                    <div class="stat-content">
                        <h3>{{ $stats['total_appointments'] }}</h3>
                        <p>Total Appointments</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-clock"></i></div>
                    <div class="stat-content">
                        <h3>{{ $stats['upcoming_appointments'] }}</h3>
                        <p>Upcoming Appointments</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-calendar-day"></i></div>
                    <div class="stat-content">
                        <h3>{{ $stats['today_appointments'] }}</h3>
                        <p>Today's Appointments</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-comments"></i></div>
                    <div class="stat-content">
                        <h3>{{ $stats['total_feedback'] }}</h3>
                        <p>Total Feedback</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Appointments (Blue, with texture) -->
        <div class="dashboard-section section-blue section-texture" style="padding:2rem 1.5rem 1.5rem 1.5rem; border-radius:1.2rem; margin-bottom:2rem; position:relative; color:white;">
            <div class="card-header" style="display:flex;align-items:center;justify-content:space-between;">
                <h3 style="margin:0;display:flex;align-items:center;gap:0.5rem;"><i class="fas fa-history"></i> Recent Appointments</h3>
                <a href="{{ route('hospital.appointments') }}" class="card-link" style="color:white;text-decoration:underline;">View All</a>
            </div>
            <div class="card-content">
                @if($recent_appointments->count() > 0)
                    <div class="appointments-list">
                        @foreach($recent_appointments as $appointment)
                            <div class="appointment-item" style="background:rgba(255,255,255,0.08);border-radius:0.7rem;padding:1rem;margin-bottom:1rem;display:flex;align-items:center;justify-content:space-between;">
                                <div class="appointment-info">
                                    <h4 style="margin:0 0 0.2rem 0;">{{ $appointment->user->full_name }}</h4>
                                    <p class="appointment-date" style="margin:0 0 0.2rem 0;">{{ $appointment->formatted_date }}</p>
                                    <p class="appointment-symptom" style="margin:0;">{{ Str::limit($appointment->symptom, 50) }}</p>
                                </div>
                                <div class="appointment-status">
                                    @if($appointment->isUpcoming())
                                        <span class="status-badge status-upcoming">Upcoming</span>
                                    @else
                                        <span class="status-badge status-past">Past</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="empty-state" style="text-align:center;color:white;">
                        <i class="fas fa-calendar-times" style="font-size:2rem;"></i>
                        <p>No appointments yet</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Main Content Grid -->
        <div class="dashboard-grid">
            <!-- Upcoming Appointments -->
            <div class="dashboard-card">
                <div class="card-header">
                    <h3>Upcoming Appointments</h3>
                    <a href="{{ route('hospital.appointments') }}" class="card-link">View All</a>
                </div>
                <div class="card-content">
                    @if($upcoming_appointments->count() > 0)
                        <div class="appointments-list">
                            @foreach($upcoming_appointments as $appointment)
                                <div class="appointment-item">
                                    <div class="appointment-info">
                                        <h4>{{ $appointment->user->full_name }}</h4>
                                        <p class="appointment-date">{{ $appointment->formatted_date }}</p>
                                        <p class="appointment-time">{{ $appointment->time_until }}</p>
                                    </div>
                                   
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-state">
                            <i class="fas fa-calendar-check"></i>
                            <p>No upcoming appointments</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Hospital Info -->
            <div class="dashboard-card">
                <div class="card-header">
                    <h3>Hospital Information</h3>
                    <a href="{{ route('hospital.profile') }}" class="card-link">Edit</a>
                </div>
                <div class="card-content hospital-info-flex">
                    <div class="hospital-info-text">
                        <h4>{{ $hospital->name }}</h4>
                        <p><i class="fas fa-map-marker-alt"></i> {{ $hospital->location }}</p>
                        <p><i class="fas fa-phone"></i> {{ $hospital->contact_info }}</p>
                        <div class="subscription-info">
                            <h5>Subscription Plan</h5>
                            <span class="plan-badge plan-{{ $hospital->getSubscriptionPlan() }}">
                                {{ ucfirst($hospital->getSubscriptionPlan()) }}
                            </span>
                        </div>
                    </div>
                    @if($hospital->profile_picture)
                        <img src="{{ asset('storage/' . $hospital->profile_picture) }}" alt="Profile Picture" class="dashboard-hospital-img">
                    @endif
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="dashboard-card">
                <div class="card-header">
                    <h3>Quick Actions</h3>
                </div>
                <div class="card-content">
                    <div class="quick-actions">
                        <a href="{{ route('hospital.appointments') }}" class="quick-action">
                            <i class="fas fa-calendar-alt"></i>
                            <span>Manage Appointments</span>
                        </a>
                        <a href="{{ route('hospital.profile') }}" class="quick-action">
                            <i class="fas fa-hospital"></i>
                            <span>Edit Hospital Profile</span>
                        </a>
                        <a href="{{ route('hospital.subscription') }}" class="quick-action">
                            <i class="fas fa-credit-card"></i>
                            <span>Manage Subscription</span>
                        </a>
                        <a href="{{ route('hospital.feedback') }}" class="quick-action">
                            <i class="fas fa-comments"></i>
                            <span>Share Feedback</span>
                        </a>
                    </div>
                </div>
            </div>
            <div class="dashboard-card">
                <div class="card-header">
                    <h3>Recent Activity</h3>
                </div>
                <div class="card-content">
                    @if(isset($recentActivities) && count($recentActivities) > 0)
                        <ul class="recent-activity-list">
                            @foreach($recentActivities as $activity)
                                <li class="recent-activity-item">
                                    <span class="activity-icon">
                                        @if($activity['type'] === 'appointment')
                                            <i class="fas fa-calendar-check" style="color:#0ea5e9;"></i>
                                        @elseif($activity['type'] === 'profile_update')
                                            <i class="fas fa-user-edit" style="color:#f59e42;"></i>
                                        @elseif($activity['type'] === 'subscription')
                                            <i class="fas fa-credit-card" style="color:#16a34a;"></i>
                                        @else
                                            <i class="fas fa-info-circle" style="color:#64748b;"></i>
                                        @endif
                                    </span>
                                    <span class="activity-text">
                                        {!! $activity['description'] !!}
                                    </span>
                                    <span class="activity-time">
                                        {{ \Carbon\Carbon::parse($activity['created_at'])->diffForHumans() }}
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <div style="color:#64748b; text-align:center; padding:1rem 0;">
                            No recent activity to display.
                        </div>
                    @endif
                </div>
            </div>
    </div>
</div>
@endsection

@section('styles')
<style>
.dashboard-container {
    padding: 2rem 0;
    background: linear-gradient(135deg, #f8fafc 0%, #e0e7ef 100%);
    min-height: 100vh;
}

.dashboard-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
}

.dashboard-title h1 {
    color: var(--dark-color);
    font-weight: 500;
    letter-spacing: 0.01em;
}

.dashboard-title p {
    color: #6b7280;
    font-size: 1.125rem;
    font-weight: 400;
}

.dashboard-actions {
    display: flex;
    gap: 1rem;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.stat-card {
    background: linear-gradient(135deg, #fff 80%, #f3f4f6 100%);
    box-shadow: 0 4px 24px 0 rgba(0, 0, 0, 0.08), 0 1.5px 4px 0 rgba(0,188,212,0.07);
    border-radius: 18px;
    border: 1.5px solid #e0e7ef;
    padding: 1.5rem 1.2rem;
    display: flex;
    align-items: center;
    gap: 1.2rem;
    position: relative;
    overflow: hidden;
}

.stat-icon {
    font-size: 2.2rem;
    color: var(--primary-color);
    background: #e0f2fe;
    border-radius: 50%;
    width: 48px;
    height: 48px;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 2px 8px 0 rgba(0,188,212,0.04);
}

.stat-content h3 {
    font-size: 1.5rem;
    font-weight: 500;
    color: var(--dark-color);
    margin-bottom: 0.2rem;
}

.stat-content p {
    color: #6b7280;
    font-size: 1rem;
    font-weight: 400;
}

.dashboard-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
    gap: 1.5rem;
}

.dashboard-card {
    background: white;
    border-radius: 0.5rem;
    box-shadow: var(--shadow);
    overflow: hidden;
}

.card-header {
    padding: 1.5rem;
    border-bottom: 1px solid var(--border-color);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.card-header h3 {
    margin: 0;
    color: var(--dark-color);
}

.card-link {
    color: var(--primary-color);
    text-decoration: none;
    font-weight: 500;
}

.card-link:hover {
    text-decoration: underline;
}

.card-content {
    padding: 1.5rem;
}

.appointments-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.appointment-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem;
    background: var(--light-color);
    border-radius: 0.375rem;
}

.appointment-info h4 {
    margin: 0 0 0.5rem 0;
    color: var(--dark-color);
}

.appointment-date {
    color: var(--primary-color);
    font-weight: 500;
    margin: 0 0 0.25rem 0;
}

.appointment-symptom,
.appointment-time {
    color: var(--gray-color);
    margin: 0;
    font-size: 0.875rem;
}

.appointment-status {
    display: flex;
    gap: 0.5rem;
}

.status-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 1rem;
    font-size: 0.75rem;
    font-weight: 500;
}

.status-upcoming {
    background: #dbeafe;
    color: #1e40af;
}

.status-past {
    background: #f3f4f6;
    color: #6b7280;
}

.appointment-actions {
    display: flex;
    gap: 0.5rem;
}

.btn-sm {
    padding: 0.5rem;
    font-size: 0.875rem;
}

.empty-state {
    text-align: center;
    padding: 2rem;
    color: var(--gray-color);
}

.empty-state i {
    font-size: 3rem;
    margin-bottom: 1rem;
    opacity: 0.5;
}

.hospital-info-flex {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 2rem;
}

.hospital-info-text {
    flex: 1;
}

.dashboard-hospital-img {
    max-width: 120px;
    max-height: 120px;
    border-radius: 12px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.08);
    object-fit: cover;
}

.subscription-info {
    margin-top: 1rem;
    padding-top: 1rem;
    border-top: 1px solid var(--border-color);
}

.subscription-info h5 {
    margin: 0 0 0.5rem 0;
    color: var(--dark-color);
}

.plan-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 1rem;
    font-size: 0.875rem;
    font-weight: 500;
}

.plan-basic {
    background: #dbeafe;
    color: #1e40af;
}

.plan-premium {
    background: #fef3c7;
    color: #d97706;
}

.plan-enterprise {
    background: #fce7f3;
    color: #be185d;
}

.quick-actions {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
}

.quick-action {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 1.5rem;
    background: var(--light-color);
    border-radius: 0.375rem;
    text-decoration: none;
    color: var(--dark-color);
    transition: all 0.3s ease;
}

.quick-action:hover {
    background: var(--primary-color);
    color: white;
    transform: translateY(-2px);
}

.quick-action i {
    font-size: 2rem;
    margin-bottom: 0.5rem;
}

.quick-action span {
    text-align: center;
    font-weight: 500;
}

@media (max-width: 768px) {
    .dashboard-header {
        flex-direction: column;
        gap: 1rem;
        text-align: center;
    }
    
    .dashboard-actions {
        flex-direction: column;
        width: 100%;
    }
    
    .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .dashboard-grid {
        grid-template-columns: 1fr;
    }
    
    .appointment-item {
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
    }
    
    .quick-actions {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 900px) {
    .hospital-info-flex {
        flex-direction: column;
        align-items: center;
    }
    .dashboard-hospital-img {
        margin-top: 1rem;
        max-width: 90vw;
    }
}

.section-blue {
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    color: white;
}
.section-white {
    background: #fff;
    color: var(--dark-color);
}
.section-texture {
    background-image: url('https://www.transparenttextures.com/patterns/cubes.png');
    background-blend-mode: lighten;
}
.btn-light {
    background: #fff;
    color: var(--primary-color);
    border: none;
    font-weight: 600;
    border-radius: 0.5rem;
    padding: 0.7rem 1.5rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.07);
    transition: background 0.2s, color 0.2s;
}
.btn-light:hover {
    background: var(--secondary-color);
    color: #fff;
}
.btn-outline-light {
    background: transparent;
    color: #fff;
    border: 2px solid #fff;
    font-weight: 600;
    border-radius: 0.5rem;
    padding: 0.7rem 1.5rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.07);
    transition: background 0.2s, color 0.2s;
}
.btn-outline-light:hover {
    background: #fff;
    color: var(--primary-color);
}
.stats-grid .stat-card {
    background: linear-gradient(135deg, #fff 80%, #f3f4f6 100%);
    box-shadow: 0 4px 24px 0 rgba(0, 0, 0, 0.08), 0 1.5px 4px 0 rgba(0,188,212,0.07);
    border-radius: 18px;
    border: 1.5px solid #e0e7ef;
    padding: 1.5rem 1.2rem;
    display: flex;
    align-items: center;
    gap: 1.2rem;
    position: relative;
    overflow: hidden;
}
.stat-icon {
    font-size: 2.2rem;
    color: var(--primary-color);
    background: #e0f2fe;
    border-radius: 50%;
    padding: 0.7rem;
    margin-right: 0.7rem;
}
</style>
@endsection

@section('scripts')
<script>
function markCompleted(appointmentId) {
    if (confirm('Mark this appointment as completed?')) {
        // AJAX call to mark appointment as completed
        CareConnect.ajaxRequest(`/hospital/appointments/${appointmentId}`, 'PUT', {
            status: 'completed'
        }).then(response => {
            CareConnect.showNotification('Appointment marked as completed', 'success');
            location.reload();
        }).catch(error => {
            CareConnect.showNotification('Error updating appointment', 'error');
        });
    }
}

</script>
@endsection 