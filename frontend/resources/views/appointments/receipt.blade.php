@extends('layouts.app')

@section('title', 'Appointment Receipt - CareConnect')

@section('content')
<div class="receipt-container">
    <div class="container">
        <!-- Header: Blue, with texture -->
        <div class="receipt-header section-blue section-texture" style="background:linear-gradient(135deg, var(--primary-color), var(--secondary-color));color:white;padding:2.5rem 1.5rem 2rem 1.5rem;border-radius:1.2rem;display:flex;justify-content:space-between;align-items:center;margin-bottom:2rem;">
            <div class="receipt-logo" style="display:flex;align-items:center;gap:0.5rem;font-size:1.5rem;font-weight:bold;">
                <img src="{{ asset('images/logo.png') }}" alt="CareConnect Logo" class="logo-image" style="height:40px;width:auto;">
                <span>CareConnect</span>
            </div>
            <div class="receipt-status" >
                <span style= "color:white; font-weight:bold" ><i class="fas fa-check-circle" ></i> Confirmed</span>
            </div>
        </div>
        <!-- Receipt Card: White, glassmorphism -->
        <div class="receipt-card section-white" style="background:rgba(255,255,255,0.85);backdrop-filter:blur(8px);border-radius:1.2rem;box-shadow:0 4px 24px 0 rgba(0,0,0,0.10);max-width:800px;margin:0 auto;overflow:hidden;">
            <div class="receipt-content" style="padding:2.5rem 2rem;">
                <div class="receipt-title" style="text-align:center;margin-bottom:2rem;">
                    <h1 style="color:var(--primary-color);margin-bottom:0.5rem;"><i class="fas fa-calendar-check"></i> Appointment Confirmation</h1>
                    <p style="color:#64748b;font-size:1.125rem;">Your appointment has been successfully booked</p>
                </div>
                <div class="receipt-details" style="margin-bottom:2rem;">
                    <div class="detail-section" style="margin-bottom:2rem;padding-bottom:1.5rem;border-bottom:1px solid #e5e7eb;">
                        <h3 style="color:var(--primary-color);margin-bottom:1rem;font-size:1.125rem;"><i class="fas fa-info-circle"></i> Appointment Information</h3>
                        <div class="detail-grid" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:1rem;">
                            <div class="detail-item" style="display:flex;justify-content:space-between;align-items:flex-start;gap:1rem;"><span class="detail-label" style="font-weight:500;color:#64748b;min-width:120px;">Appointment ID:</span><span class="detail-value" style="color:var(--dark-color);text-align:right;flex:1;">#{{ $appointment->appointment_id }}</span></div>
                            <div class="detail-item" style="display:flex;justify-content:space-between;align-items:flex-start;gap:1rem;"><span class="detail-label" style="font-weight:500;color:#64748b;min-width:120px;">Date:</span><span class="detail-value" style="color:var(--dark-color);text-align:right;flex:1;">{{ $appointment->formatted_date }}</span></div>
                            <div class="detail-item" style="display:flex;justify-content:space-between;align-items:flex-start;gap:1rem;"><span class="detail-label" style="font-weight:500;color:#64748b;min-width:120px;">Time:</span><span class="detail-value" style="color:var(--dark-color);text-align:right;flex:1;">{{ $appointment->formatted_time ?? 'TBD' }}</span></div>
                            <div class="detail-item" style="display:flex;justify-content:space-between;align-items:flex-start;gap:1rem;"><span class="detail-label" style="font-weight:500;color:#64748b;min-width:120px;">Hospital:</span><span class="detail-value" style="color:var(--dark-color);text-align:right;flex:1;">{{ $appointment->hospital->name }}</span></div>
                            <div class="detail-item" style="display:flex;justify-content:space-between;align-items:flex-start;gap:1rem;"><span class="detail-label" style="font-weight:500;color:#64748b;min-width:120px;">Location:</span><span class="detail-value" style="color:var(--dark-color);text-align:right;flex:1;">{{ $appointment->hospital->location }}</span></div>
                        </div>
                    </div>
                    <div class="detail-section" style="margin-bottom:2rem;padding-bottom:1.5rem;border-bottom:1px solid #e5e7eb;">
                        <h3 style="color:var(--primary-color);margin-bottom:1rem;font-size:1.125rem;"><i class="fas fa-user"></i> Patient Information</h3>
                        <div class="detail-grid" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:1rem;">
                            <div class="detail-item" style="display:flex;justify-content:space-between;align-items:flex-start;gap:1rem;"><span class="detail-label" style="font-weight:500;color:#64748b;min-width:120px;">Name:</span><span class="detail-value" style="color:var(--dark-color);text-align:right;flex:1;">{{ $appointment->user->full_name }}</span></div>
                            <div class="detail-item" style="display:flex;justify-content:space-between;align-items:flex-start;gap:1rem;"><span class="detail-label" style="font-weight:500;color:#64748b;min-width:120px;">Email:</span><span class="detail-value" style="color:var(--dark-color);text-align:right;flex:1;">{{ $appointment->user->email }}</span></div>
                            <div class="detail-item" style="display:flex;justify-content:space-between;align-items:flex-start;gap:1rem;"><span class="detail-label" style="font-weight:500;color:#64748b;min-width:120px;">Phone:</span><span class="detail-value" style="color:var(--dark-color);text-align:right;flex:1;">{{ $appointment->patient_phone ?? 'N/A' }}</span></div>
                        </div>
                    </div>
                    <div class="detail-section" style="margin-bottom:2rem;padding-bottom:1.5rem;border-bottom:1px solid #e5e7eb;">
                        <h3 style="color:var(--primary-color);margin-bottom:1rem;font-size:1.125rem;"><i class="fas fa-notes-medical"></i> Medical Information</h3>
                        <div class="detail-item full-width" style="flex-direction:column;align-items:flex-start;"><span class="detail-label" style="font-weight:500;color:#64748b;">Symptoms/Reason:</span><span class="detail-value" style="color:var(--dark-color);text-align:left;margin-top:0.5rem;padding:0.75rem;background:var(--light-color);border-radius:0.375rem;width:100%;">{{ $appointment->symptom }}</span></div>
                    </div>
                    <div class="detail-section" style="margin-bottom:2rem;">
                        <h3 style="color:var(--primary-color);margin-bottom:1rem;font-size:1.125rem;"><i class="fas fa-hospital"></i> Hospital Contact</h3>
                        <div class="detail-grid" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:1rem;">
                            <div class="detail-item" style="display:flex;justify-content:space-between;align-items:flex-start;gap:1rem;"><span class="detail-label" style="font-weight:500;color:#64748b;min-width:120px;">Contact Info:</span><span class="detail-value" style="color:var(--dark-color);text-align:right;flex:1;">{{ $appointment->hospital->contact_info }}</span></div>
                        </div>
                    </div>
                </div>
                <div class="receipt-actions" style="display:flex;gap:1rem;justify-content:center;margin-bottom:2rem;padding:1.5rem;background:var(--light-color);border-radius:0.5rem;">
                    <a href="{{ route('appointments.show', $appointment) }}" class="btn btn-primary" style="border-radius:2em;"><i class="fas fa-eye"></i> View Details</a>
                    <a href="{{ route('appointments') }}" class="btn btn-outline" style="border-radius:2em;"><i class="fas fa-list"></i> My Appointments</a>
                    <button onclick="window.print()" class="btn btn-outline" style="border-radius:2em;"><i class="fas fa-print"></i> Print Receipt</button>
                </div>
                <div class="receipt-footer" style="border-top:1px solid #e5e7eb;padding-top:1.5rem;">
                    <div class="important-notice" style="background:#fef3c7;border:1px solid #fbbf24;border-radius:0.375rem;padding:1rem;">
                        <h4 style="color:#d97706;margin:0 0 0.75rem 0;display:flex;align-items:center;gap:0.5rem;"><i class="fas fa-exclamation-triangle"></i> Important Information</h4>
                        <ul style="margin:0;padding-left:1.5rem;color:#92400e;">
                            <li>Please arrive 15 minutes before your scheduled appointment time</li>
                            <li>Bring a valid ID and any relevant medical records</li>
                            <li>The hospital will contact you to confirm the exact time</li>
                        </ul>
                    </div>
                </div>
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
.btn-primary {
    background: var(--primary-color);
    color: white;
    border: none;
    font-weight: 600;
    border-radius: 2em;
    padding: 0.7em 2em;
    box-shadow: 0 2px 8px rgba(0,0,0,0.07);
    transition: background 0.2s, color 0.2s;
}
.btn-primary:hover {
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
.status-badge {
    padding: 0.5rem 1rem;
    border-radius: 1rem;
    font-weight: 500;
    font-size: 0.875rem;
    background: rgba(255,255,255,0.2);
    color: var(--primary-color);
}
.status-confirmed {
    background: rgba(34,197,94,0.15);
    color: #16a34a;
}
@media (max-width: 900px) {
    .receipt-header, .receipt-card {
        padding: 1.2rem 0.5rem !important;
    }
}
@media (max-width: 700px) {
    .receipt-header, .receipt-card, .receipt-content {
        padding: 1.2rem 0.5rem !important;
    }
    .receipt-actions {
        flex-direction: column !important;
        gap: 0.7rem !important;
    }
}
</style>
@endsection 