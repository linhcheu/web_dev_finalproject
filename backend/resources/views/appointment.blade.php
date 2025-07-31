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
            -webkit-overflow-scrolling: touch;
        }
        table {
            width: 100%;
            min-width: 900px;
            border-collapse: collapse;
        }
        th, td {
            padding: 12px 16px;
            text-align: left;
            font-size: 14px;
            word-break: break-word;
            max-width: 200px;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        th {
            background-color: #f4f6fa;
            font-weight: 600;
            white-space: nowrap;
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
            white-space: nowrap;
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
        /* Filter Styles */
        .filter-section {
            background: white;
            padding: 20px;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
            margin-bottom: 20px;
        }
        
        .filter-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
            align-items: end;
        }
        
        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
            min-width: 0; /* Allow flex items to shrink */
        }
        
        .filter-group label {
            font-size: 14px;
            font-weight: 600;
            color: #374151;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .filter-group input,
        .filter-group select {
            padding: 10px 12px;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s ease;
            background: white;
            width: 100%;
            min-width: 0; /* Allow input to shrink */
            box-sizing: border-box;
        }
        
        .filter-group input:focus,
        .filter-group select:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        
        .filter-actions {
            display: flex;
            gap: 10px;
            align-items: end;
            flex-wrap: wrap;
        }
        
        .btn-filter {
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            white-space: nowrap;
            min-width: fit-content;
        }
        
        .btn-filter-primary {
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
            color: white;
        }
        
        .btn-filter-primary:hover {
            background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
            transform: translateY(-1px);
        }
        
        .btn-filter-secondary {
            background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%);
            color: white;
        }
        
        .btn-filter-secondary:hover {
            background: linear-gradient(135deg, #4b5563 0%, #374151 100%);
            transform: translateY(-1px);
        }
        
        .active-filters {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 15px;
            padding: 10px;
            background: #f3f4f6;
            border-radius: 8px;
        }
        
        .filter-tag {
            background: #3b82f6;
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 6px;
            max-width: 200px;
            overflow: hidden;
        }
        
        .filter-tag span {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        
        .filter-tag button {
            background: none;
            border: none;
            color: white;
            cursor: pointer;
            font-size: 14px;
            padding: 0;
            margin-left: 4px;
            flex-shrink: 0;
        }
        
        /* Table responsive improvements */
        .overflow-x-auto {
            width: 100%;
            overflow-x: auto;
            box-sizing: border-box;
            -webkit-overflow-scrolling: touch;
        }
        
        table {
            width: 100%;
            min-width: 900px;
            border-collapse: collapse;
        }
        
        th, td {
            padding: 12px 16px;
            text-align: left;
            font-size: 14px;
            word-break: break-word;
            max-width: 200px;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        th {
            background-color: #f4f6fa;
            font-weight: 600;
            white-space: nowrap;
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
            white-space: nowrap;
        }
        
        /* Status badge styles */
        .status-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
            text-transform: capitalize;
            display: inline-block;
            white-space: nowrap;
        }
        
        .status-scheduled {
            background-color: #dbeafe;
            color: #1d4ed8;
        }
        
        .status-completed {
            background-color: #d1fae5;
            color: #065f46;
        }
        
        @media (max-width: 1024px) {
            .filter-grid {
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            }
            
            th, td {
                padding: 10px 12px;
                font-size: 13px;
            }
        }
        
        @media (max-width: 768px) {
            .filter-section {
                padding: 15px;
            }
            
            .filter-grid {
                grid-template-columns: 1fr;
                gap: 12px;
            }
            
            .filter-actions {
                flex-direction: column;
                align-items: stretch;
                gap: 8px;
            }
            
            .btn-filter {
                justify-content: center;
                padding: 12px 16px;
            }
            
            .active-filters {
                padding: 8px;
                gap: 6px;
            }
            
            .filter-tag {
                font-size: 11px;
                padding: 3px 10px;
                max-width: 150px;
            }
            
            th, td {
                padding: 8px 6px;
                font-size: 12px;
            }
            
            table {
                min-width: 700px;
            }
            
            .action-buttons {
                flex-direction: column;
                gap: 4px;
                align-items: stretch;
            }
        }
        
        @media (max-width: 480px) {
            .filter-section {
                padding: 12px;
                margin-bottom: 15px;
            }
            
            .filter-grid {
                gap: 10px;
            }
            
            .filter-group input,
            .filter-group select {
                padding: 8px 10px;
                font-size: 13px;
            }
            
            .btn-filter {
                padding: 10px 14px;
                font-size: 13px;
            }
            
            .active-filters {
                padding: 6px;
                gap: 4px;
            }
            
            .filter-tag {
                font-size: 10px;
                padding: 2px 8px;
                max-width: 120px;
            }
            
            th, td {
                padding: 6px 4px;
                font-size: 11px;
            }
            
            table {
                min-width: 600px;
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
        <div class="filter-section">
            <h3 style="margin-bottom: 15px; color: #111827; font-size: 16px; font-weight: 600;">🔍 Filter Appointments</h3>
            <form method="GET" action="{{ route('appointment.index') }}" id="filterForm">
                <div class="filter-grid">
                    <div class="filter-group">
                        <label for="appointment_id">Appointment ID</label>
                        <input type="text" id="appointment_id" name="appointment_id" value="{{ request('appointment_id') }}" placeholder="Enter appointment ID">
                    </div>
                    <div class="filter-group">
                        <label for="status">Status</label>
                        <select id="status" name="status">
                            <option value="all">All Status</option>
                            <option value="upcoming" {{ request('status') == 'upcoming' ? 'selected' : '' }}>Upcoming</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label for="hospital_id">Hospital</label>
                        <select id="hospital_id" name="hospital_id">
                            <option value="all">All Hospitals</option>
                            @foreach($hospitals as $hospital)
                                <option value="{{ $hospital->hospital_id }}" {{ request('hospital_id') == $hospital->hospital_id ? 'selected' : '' }}>
                                    {{ $hospital->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="filter-group">
                        <label for="user_id">Patient</label>
                        <select id="user_id" name="user_id">
                            <option value="all">All Patients</option>
                            @foreach($users as $user)
                                <option value="{{ $user->user_id }}" {{ request('user_id') == $user->user_id ? 'selected' : '' }}>
                                    {{ $user->first_name }} {{ $user->last_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="filter-group">
                        <label for="appointment_date">Appointment Date</label>
                        <input type="date" id="appointment_date" name="appointment_date" value="{{ request('appointment_date') }}">
                    </div>
                    <div class="filter-group">
                        <label for="sort_by">Sort By</label>
                        <select id="sort_by" name="sort_by">
                            <option value="appointment_id" {{ $sortBy == 'appointment_id' ? 'selected' : '' }}>Appointment ID</option>
                            <option value="appointment_date" {{ $sortBy == 'appointment_date' ? 'selected' : '' }}>Appointment Date</option>
                            <option value="appointment_time" {{ $sortBy == 'appointment_time' ? 'selected' : '' }}>Appointment Time</option>
                            <option value="status" {{ $sortBy == 'status' ? 'selected' : '' }}>Status</option>
                            <option value="created_at" {{ $sortBy == 'created_at' ? 'selected' : '' }}>Created Date</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label for="sort_order">Sort Order</label>
                        <select id="sort_order" name="sort_order">
                            <option value="asc" {{ $sortOrder == 'asc' ? 'selected' : '' }}>Lowest to Highest</option>
                            <option value="desc" {{ $sortOrder == 'desc' ? 'selected' : '' }}>Highest to Lowest</option>
                        </select>
                    </div>
                    <div class="filter-actions">
                        <button type="submit" class="btn-filter btn-filter-primary">
                            <i class="fas fa-search"></i>
                            Apply Filters
                        </button>
                        <a href="{{ route('appointment.index') }}" class="btn-filter btn-filter-secondary">
                            <i class="fas fa-times"></i>
                            Clear All
                        </a>
                    </div>
                </div>
            </form>
            
            @if(request('appointment_id') || (request('status') && request('status') !== 'all') || (request('hospital_id') && request('hospital_id') !== 'all') || (request('user_id') && request('user_id') !== 'all') || request('appointment_date') || (request('sort_by') && request('sort_by') !== 'appointment_id') || (request('sort_order') && request('sort_order') !== 'asc'))
                <div class="active-filters">
                    @if(request('appointment_id'))
                        <span class="filter-tag">
                            <span>ID: {{ request('appointment_id') }}</span>
                            <button onclick="removeFilter('appointment_id')">×</button>
                        </span>
                    @endif
                    @if(request('status') && request('status') !== 'all')
                        <span class="filter-tag">
                            <span>Status: {{ ucfirst(request('status')) }}</span>
                            <button onclick="removeFilter('status')">×</button>
                        </span>
                    @endif
                    @if(request('hospital_id') && request('hospital_id') !== 'all')
                        @php
                            $selectedHospital = $hospitals->firstWhere('hospital_id', request('hospital_id'));
                        @endphp
                        <span class="filter-tag">
                            <span>Hospital: {{ $selectedHospital ? $selectedHospital->name : 'Unknown' }}</span>
                            <button onclick="removeFilter('hospital_id')">×</button>
                        </span>
                    @endif
                    @if(request('user_id') && request('user_id') !== 'all')
                        @php
                            $selectedUser = $users->firstWhere('user_id', request('user_id'));
                        @endphp
                        <span class="filter-tag">
                            <span>Patient: {{ $selectedUser ? $selectedUser->first_name . ' ' . $selectedUser->last_name : 'Unknown' }}</span>
                            <button onclick="removeFilter('user_id')">×</button>
                        </span>
                    @endif
                    @if(request('appointment_date'))
                        <span class="filter-tag">
                            <span>Date: {{ request('appointment_date') }}</span>
                            <button onclick="removeFilter('appointment_date')">×</button>
                        </span>
                    @endif
                    @if(request('sort_by') && request('sort_by') !== 'appointment_id')
                        <span class="filter-tag">
                            <span>Sort: {{ ucfirst(str_replace('_', ' ', request('sort_by'))) }}</span>
                            <button onclick="removeFilter('sort_by')">×</button>
                        </span>
                    @endif
                    @if(request('sort_order') && request('sort_order') !== 'asc')
                        <span class="filter-tag">
                            <span>Order: {{ request('sort_order') == 'asc' ? 'Lowest to Highest' : 'Highest to Lowest' }}</span>
                            <button onclick="removeFilter('sort_order')">×</button>
                        </span>
                    @endif
                </div>
            @endif
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
                        <th>Status</th>
                        <th>Symptoms</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($appointments as $appointment)
                        <tr>
                            <td>{{ $appointment->appointment_id }}</td>
                            <td><strong>{{ $appointment->user->first_name }} {{ $appointment->user->last_name }}</strong></td>
                            <td>{{ $appointment->patient_phone ?? 'N/A' }}</td>
                            <td>{{ $appointment->appointment_date ? $appointment->appointment_date->format('m/d/Y') : 'N/A' }}</td>
                            <td>{{ $appointment->appointment_time ? \Carbon\Carbon::parse($appointment->appointment_time)->format('h:i A') : 'N/A' }}</td>
                            <td>{{ $appointment->hospital->name ?? 'N/A' }}<br><small>{{ $appointment->hospital->province ?? 'N/A' }}</small></td>
                            <td>
                                <span class="px-2 py-1 text-xs rounded-full 
                                    @if($appointment->status == 'upcoming') bg-blue-100 text-blue-800
                                    @elseif($appointment->status == 'completed') bg-green-100 text-green-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    {{ ucfirst($appointment->status) }}
                                </span>
                            </td>
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
                            <td colspan="9" class="text-center py-4 text-gray-500">No appointments found</td>
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
        
        function removeFilter(filterName) {
            const url = new URL(window.location);
            url.searchParams.delete(filterName);
            window.location.href = url.toString();
        }
    </script>
@endsection
