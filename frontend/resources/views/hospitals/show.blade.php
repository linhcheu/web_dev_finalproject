@extends('layouts.app')

@section('title', $hospital->name . ' - CareConnect')

@section('content')
<div class="hospital-detail-container">
    <div class="container">
        <!-- Header: Blue, with texture -->
        <div class="hospital-header section-blue section-texture" style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:2rem;padding:2.5rem 1.5rem 2rem 1.5rem;border-radius:1.2rem;color:white;">
            <div class="hospital-info">
                <h1 style="color:var(--primary-color);margin-bottom:0.5rem;"><i class="fas fa-hospital-alt" style="margin-right:0.7rem;"></i>{{ $hospital->name }}</h1>
                <p class="hospital-location" style="color:black;"><i class="fas fa-map-marker-alt"></i> {{ $hospital->location }}</p>
                <p class="hospital-contact" style="color:black;"><i class="fas fa-phone"></i> {{ $hospital->contact_info }}</p>
                <p class="hospital-province" style="font-weight: bold; color:black;"><i class="fas fa-map"></i> {{ $hospital->province }}</p>
                <div class="hospital-status">
                    <span class="status-badge status-{{ $hospital->status }}" style="color:green;"><i class="fas fa-info-circle"></i> {{ ucfirst($hospital->status) }}</span>
                </div>
            </div>
            @if($hospital->profile_picture)
                <img src="{{ asset('storage/' . $hospital->profile_picture) }}" alt="Profile Picture" class="hospital-profile-img" style="width:180px;height:180px;border-radius:1rem;box-shadow:0 4px 24px rgba(0,0,0,0.12);object-fit:cover;background:#f3f4f6;">
            @endif
        </div>
        <!-- Content: White, glassmorphism -->
        <div class="hospital-content section-white" style="margin-bottom:2rem;background:rgba(255,255,255,0.85);backdrop-filter:blur(8px);border-radius:1.5rem;box-shadow:0 8px 32px 0 rgba(0,0,0,0.12);padding:2.5rem 2rem;">
            <div class="content-grid" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(400px,1fr));gap:2rem;">
                <!-- Hospital Information -->
                <div class="info-section" style="background:transparent;box-shadow:none;">
                    <h2 style="padding:1.5rem 0 0.5rem 0;margin:0;color:var(--primary-color);"><i class="fas fa-info-circle"></i> About This Hospital</h2>
                    <div class="info-card" style="background:rgba(255,255,255,0.7);border-radius:1rem;box-shadow:0 2px 8px rgba(0,0,0,0.07);padding:1.5rem;">
                        <p style="color:black;">{{ $hospital->information }}</p>
                    </div>
                </div>
                <!-- Hospital Details -->
                <div class="details-section" style="background:transparent;box-shadow:none;">
                    <h2 style="padding:1.5rem 0 0.5rem 0;margin:0;color:var(--primary-color);"><i class="fas fa-list"></i> Hospital Details</h2>
                    <div class="details-card" style="background:rgba(255,255,255,0.7);border-radius:1rem;box-shadow:0 2px 8px rgba(0,0,0,0.07);padding:1.5rem;">
                        <div class="detail-item" style="display:flex;justify-content:space-between;align-items:center;padding:0.75rem 0;border-bottom:1px solid #e5e7eb;">
                            <span class="detail-label" style="font-weight:500;color:#64748b;">Status:</span>
                            <span class="detail-value"><span class="status-badge status-{{ $hospital->status }}"><i class="fas fa-info-circle"></i> {{ ucfirst($hospital->status) }}</span></span>
                        </div>
                        <div class="detail-item" style="display:flex;justify-content:space-between;align-items:center;padding:0.75rem 0;border-bottom:1px solid #e5e7eb;">
                            <span class="detail-label" style="font-weight:500;color:#64748b;">Province:</span>
                            <span class="detail-value" style="color:black;">{{ $hospital->province }}</span>
                        </div>
                    </div>
                </div>
                <!-- Actions -->
                <div class="actions-section" style="background:transparent;box-shadow:none;">
                    <div class="actions-card" style="background:rgba(255,255,255,0.7);border-radius:1rem;box-shadow:0 2px 8px rgba(0,0,0,0.07);padding:1.5rem;">
                        @auth
                            @if(auth()->user()->role === 'user')
                                <a href="{{ route('appointments.create') }}?hospital={{ $hospital->hospital_id }}" class="btn btn-primary btn-large" style="width:100%;padding:1rem;font-size:1.125rem;margin-bottom:1rem;border-radius:2em;display:inline-flex;align-:center;gap:0.5rem;"><i class="fas fa-calendar-plus"></i> Book Appointment</a>
                            @else
                                <h3 class="action-note" style="color:#991b1b; font-weight: bold;">Only regular users can book appointments</h3>
                            @endif
                        @else
                            <a href="{{ route('login') }}" class="btn btn-primary btn-large" style="width:100%;padding:1rem;font-size:1.125rem;margin-bottom:1rem;border-radius:2em;display:inline-flex;align-items:center;gap:0.5rem;"><i class="fas fa-sign-in-alt"></i> Login to Book Appointment</a>
                        @endauth
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
@media (max-width: 900px) {
    .content-grid {
        grid-template-columns: 1fr;
    }
    .hospital-header {
        flex-direction: column;
        gap: 1.2rem;
        padding: 1.2rem 0.5rem !important;
    }
    .hospital-profile-img {
        width: 100%;
        height: 180px;
    }
}
</style>
@endsection 