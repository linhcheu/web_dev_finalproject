@extends('layouts.app')

@section('title', 'Hospitals - CareConnect')

@section('content')
<div class="hospitals-container">
    <div class="container">
        <!-- Header: Blue, with texture -->
        <div class="hospitals-header section-blue section-texture" style="text-align:center;margin-bottom:2rem;padding:2.5rem 1.5rem 2rem 1.5rem;border-radius:1.2rem;color:white;">
            <h1 style="color:var(--primary-color);margin-bottom:0.5rem;"><i class="fas fa-hospital-alt" style="margin-right:0.7rem;"></i>Find Hospitals</h1>
            <p style="color:black;font-size:1.125rem;">Discover trusted healthcare facilities in your area</p>
        </div>
        <form method="GET" action="{{ route('hospitals.index') }}" class="mb-4" style="max-width: 400px; margin: 0 auto 2rem auto;">
            <div class="form-group" style="text-align:center;">
                <label for="province" style="font-weight: bold;"><i class="fas fa-map"></i> Filter by Province / City</label>
                <select name="province" id="province" class="form-control" onchange="this.form.submit()" style="margin: 0 auto; width: 100%; max-width: 300px; padding: 10px 12px; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 14px; transition: all 0.3s ease; background: white;">
                    <option value="">All Provinces</option>
                    @foreach($provinces as $province)
                        <option value="{{ $province }}" {{ request('province') == $province ? 'selected' : '' }}>{{ $province }}</option>
                    @endforeach
                </select>
            </div>
        </form>
        @if($hospitals->count() > 0)
            <div class="hospitals-list grid-3">
                @foreach($hospitals as $hospital)
                    <div class="hospital-card hospital-flex-card section-white" style="background:rgba(255,255,255,0.85);backdrop-filter:blur(8px);border-radius:1.5rem;box-shadow:0 8px 32px 0 rgba(0,0,0,0.12);padding:2.5rem 2rem;display:flex;flex-direction:column;justify-content:space-between;align-items:flex-start;gap:2.5rem;">
                        <div class="hospital-content">
                            <h3 class="hospital-name" style="color:var(--primary-color);font-size:1.5rem;font-weight:700;margin-bottom:1rem;"><i class="fas fa-hospital"></i> {{ $hospital->name }}</h3>
                            <p class="hospital-location" style="color:black;"><i class="fas fa-map-marker-alt"></i> {{ $hospital->location }}</p>
                            <p class="hospital-province" style="font-weight: bold; color:black;"><i class="fas fa-map"></i> {{ $hospital->province }}</p>
                            <p class="hospital-contact" style="color:black;"><i class="fas fa-phone"></i> {{ $hospital->contact_info }}</p>
                            <p class="hospital-description" style="color:black;">{{ Str::limit($hospital->information, 150) }}</p>
                            <div class="hospital-actions" style="margin-top:1.5rem;display:flex;gap:1rem;flex-wrap:wrap;">
                                <a href="{{ route('hospitals.show', $hospital) }}" class="btn btn-primary" style="padding:0.75rem 1.5rem;border-radius:2em;display:inline-flex;align-items:center;gap:0.5rem;"><i class="fas fa-eye"></i> View Details</a>
                                @auth
                                    @if(auth()->user()->role === 'user')
                                        <a href="{{ route('appointments.create') }}?hospital={{ $hospital->hospital_id }}" class="btn btn-outline" style="padding:0.75rem 1.5rem;border-radius:2em;display:inline-flex;align-items:center;gap:0.5rem;"><i class="fas fa-calendar-plus"></i> Book Appointment</a>
                                    @endif
                                @else
                                    <a href="{{ route('login') }}" class="btn btn-outline" style="padding:0.75rem 1.5rem;border-radius:2em;display:inline-flex;align-items:center;gap:0.5rem;"><i class="fas fa-sign-in-alt"></i> Login to Book</a>
                                @endauth
                            </div>
                        </div>
                        <div class="hospital-image-section">
                            @if($hospital->profile_picture)
                                <img src="{{ asset('storage/' . $hospital->profile_picture) }}" alt="Hospital Profile Picture" class="hospital-list-img" style="width:240px;height:180px;border-radius:1rem;box-shadow:0 4px 24px rgba(0,0,0,0.12);object-fit:cover;background:#f3f4f6;">
                            @else
                                <div class="hospital-icon" style="width:240px;height:180px;background:var(--primary-color);border-radius:1rem;display:flex;align-items:center;justify-content:center;color:white;font-size:4rem;box-shadow:0 4px 24px rgba(0,0,0,0.12);">
                                    <i class="fas fa-hospital"></i>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="empty-state card section-blue section-texture" style="text-align:center;padding:2.5rem 1.5rem;border-radius:1.2rem;color:white;">
                <div class="empty-icon" style="font-size:3rem;margin-bottom:1rem;"><i class="fas fa-hospital"></i></div>
                <h3 style="color:var(--primary-color);">No Hospitals Found</h3>
                <p style="color:black;">There are currently no active hospitals in our system. Please check back later.</p>
            </div>
        @endif
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
    .hospitals-list {
        flex-direction: column;
        gap: 1.2rem;
    }
    .hospital-flex-card {
        flex-direction: column;
        gap: 1.2rem;
        padding: 1.2rem 0.5rem !important;
    }
    .hospital-image-section {
        min-width: 100%;
        max-width: 100%;
        justify-content: center;
    }
    .hospital-list-img, .hospital-icon {
        width: 100%;
        height: 180px;
    }
    .hospital-content {
        width: 100%;
    }
    .hospital-name {
        font-size: 1.3rem !important;
        word-break: break-word;
        overflow-wrap: break-word;
    }
    .hospital-description {
        word-break: break-word;
        overflow-wrap: break-word;
    }
    .hospital-actions {
        flex-direction: column;
        align-items: stretch;
    }
    .btn {
        justify-content: center;
        text-align: center;
    }
}

