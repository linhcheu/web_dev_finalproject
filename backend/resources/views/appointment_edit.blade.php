@extends('layouts.app')
@section('handles_flash', '1')
@section('handles_errors', '1')

@section('content')
    <style>
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(-20px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .main {
            flex-grow: 1;
            padding: 30px;
            background: var(--cc-bg);
            min-height: calc(100vh - 64px);
        }

        .form-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            max-width: 800px;
            margin: 0 auto;
            animation: fadeInUp 0.8s ease-out;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .appointment-info {
            background: #ffffff;
            padding: 25px;
            border-radius: 15px;
            margin-bottom: 30px;
            border: 1px solid #cbd5e1;
        }

        .appointment-info h4 {
            margin-bottom: 20px;
            color: #374151;
            font-size: 18px;
            font-weight: 600;
        }

        .appointment-info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }

        .info-item {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .info-label {
            font-size: 12px;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 600;
        }

        .info-value {
            font-size: 14px;
            color: #374151;
            font-weight: 500;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-scheduled {
            background: #dbeafe;
            color: #1e40af;
        }

        .status-completed {
            background: #d1fae5;
            color: #065f46;
        }

        .status-cancelled {
            background: #fee2e2;
            color: #991b1b;
        }

        .status-rescheduled {
            background: #fef3c7;
            color: #92400e;
        }

        .form-group {
            margin-bottom: 25px;
            animation: slideIn 0.6s ease-out;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #374151;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 15px 20px;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            font-size: 16px;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.8);
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: var(--cc-primary);
            box-shadow: 0 0 0 4px rgba(14, 116, 144, 0.1);
            transform: translateY(-2px);
            background: white;
        }

        .form-group textarea {
            resize: vertical;
            min-height: 100px;
        }

        .btn {
            padding: 15px 30px;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            text-align: center;
            margin-right: 15px;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .btn-primary {
            background: var(--cc-primary);
            color: white;
            box-shadow: 0 4px 15px rgba(14, 116, 144, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(14, 116, 144, 0.4);
        }

        .btn-secondary {
            background: #475569;
            color: white;
            box-shadow: 0 4px 15px rgba(107, 114, 128, 0.3);
        }

        .btn-secondary:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(107, 114, 128, 0.4);
        }

        .error {
            color: #dc2626;
            font-size: 13px;
            margin-top: 8px;
            font-weight: 500;
        }

        .error::before {
            content: '\u26a0';
            margin-right: 5px;
            font-size: 14px;
        }

        .success {
            color: #059669;
            font-size: 14px;
            margin-bottom: 25px;
            padding: 15px;
            background: #ecfdf5;
            border-radius: 12px;
            border: 1px solid #10b981;
            display: flex;
            align-items: center;
        }

        .success::before {
            content: '\u2713';
            margin-right: 10px;
            font-size: 18px;
            font-weight: bold;
        }

        .section-title {
            background: var(--cc-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
        }

        .page-title {
            background: var(--cc-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-size: 28px;
            font-weight: 800;
            margin-bottom: 30px;
            text-align: center;
        }

        .page-title::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 60px;
            height: 3px;
            background: var(--cc-primary);
            border-radius: 2px;
        }

        .basic-info-section {
            background: var(--cc-primary-soft);
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 25px;
            border: 1px solid #bae6fd;
            position: relative;
            overflow: hidden;
        }

        .basic-info-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: var(--cc-primary);
        }

        .status-section {
            background: #fef2f2;
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 25px;
            border: 1px solid #fca5a5;
            position: relative;
            overflow: hidden;
        }

        .status-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: var(--cc-danger);
        }

        .basic-info-section .section-title {
            background: var(--cc-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .status-section .section-title {
            background: var(--cc-danger);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .basic-info-section .section-title::before {
            content: '📅';
        }

        .status-section .section-title::before {
            content: '🔄';
        }

        .form-actions {
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px solid rgba(0, 0, 0, 0.1);
            text-align: right;
        }

        .section-form {
            margin-bottom: 0;
        }

        .section-form .form-group:last-child {
            margin-bottom: 0;
        }

        /* Responsive styles */
        @media (max-width: 1024px) {
            .main {
                padding: 20px;
            }
            
            .form-container {
                padding: 30px;
                max-width: 90%;
            }
            
            .appointment-info-grid {
                grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
                gap: 12px;
            }
        }
        
        @media (max-width: 768px) {
            .main {
                padding: 15px;
            }
            
            .form-container {
                padding: 25px;
                max-width: 95%;
            }
            
            .appointment-info {
                padding: 20px;
            }
            
            .appointment-info-grid {
                grid-template-columns: 1fr;
                gap: 10px;
            }
            
            .form-group {
                margin-bottom: 20px;
            }
            
            .form-group input,
            .form-group select,
            .form-group textarea {
                padding: 12px 16px;
                font-size: 14px;
            }
            
            .btn {
                padding: 12px 24px;
                font-size: 14px;
                margin-right: 10px;
                margin-bottom: 10px;
            }
            
            .info-value {
                word-break: break-word;
                overflow-wrap: break-word;
            }
        }
        
        @media (max-width: 480px) {
            .main {
                padding: 10px;
            }
            
            .form-container {
                padding: 20px;
                max-width: 100%;
            }
            
            .appointment-info {
                padding: 15px;
            }
            
            .appointment-info h4 {
                font-size: 16px;
                margin-bottom: 15px;
            }
            
            .form-group {
                margin-bottom: 15px;
            }
            
            .form-group label {
                font-size: 13px;
            }
            
            .form-group input,
            .form-group select,
            .form-group textarea {
                padding: 10px 12px;
                font-size: 13px;
            }
            
            .btn {
                padding: 10px 20px;
                font-size: 13px;
                margin-right: 8px;
                margin-bottom: 8px;
                width: 100%;
                display: block;
            }
            
            .info-label {
                font-size: 11px;
            }
            
            .info-value {
                font-size: 13px;
            }
        }
    </style>

    <div class="main px-2 sm:px-4 md:px-8">
        <div class="form-container">
            <h2 class="page-title">Edit Appointment</h2>

            @if(session('success'))
                <div class="success">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Appointment Information Display -->
            <div class="appointment-info">
                <h4>Current Appointment Information</h4>
                <div class="appointment-info-grid">
                    <div class="info-item">
                        <span class="info-label">Appointment ID</span>
                        <span class="info-value">{{ $appointment->appointment_id }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Patient</span>
                        <span class="info-value">{{ $appointment->user->first_name }} {{ $appointment->user->last_name }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Hospital</span>
                        <span class="info-value">{{ $appointment->hospital->name }}<br><small>{{ $appointment->hospital->province ?? 'N/A' }}</small></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Status</span>
                        <span class="status-badge status-{{ $appointment->status }}">
                            {{ ucfirst($appointment->status) }}
                        </span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Date</span>
                        <span class="info-value">{{ $appointment->appointment_date?->format('M j, Y') }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Time</span>
                        <span class="info-value">{{ $appointment->appointment_time }}</span>
                    </div>
                </div>
            </div>

            <!-- Basic Information Section -->
            <div class="basic-info-section">
                <h3 class="section-title">Appointment Details</h3>

                <form method="POST" action="{{ route('appointment.update', $appointment->appointment_id) }}" class="section-form">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="update_type" value="basic_info">

                    <div class="form-group">
                        <label for="user_id">Patient</label>
                        <select id="user_id" name="user_id" required>
                            <option value="">Select Patient</option>
                            @foreach($users as $user)
                                <option value="{{ $user->user_id }}" {{ old('user_id', $appointment->user_id) == $user->user_id ? 'selected' : '' }}>
                                    {{ $user->first_name }} {{ $user->last_name }} ({{ $user->email }})
                                </option>
                            @endforeach
                        </select>
                        @error('user_id')
                        <div class="error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="hospital_id">Hospital</label>
                        <select id="hospital_id" name="hospital_id" required>
                            <option value="">Select Hospital</option>
                            @foreach($hospitals as $hospital)
                                <option value="{{ $hospital->hospital_id }}" {{ old('hospital_id', $appointment->hospital_id) == $hospital->hospital_id ? 'selected' : '' }}>
                                    {{ $hospital->name }} - {{ $hospital->province ?? 'Unknown Province' }}
                                </option>
                            @endforeach
                        </select>
                        @error('hospital_id')
                        <div class="error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="appointment_date">Appointment Date</label>
                        <input type="date" id="appointment_date" name="appointment_date" value="{{ old('appointment_date', $appointment->appointment_date?->toDateString()) }}" required>
                        @error('appointment_date')
                        <div class="error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="appointment_time">Appointment Time</label>
                        <input type="time" id="appointment_time" name="appointment_time" value="{{ old('appointment_time', substr((string) $appointment->appointment_time, 0, 5)) }}" required>
                        @error('appointment_time')
                        <div class="error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="symptom">Symptoms</label>
                        <textarea id="symptom" name="symptom" maxlength="500" placeholder="Symptoms or reason for the visit">{{ old('symptom', $appointment->symptom) }}</textarea>
                        @error('symptom')
                        <div class="error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Update Appointment Details</button>
                    </div>
                </form>
            </div>

            <!-- Status Update Section -->
            <div class="status-section">
                <h3 class="section-title">Update Status</h3>

                <form method="POST" action="{{ route('appointment.update', $appointment->appointment_id) }}" class="section-form">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="update_type" value="status">

                    <div class="form-group">
                        <label for="status">Appointment Status</label>
                        <select id="status" name="status" required>
                            <option value="">Select Status</option>
                            <option value="upcoming" {{ old('status', $appointment->status) == 'upcoming' ? 'selected' : '' }}>Upcoming</option>
                            <option value="completed" {{ old('status', $appointment->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                        </select>
                        @error('status')
                        <div class="error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Update Status</button>
                    </div>
                </form>
            </div>

            <div class="flex justify-end space-x-4 mt-8">
                <a href="{{ route('appointment.index') }}" class="btn btn-secondary">Back to Appointments</a>
            </div>
        </div>
    </div>
@endsection 