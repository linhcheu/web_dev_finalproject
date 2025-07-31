@extends('layouts.app')

@section('title', 'Book Appointment - CareConnect')

@section('content')
<div class="appointment-container">
    <div class="container">
        <!-- Header: Blue, with texture -->
        <div class="appointment-header section-blue section-texture" style="text-align:center;margin-bottom:2rem;padding:2.5rem 1.5rem 2rem 1.5rem;border-radius:1.2rem;color:black;">
            <h1 style="margin-bottom:0.5rem;"><i class="fas fa-calendar-plus" style="margin-right:0.7rem;"></i>Book an Appointment</h1>
            <p style="color:black;font-size:1.125rem;">Schedule your appointment with a trusted hospital</p>
        </div>
        <div class="appointment-form-container" style="display:flex;justify-content:center;align-items:center;gap:2rem;max-width:1200px;margin:0 auto;">
            <!-- Form Card: White, glassmorphism -->
            <div class="appointment-form-card section-white" style="background:rgba(255,255,255,0.85);backdrop-filter:blur(8px);border-radius:1.2rem;box-shadow:0 4px 24px 0 rgba(0,0,0,0.10);padding:2.5rem 2rem;min-width:340px;max-width:500px;width:100%;margin:0 auto;">
                <div class="form-header" style="text-align:center;margin-bottom:2rem;">
                    <h2 style="color:var(--primary-color);margin-bottom:0.5rem;"><i class="fas fa-info-circle"></i> Appointment Details</h2>
                    <p style="color:#64748b;">Please fill in the details below to book your appointment</p>
                </div>
                @if ($errors->any())
                    <div class="alert alert-danger" style="padding:0.7rem;border-radius:0.5rem;margin-bottom:1rem;background:#fef2f2;border:1px solid #fecaca;color:#dc2626;box-shadow:0 2px 8px rgba(239,68,68,0.07);">
                        <ul style="margin:0;padding-left:1.2rem;">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <form method="POST" action="{{ route('appointments.store') }}" class="appointment-form">
                    @csrf
                    <div class="form-group" style="margin-bottom:1.2rem;">
                        <label for="hospital_id" class="form-label" style="font-weight:600;"><i class="fas fa-hospital"></i> Select Hospital</label>
                        <select id="hospital_id" name="hospital_id" class="form-select" required style="width:100%;padding:0.7rem 1rem;border:1.5px solid #e5e7eb;border-radius:0.7rem;font-size:1rem;box-shadow:0 1.5px 6px 0 rgba(0,0,0,0.03);">
                            <option value="">Choose a hospital</option>
                            @foreach($hospitals as $hospital)
                                <option value="{{ $hospital->hospital_id }}" {{ old('hospital_id') == $hospital->hospital_id ? 'selected' : '' }}>
                                    {{ $hospital->name }} - {{ $hospital->location }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group" style="margin-bottom:1.2rem;">
                        <label for="patient_phone" class="form-label" style="font-weight:600;"><i class="fas fa-phone"></i> Your Phone Number</label>
                        <input type="tel" id="patient_phone" name="patient_phone" class="form-control" value="{{ old('patient_phone') }}" required pattern="[0-9\-\+\s]{8,20}" maxlength="20" placeholder="Enter your phone number" style="width:100%;padding:0.7rem 1rem;border:1.5px solid #e5e7eb;border-radius:0.7rem;font-size:1rem;box-shadow:0 1.5px 6px 0 rgba(0,0,0,0.03);">
                        <small class="form-text" style="color:#64748b;">We'll use this to contact you about your appointment.</small>
                    </div>
                    <div class="form-group" style="margin-bottom:1.2rem;">
                        <label for="appointment_date" class="form-label" style="font-weight:600;"><i class="fas fa-calendar-day"></i> Appointment Date</label>
                        <input type="date" id="appointment_date" name="appointment_date" class="form-control" value="{{ old('appointment_date') }}" required style="width:100%;padding:0.7rem 1rem;border:1.5px solid #e5e7eb;border-radius:0.7rem;font-size:1rem;box-shadow:0 1.5px 6px 0 rgba(0,0,0,0.03);">
                        <small class="form-text" style="color:#64748b;">Please select a date at least 24 hours in advance</small>
                    </div>
                    <div class="form-group" style="margin-bottom:1.2rem;">
                        <label for="appointment_time" class="form-label" style="font-weight:600;"><i class="fas fa-clock"></i> Preferred Time</label>
                        <select id="appointment_time" name="appointment_time" class="form-select" required style="width:100%;padding:0.7rem 1rem;border:1.5px solid #e5e7eb;border-radius:0.7rem;font-size:1rem;box-shadow:0 1.5px 6px 0 rgba(0,0,0,0.03);">
                            <option value="">Select a time</option>
                            <option value="09:00" {{ old('appointment_time') == '09:00' ? 'selected' : '' }}>9:00 AM</option>
                            <option value="09:30" {{ old('appointment_time') == '09:30' ? 'selected' : '' }}>9:30 AM</option>
                            <option value="10:00" {{ old('appointment_time') == '10:00' ? 'selected' : '' }}>10:00 AM</option>
                            <option value="10:30" {{ old('appointment_time') == '10:30' ? 'selected' : '' }}>10:30 AM</option>
                            <option value="11:00" {{ old('appointment_time') == '11:00' ? 'selected' : '' }}>11:00 AM</option>
                            <option value="11:30" {{ old('appointment_time') == '11:30' ? 'selected' : '' }}>11:30 AM</option>
                            <option value="12:00" {{ old('appointment_time') == '12:00' ? 'selected' : '' }}>12:00 PM</option>
                            <option value="12:30" {{ old('appointment_time') == '12:30' ? 'selected' : '' }}>12:30 PM</option>
                            <option value="13:00" {{ old('appointment_time') == '13:00' ? 'selected' : '' }}>1:00 PM</option>
                            <option value="13:30" {{ old('appointment_time') == '13:30' ? 'selected' : '' }}>1:30 PM</option>
                            <option value="14:00" {{ old('appointment_time') == '14:00' ? 'selected' : '' }}>2:00 PM</option>
                            <option value="14:30" {{ old('appointment_time') == '14:30' ? 'selected' : '' }}>2:30 PM</option>
                            <option value="15:00" {{ old('appointment_time') == '15:00' ? 'selected' : '' }}>3:00 PM</option>
                            <option value="15:30" {{ old('appointment_time') == '15:30' ? 'selected' : '' }}>3:30 PM</option>
                            <option value="16:00" {{ old('appointment_time') == '16:00' ? 'selected' : '' }}>4:00 PM</option>
                            <option value="16:30" {{ old('appointment_time') == '16:30' ? 'selected' : '' }}>4:30 PM</option>
                            <option value="17:00" {{ old('appointment_time') == '17:00' ? 'selected' : '' }}>5:00 PM</option>
                        </select>
                        <small class="form-text" style="color:#64748b;">Please select your preferred appointment time. The hospital will confirm the exact time.</small>
                    </div>
                    <div class="form-group" style="margin-bottom:1.2rem;">
                        <label for="symptom" class="form-label" style="font-weight:600;"><i class="fas fa-notes-medical"></i> Symptoms/Reason for Visit</label>
                        <textarea id="symptom" name="symptom" class="form-control" rows="4" placeholder="Please describe your symptoms or reason for the appointment..." required style="width:100%;padding:0.7rem 1rem;border:1.5px solid #e5e7eb;border-radius:0.7rem;font-size:1rem;box-shadow:0 1.5px 6px 0 rgba(0,0,0,0.03);resize:vertical;">{{ old('symptom') }}</textarea>
                        <small class="form-text" style="color:#64748b;">Provide detailed information to help the hospital prepare for your visit</small>
                    </div>
                    <div class="form-actions" style="display:flex;gap:1.2rem;justify-content:center;align-items:center;margin-top:2rem;">
                        <button type="submit" class="btn btn-primary" style="font-size:1.1rem;padding:0.7em 2.2em;border-radius:2em;box-shadow:0 2px 8px rgba(37,99,235,0.07);font-weight:600;transition:transform 0.12s;"><i class="fas fa-calendar-check"></i> Book Appointment</button>
                    </div>
                </form>
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
.appointment-form-card input:focus, .appointment-form-card select:focus, .appointment-form-card textarea:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(37,99,235,0.10);
    outline: none;
    background: rgba(237,242,255,0.25);
    transition: box-shadow 0.2s, border-color 0.2s;
}
.form-actions button:hover, .form-actions button:focus, .form-actions a:hover, .form-actions a:focus {
    transform: scale(1.04);
    box-shadow: 0 4px 16px 0 rgba(37,99,235,0.13);
}
@media (max-width: 900px) {
    .appointment-form-container {
        grid-template-columns: 1fr;
    }
}
@media (max-width: 700px) {
    .appointment-header, .appointment-form-card, .appointment-info-card {
        padding: 1.2rem 0.5rem !important;
    }
    .form-actions {
        flex-direction: column !important;
        gap: 0.7rem !important;
    }
}
</style>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Set minimum date to tomorrow
    const appointmentDate = document.getElementById('appointment_date');
    const tomorrow = new Date();
    tomorrow.setDate(tomorrow.getDate() + 1);
    appointmentDate.setAttribute('min', tomorrow.toISOString().split('T')[0]);
    
    // Hospital selection change
    const hospitalSelect = document.getElementById('hospital_id');
    hospitalSelect.addEventListener('change', function() {
        if (this.value) {
            // You can add AJAX call here to get hospital details
            console.log('Selected hospital:', this.value);
        }
    });
});
</script>
@endsection 