@media (max-width: 600px) {
    .hospitals-header {
        padding: 1.5rem 1rem !important;
        margin-bottom: 1.5rem !important;
    }
    .hospitals-header h1 {
        font-size: 1.5rem !important;
    }
    .hospitals-header p {
        font-size: 1rem !important;
    }
    .hospital-flex-card {
        padding: 1rem 0.75rem !important;
        gap: 1rem;
    }
    .hospital-name {
        font-size: 1.2rem !important;
    }
    .hospital-description {
        font-size: 0.9rem;
    }
    .hospital-actions {
        gap: 0.75rem;
    }
    .btn {
        padding: 0.6rem 1.2rem !important;
        font-size: 0.9rem;
    }
    .form-group {
        max-width: 100% !important;
        padding: 0 1rem;
    }
    .form-control {
        max-width: 100% !important;
        font-size: 13px !important;
        padding: 8px 10px !important;
    }
}

@media (max-width: 480px) {
    .hospitals-header {
        padding: 1rem 0.75rem !important;
        margin-bottom: 1rem !important;
    }
    .hospitals-header h1 {
        font-size: 1.3rem !important;
    }
    .hospitals-header p {
        font-size: 0.9rem !important;
    }
    .hospital-flex-card {
        padding: 0.75rem 0.5rem !important;
        gap: 0.75rem;
    }
    .hospital-name {
        font-size: 1.1rem !important;
    }
    .hospital-description {
        font-size: 0.85rem;
    }
    .hospital-actions {
        gap: 0.5rem;
    }
    .btn {
        padding: 0.5rem 1rem !important;
        font-size: 0.85rem;
    }
    .hospital-list-img, .hospital-icon {
        height: 150px;
    }
    .form-group {
        padding: 0 0.5rem;
    }
    .form-control {
        max-width: 100% !important;
        font-size: 12px !important;
        padding: 6px 8px !important;
    }
}

/* Enhanced Pagination Styling - Compact Right Position */
.pagination-container {
    margin-top: 0;
    margin-bottom: 1rem;
    display: flex;
    justify-content: flex-end;
}

.pagination-wrapper {
    background: rgba(255, 255, 255, 0.9);
    backdrop-filter: blur(10px);
    border-radius: 0.5rem;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    padding: 0.5rem 0.75rem;
    border: 1px solid rgba(0, 0, 0, 0.05);
    transition: all 0.2s ease;
    display: inline-block;
}

.pagination-wrapper:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    transform: translateY(-1px);
}

.pagination-nav {
    display: flex;
    align-items: center;
    gap: 0.25rem;
    flex-wrap: wrap;
    justify-content: center;
}

/* Laravel Pagination Styling - Compact */
.pagination {
    display: flex;
    align-items: center;
    gap: 0.125rem;
    list-style: none;
    margin: 0;
    padding: 0;
    flex-wrap: wrap;
    justify-content: center;
}

.pagination li {
    margin: 0;
    padding: 0;
}

