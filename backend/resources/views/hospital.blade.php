@extends('layouts.app')
@section('content')
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        .main {
            flex-grow: 1;
            padding: 30px;
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
        .stat-icon.hospitals { background-color: #f3e8ff; color: #9333ea; }
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
        }
        .activity-section h2 {
            font-size: 18px;
            font-weight: 600;
            color: #111827;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #e5e7eb;
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
        }
        .action-btn, .btn {
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
        .action-btn-edit, .btn-edit {
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
            color: white;
        }
        .action-btn-edit:hover, .btn-edit:hover {
            background: #2563eb;
        }
        .action-btn-delete, .btn-delete {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white;
        }
        .action-btn-delete:hover, .btn-delete:hover {
            background: #dc2626;
        }
        .action-btn svg, .btn svg {
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
        .notification.success {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        }
        .notification.error {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        }
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
        .overflow-x-auto {
            width: 100%;
            overflow-x: auto;
            box-sizing: border-box;
        }
        @media (max-width: 768px) {
            th, td {
                padding: 8px 6px;
                font-size: 12px;
            }
            table {
                min-width: 600px;
            }
            .action-buttons {
                flex-direction: column;
                gap: 4px;
                align-items: stretch;
            }
        }
        @media (max-width: 480px) {
            th, td {
                padding: 6px 2px;
                font-size: 11px;
            }
            table {
                min-width: 500px;
            }
        }
    </style>
    <div class="main px-2 sm:px-4 md:px-8">
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">🏥</div>
                <div class="stat-info">
                    <h3>Total Hospitals</h3>
                    <div class="stat-number">{{ number_format($totalHospitals) }}</div>
                </div>
            </div>
        </div>
        <div class="activity-section mt-4">
            <h2>Hospital List</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Location</th>
                            <th>Contact Info</th>
                            <th>Actions</th>
                            <th>Subscription</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($hospitals as $hospital)
                        <tr data-hospital-id="{{ $hospital->hospital_id }}">
                            <td>{{ $hospital->hospital_id }}</td>
                            <td><strong>{{ $hospital->name }}</strong></td>
                            <td>{{ $hospital->location ?? 'N/A' }}</td>
                            <td>{{ $hospital->contact_info ?? 'N/A' }}</td>
                            <td>
                                <div class="action-buttons">
                                    <a href="{{ route('hospital.edit', $hospital->hospital_id) }}" class="btn btn-edit" title="Edit Hospital">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </a>
                                    <button class="btn btn-delete" onclick="deleteHospital({{ $hospital->hospital_id }}, '{{ $hospital->name }}')" title="Delete Hospital">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                            <td>
                                @if($hospital->subscription)
                                    {{ $hospital->subscription->plan_type ?? 'N/A' }}<br>
                                    Status: {{ $hospital->subscription->status ?? 'N/A' }}
                                @else
                                    <span class="text-muted">No subscription</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-gray-500">No hospitals found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                @if($hospitals->hasPages())
                    <div class="pagination">
                        @if($hospitals->onFirstPage())
                            <button disabled>&lt;</button>
                        @else
                            <a href="{{ $hospitals->previousPageUrl() }}"><button>&lt;</button></a>
                        @endif
                        @foreach($hospitals->getUrlRange(1, $hospitals->lastPage()) as $page => $url)
                            @if($page == $hospitals->currentPage())
                                <button class="active">{{ $page }}</button>
                            @else
                                <a href="{{ $url }}"><button>{{ $page }}</button></a>
                            @endif
                        @endforeach
                        @if($hospitals->hasMorePages())
                            <a href="{{ $hospitals->nextPageUrl() }}"><button>&gt;</button></a>
                        @else
                            <button disabled>&gt;</button>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <h3>Confirm Delete</h3>
            <p>Are you sure you want to delete the hospital "<span id="hospitalName"></span>"?</p>
            <p>This action cannot be undone.</p>
            <div class="modal-buttons">
                <button class="btn-cancel" onclick="closeModal()">Cancel</button>
                <button class="btn-confirm" onclick="confirmDelete()">Delete</button>
            </div>
        </div>
    </div>
    <script>
        let hospitalToDelete = null;
        function showNotification(message, type = 'success') {
            const notification = document.createElement('div');
            notification.className = `notification ${type}`;
            notification.textContent = message;
            document.body.appendChild(notification);
            setTimeout(() => notification.classList.add('show'), 100);
            setTimeout(() => {
                notification.classList.remove('show');
                setTimeout(() => document.body.removeChild(notification), 300);
            }, 3000);
        }
        function deleteHospital(hospitalId, hospitalName) {
            hospitalToDelete = hospitalId;
            document.getElementById('hospitalName').textContent = hospitalName;
            document.getElementById('deleteModal').style.display = 'block';
        }
        function closeModal() {
            document.getElementById('deleteModal').style.display = 'none';
            hospitalToDelete = null;
        }
        function confirmDelete() {
            if (!hospitalToDelete) return;
            const row = document.querySelector(`tr[data-hospital-id="${hospitalToDelete}"]`);
            const button = row.querySelector('.btn-delete');
            button.classList.add('loading');
            row.classList.add('loading');
            fetch(`/hospital/${hospitalToDelete}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    row.remove();
                    showNotification('Hospital deleted successfully!');
                    const totalElement = document.querySelector('.stat-number');
                    const currentTotal = parseInt(totalElement.textContent.replace(/,/g, ''));
                    totalElement.textContent = (currentTotal - 1).toLocaleString();
                } else {
                    showNotification(data.message || 'Failed to delete hospital', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('An error occurred while deleting hospital', 'error');
            })
            .finally(() => {
                closeModal();
                button.classList.remove('loading');
                row.classList.remove('loading');
            });
        }
        window.onclick = function(event) {
            const modal = document.getElementById('deleteModal');
            if (event.target === modal) {
                closeModal();
            }
        }
    </script>
@endsection
