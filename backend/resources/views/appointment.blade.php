@extends('layouts.app')
@section('content')
    <style>
        /* Unified base styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        .main {
            flex-grow: 1;
            padding: 30px;
            background: #f3f4f6;
            min-height: 100vh;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background-color: white;
            padding: 20px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            gap: 20px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
            flex-wrap: wrap;
        }
        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }
        .stat-icon.appointments { background-color: #dcfce7; color: #16a34a; }
        .stat-info h3 {
            color: #6b7280;
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 5px;
        }
        .stat-number {
            font-size: 28px;
            font-weight: 700;
            color: #111827;
        }
        .activity-section {
            background-color: white;
            padding: 20px;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
            overflow-x: auto;
        }
        .activity-section h2 {
            font-size: 18px;
            font-weight: 600;
            color: #111827;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #e5e7eb;
        }
        .overflow-x-auto {
            width: 100%;
            overflow-x: auto;
            box-sizing: border-box;
        }
        table {
            width: 100%;
            min-width: 400px;
            border-collapse: collapse;
        }
        th, td {
            padding: 12px 16px;
            text-align: left;
            font-size: 14px;
            word-break: break-word;
        }
        th {
            background-color: #f4f6fa;
            font-weight: 600;
        }
        .action-buttons {
            display: flex;
            gap: 8px;
            align-items: center;
            flex-wrap: wrap;
        }
        .action-btn {
            padding: 6px 12px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 12px;
            transition: all 0.3s ease;
            min-width: 32px;
            height: 32px;
        }
        .action-btn-edit {
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
            color: white;
        }
        .action-btn-edit:hover {
            background: #2563eb;
        }
        .action-btn-delete {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white;
        }
        .action-btn-delete:hover {
            background: #dc2626;
        }
        .action-btn svg {
            width: 16px;
            height: 16px;
        }
        .pagination {
            text-align: center;
            padding: 20px 0 0;
        }
        .pagination button {
            margin: 0 4px;
            padding: 6px 10px;
            border-radius: 4px;
            border: 1px solid #ddd;
            background: #fff;
            cursor: pointer;
        }
        .pagination .active {
            background-color: #7b61ff;
            color: white;
        }
        .loading {
            opacity: 0.6;
            pointer-events: none;
        }
        /* Notification styles */
        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 20px;
            border-radius: 8px;
            color: white;
            font-weight: 600;
            z-index: 1000;
            transform: translateX(100%);
            transition: transform 0.3s ease;
        }
        .notification.show {
            transform: translateX(0);
        }
        .notification-success {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        }
        .notification-error {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        }
        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            backdrop-filter: blur(5px);
        }
        .modal-content {
            background: white;
            margin: 15% auto;
            padding: 30px;
            border-radius: 15px;
            width: 90%;
            max-width: 400px;
            text-align: center;
            box-shadow: 0 20px 40px rgba(0,0,0,0.2);
        }
        .modal h3 {
            margin-bottom: 20px;
            color: #374151;
        }
        .modal-buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 25px;
            flex-wrap: wrap;
        }
        .btn-cancel {
            background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%);
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
        }
        .btn-confirm {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
        }
        /* Responsive styles */
        @media (max-width: 900px) {
            .main {
                padding: 1rem !important;
            }
            .stats-grid {
                grid-template-columns: 1fr !important;
                gap: 1rem !important;
            }
        }
        @media (max-width: 640px) {
            .main {
                padding: 0.5rem !important;
            }
            .stats-grid {
                grid-template-columns: 1fr !important;
                gap: 0.5rem !important;
            }
            .stat-card {
                width: 100% !important;
                margin-bottom: 0.5rem !important;
                flex-direction: column;
                align-items: flex-start;
            }
            .activity-section {
                padding: 0.5rem !important;
            }
            table, th, td {
                font-size: 12px !important;
                padding: 6px 8px !important;
            }
            .action-buttons {
                flex-direction: column;
                gap: 0.5rem;
            }
            .modal-content {
                padding: 15px !important;
                width: 98% !important;
            }
        }
        @media (max-width: 480px) {
            .main {
                padding: 0.25rem !important;
            }
            .stat-card {
                padding: 10px !important;
            }
            .activity-section {
                padding: 0.25rem !important;
            }
            th, td {
                font-size: 10px !important;
                padding: 4px 4px !important;
            }
            .modal-content {
                padding: 8px !important;
                width: 100% !important;
            }
        }
    </style>
    <div class="main px-2 sm:px-4 md:px-8">
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon appointments">📅</div>
                <div class="stat-info">
                    <h3>Total Appointments</h3>
                    <div class="stat-number">{{ number_format($totalAppointments) }}</div>
                </div>
            </div>
        </div>
        <div class="activity-section mt-4">
            <h2>Appointment List</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Phone Number</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Hospital</th>
                        <th>Symptoms</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($appointments as $appointment)
                        <tr>
                            <td>{{ $appointment->appointment_id }}</td>
                            <td><strong>{{ $appointment->user->first_name }} {{ $appointment->user->last_name }}</strong></td>
                            <td>{{ $appointment->user->phone ?? 'N/A' }}</td>
                            <td>{{ $appointment->appointment_date ? $appointment->appointment_date->format('m/d/Y') : 'N/A' }}</td>
                            <td>{{ $appointment->appointment_time ? \Carbon\Carbon::parse($appointment->appointment_time)->format('h:i A') : 'N/A' }}</td>
                            <td>{{ $appointment->hospital->name ?? 'N/A' }}</td>
                            <td>{{ $appointment->symptom ?? 'N/A' }}</td>
                            <td>
                                <div class="action-buttons">
                                    <a href="{{ route('appointment.edit', $appointment->appointment_id) }}" class="action-btn action-btn-edit" title="Edit Appointment">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </a>
                                    <button class="action-btn action-btn-delete" onclick="deleteAppointment({{ $appointment->appointment_id }})" title="Delete Appointment">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-gray-500">No appointments found</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
                @if($appointments->hasPages())
                    <div class="pagination">
                        @if($appointments->onFirstPage())
                            <button disabled>&lt;</button>
                        @else
                            <a href="{{ $appointments->previousPageUrl() }}"><button>&lt;</button></a>
                        @endif
                        @foreach($appointments->getUrlRange(1, $appointments->lastPage()) as $page => $url)
                            @if($page == $appointments->currentPage())
                                <button class="active">{{ $page }}</button>
                            @else
                                <a href="{{ $url }}"><button>{{ $page }}</button></a>
                            @endif
                        @endforeach
                        @if($appointments->hasMorePages())
                            <a href="{{ $appointments->nextPageUrl() }}"><button>&gt;</button></a>
                        @else
                            <button disabled>&gt;</button>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
    <script>
        function showNotification(message, type = 'success') {
            const notification = document.createElement('div');
            notification.className = `notification notification-${type}`;
            notification.textContent = message;
            document.body.appendChild(notification);
            setTimeout(() => notification.classList.add('show'), 100);
            setTimeout(() => {
                notification.classList.remove('show');
                setTimeout(() => document.body.removeChild(notification), 300);
            }, 3000);
        }
        function deleteAppointment(appointmentId) {
            if (confirm('Are you sure you want to delete this appointment? This action cannot be undone.')) {
                const button = event.target.closest('.action-btn');
                button.classList.add('loading');
                fetch(`/appointment/${appointmentId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                    },
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification(data.message, 'success');
                        setTimeout(() => window.location.reload(), 1000);
                    } else {
                        showNotification(data.message || 'Error deleting appointment', 'error');
                    }
                })
                .catch(error => {
                    showNotification('Error deleting appointment', 'error');
                })
                .finally(() => {
                    button.classList.remove('loading');
                });
            }
        }
    </script>
@endsection