.pagination li a,
.pagination li span {
    display: flex;
    align-items: center;
    justify-content: center;
    min-width: 1.5rem;
    height: 1.5rem;
    padding: 0.25rem 0.5rem;
    border-radius: 0.25rem;
    text-decoration: none;
    font-weight: 500;
    font-size: 0.75rem;
    transition: all 0.2s ease;
    border: 1px solid #e5e7eb;
    background: #ffffff;
    color: #6b7280;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
}

.pagination li a:hover {
    background: var(--primary-color);
    color: white;
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    border-color: var(--primary-color);
}

/* Active page styling */
.pagination li .bg-blue-50,
.pagination li .bg-blue-500,
.pagination li .bg-blue-600,
.pagination li .bg-blue-700,
.pagination li .bg-blue-800,
.pagination li .bg-blue-900,
.pagination li .bg-indigo-50,
.pagination li .bg-indigo-500,
.pagination li .bg-indigo-600,
.pagination li .bg-indigo-700,
.pagination li .bg-indigo-800,
.pagination li .bg-indigo-900 {
    background: var(--primary-color) !important;
    color: white !important;
    border-color: var(--primary-color) !important;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1) !important;
    transform: translateY(-1px);
}

/* Disabled page styling */
.pagination li .text-gray-500,
.pagination li .text-gray-400,
.pagination li .text-gray-300,
.pagination li .text-gray-200,
.pagination li .text-gray-100,
.pagination li .text-gray-600,
.pagination li .text-gray-700,
.pagination li .text-gray-800,
.pagination li .text-gray-900 {
    background: #f9fafb !important;
    color: #d1d5db !important;
    cursor: not-allowed;
    transform: none !important;
    box-shadow: none !important;
    border-color: #f3f4f6 !important;
}

.pagination li .text-gray-500:hover,
.pagination li .text-gray-400:hover,
.pagination li .text-gray-300:hover,
.pagination li .text-gray-200:hover,
.pagination li .text-gray-100:hover,
.pagination li .text-gray-600:hover,
.pagination li .text-gray-700:hover,
.pagination li .text-gray-800:hover,
.pagination li .text-gray-900:hover {
    background: #f9fafb !important;
    color: #d1d5db !important;
    transform: none !important;
    box-shadow: none !important;
}

/* Override any Tailwind classes that might interfere */
.pagination li a[class*="bg-"],
.pagination li span[class*="bg-"] {
    background: #ffffff !important;
    color: #6b7280 !important;
}

.pagination li a[class*="text-"],
.pagination li span[class*="text-"] {
    color: #6b7280 !important;
}

/* Previous/Next Buttons - Compact */
.pagination li:first-child a,
.pagination li:last-child a {
    padding: 0.25rem 0.5rem;
    min-width: auto;
    font-weight: 500;
    background: #f3f4f6;
    color: #6b7280;
    border-color: #e5e7eb;
    font-size: 0.7rem;
}

.pagination li:first-child a:hover,
.pagination li:last-child a:hover {
    background: var(--primary-color);
    color: white;
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    border-color: var(--primary-color);
}

/* Responsive Design - Compact */
@media (max-width: 768px) {
    .pagination-wrapper {
        padding: 0.4rem 0.6rem;
        border-radius: 0.4rem;
    }
    
    .pagination {
        gap: 0.1rem;
    }
    
    .pagination li a,
    .pagination li span {
        min-width: 1.25rem;
        height: 1.25rem;
        padding: 0.2rem 0.4rem;
        font-size: 0.65rem;
        border-radius: 0.2rem;
    }
    
    .pagination li:first-child a,
    .pagination li:last-child a {
        padding: 0.2rem 0.4rem;
        font-size: 0.6rem;
    }
}

@media (max-width: 480px) {
    .pagination-wrapper {
        padding: 0.3rem 0.5rem;
    }
    
    .pagination {
        gap: 0.075rem;
    }
    
    .pagination li a,
    .pagination li span {
        min-width: 1.125rem;
        height: 1.125rem;
        padding: 0.15rem 0.3rem;
        font-size: 0.6rem;
        border-radius: 0.15rem;
    }
    
    .pagination li:first-child a,
    .pagination li:last-child a {
        padding: 0.15rem 0.3rem;
        font-size: 0.55rem;
    }
}

.grid-3 {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 2rem;
}
@media (max-width: 1100px) {
    .grid-3 {
        grid-template-columns: repeat(2, 1fr);
    }
}
@media (max-width: 700px) {
    .grid-3 {
        grid-template-columns: 1fr;
    }
}
</style>
@endsection 