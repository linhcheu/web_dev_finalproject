@extends('layouts.app')

@section('title', 'Manage Appointments - CareConnect')

@section('content')
<div class="hospital-appointments-container">
    <div class="container">
        <!-- Header: Blue, with texture -->
        <div class="appointments-header section-blue section-texture" style="text-align:center;margin-bottom:2rem;padding:2.5rem 1.5rem 2rem 1.5rem;border-radius:1.2rem; color:var(--primary-color);">
            <h1 style="margin-bottom:0.5rem; color:var(--primary-color--)"><i class="fas fa-calendar-alt" style="margin-right:0.7rem; color:var(--primary-color);"></i>Manage Appointments</h1>
            <p style="color:var(--primary-color--);font-size:1.125rem; ">View and manage all appointments for your hospital</p>
        </div>
        <!-- Table Section: White -->
        <div class="appointments-table-section section-white" style="background:white;border-radius:0.5rem;box-shadow:var(--shadow);padding:2rem;">
            <!-- Filter Section -->
            <div class="filter-section" style="margin-bottom:1.5rem;display:flex;gap:1rem;flex-wrap:wrap;align-items:center;">
                <!-- Filter by Phone Number -->
                <form method="GET" action="" style="display:flex;gap:0.5rem;align-items:center;">
                    <input type="text" name="phone" value="{{ request('phone') }}" placeholder="Filter by phone number" class="form-control" style="padding:0.6rem 1rem;border-radius:0.5rem;border:1.5px solid #e5e7eb;min-width:200px;">
                    @if(request('status'))
                        <input type="hidden" name="status" value="{{ request('status') }}">
                    @endif
                    <button type="submit" class="btn btn-primary" style="padding:0.6rem 1.2rem;border-radius:0.5rem;font-size:1rem;"><i class="fas fa-search"></i> Filter</button>
                </form>
                
                <!-- Filter by Status -->
                <form method="GET" action="" style="display:flex;gap:0.5rem;align-items:center;">
                    <select name="status" class="form-control" style="padding:0.6rem 1rem;border-radius:0.5rem;border:1.5px solid #e5e7eb;min-width:150px;">
                        <option value="">All Status</option>
                        <option value="upcoming" @if(request('status') == 'upcoming') selected @endif>Upcoming</option>
                        <option value="completed" @if(request('status') == 'completed') selected @endif>Completed</option>
                    </select>
                    @if(request('phone'))
                        <input type="hidden" name="phone" value="{{ request('phone') }}">
                    @endif
                    <button type="submit" class="btn btn-primary" style="padding:0.6rem 1.2rem;border-radius:0.5rem;font-size:1rem;"><i class="fas fa-filter"></i> Filter</button>
                </form>
                
                <!-- Clear All Filters -->
                @if(request('phone') || request('status'))
                <a href="{{ route('hospital.appointments') }}" class="btn btn-outline" style="padding:0.6rem 1.2rem;border-radius:0.5rem;font-size:1rem;background:#fff;border:1.5px solid #e5e7eb;"><i class="fas fa-times"></i> Clear All</a>
                @endif
            </div>
            <div class="table-responsive">
                <table class="appointments-table">
                    <thead>
                        <tr>
                            <th><i class="fas fa-user"></i> Patient Name</th>
                            <th><i class="fas fa-envelope"></i> Email</th>
                            <th><i class="fas fa-phone"></i> Phone</th>
                            <th><i class="fas fa-calendar-day"></i> Date</th>
                            <th><i class="fas fa-clock"></i> Time</th>
                            <th><i class="fas fa-info-circle"></i> Status</th>
                            <th><i class="fas fa-cogs"></i> Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($appointments as $appointment)
                            <tr>
                                <td>{{ $appointment->user->first_name }} {{ $appointment->user->last_name }}</td>
                                <td>{{ $appointment->user->email }}</td>
                                <td>{{ $appointment->patient_phone }}</td>
                                <td>{{ $appointment->appointment_date }}</td>
                                <td>{{ $appointment->appointment_time }}</td>
                                <td>
                                    <span class="status-badge status-{{ $appointment->status }}">
                                        {{ ucfirst($appointment->status) }}
                                    </span>
                                </td>
                                <td>
                                    <form method="POST" action="{{ route('hospital.appointments.update', $appointment->appointment_id) }}">
                                        @csrf
                                        @method('PUT')
                                        <select name="status" class="form-control form-control-sm">
                                            <option value="upcoming" @if($appointment->status == 'upcoming') selected @endif>Upcoming</option>
                                            <option value="completed" @if($appointment->status == 'completed') selected @endif>Completed</option>
                                        </select>
                                        <button type="submit" class="btn btn-primary btn-sm mt-1"><i class="fas fa-save"></i> Update</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">No appointments found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
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
.hospital-appointments-container {
    padding: 2rem 0;
    background-color: var(--light-color);
    min-height: 100vh;
}
.appointments-header h1 {
    color: #fff;
    margin-bottom: 0.5rem;
    font-size: 2rem;
    font-weight: 700;
}
.appointments-header p {
    color: #e0e7ef;
    font-size: 1.125rem;
}
.appointments-table-section {
    background: white;
    border-radius: 0.5rem;
    box-shadow: var(--shadow);
    padding: 2rem;
}
.table-responsive {
    overflow-x: auto;
}
.appointments-table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 0;
}
.appointments-table th, .appointments-table td {
    padding: 0.75rem 1rem;
    border-bottom: 1px solid var(--border-color);
    text-align: left;
    font-size: 0.95rem;
}
.appointments-table th {
    background: var(--light-color);
    color: var(--dark-color);
    font-weight: 600;
}
.status-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 1rem;
    font-size: 0.85rem;
    font-weight: 500;
    display: inline-block;
}
.status-upcoming {
    background: #dbeafe;
    color: #1e40af;
}
.status-completed {
    background: #d1fae5;
    color: #065f46;
}
.btn-sm {
    padding: 0.25rem 0.75rem;
    font-size: 0.85rem;
    border-radius: 0.375rem;
}
.mt-1 {
    margin-top: 0.5rem;
}
.text-center {
    text-align: center;
}
@media (max-width: 768px) {
    .appointments-table-section {
        padding: 1rem;
    }
    .appointments-table th, .appointments-table td {
        padding: 0.5rem 0.5rem;
        font-size: 0.85rem;
    }
    .filter-section {
        flex-direction: column;
        align-items: stretch;
    }
    .filter-section form {
        flex-direction: column;
        gap: 0.5rem;
    }
    .filter-section .form-control {
        min-width: auto;
    }
}
</style>
@endsection