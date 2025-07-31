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
            min-width: 800px;
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
                min-width: 600px;
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
        <div class="filter-section">
            <h3 style="margin-bottom: 15px; color: #111827; font-size: 16px; font-weight: 600;">🔍 Filter Hospitals</h3>
            <form method="GET" action="{{ route('hospital.index') }}" id="filterForm">
                <div class="filter-grid">
                    <div class="filter-group">
                        <label for="hospital_id">Hospital ID</label>
                        <input type="text" id="hospital_id" name="hospital_id" value="{{ request('hospital_id') }}" placeholder="Enter hospital ID">
                    </div>
                    <div class="filter-group">
                        <label for="name">Hospital Name</label>
                        <input type="text" id="name" name="name" value="{{ request('name') }}" placeholder="Enter hospital name">
                    </div>
                    <div class="filter-group">
                        <label for="province">Province</label>
                        <select id="province" name="province">
                            <option value="all">All Provinces</option>
                            @foreach($provinces as $province)
                                <option value="{{ $province }}" {{ request('province') == $province ? 'selected' : '' }}>{{ $province }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="filter-group">
                        <label for="subscription_status">Subscription Status</label>
                        <select id="subscription_status" name="subscription_status">
                            <option value="all">All Status</option>
                            <option value="has_subscription" {{ request('subscription_status') == 'has_subscription' ? 'selected' : '' }}>Has Subscription</option>
                            <option value="no_subscription" {{ request('subscription_status') == 'no_subscription' ? 'selected' : '' }}>No Subscription</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label for="sort_by">Sort By</label>
                        <select id="sort_by" name="sort_by">
                            <option value="hospital_id" {{ $sortBy == 'hospital_id' ? 'selected' : '' }}>Hospital ID</option>
                            <option value="name" {{ $sortBy == 'name' ? 'selected' : '' }}>Hospital Name</option>
                            <option value="province" {{ $sortBy == 'province' ? 'selected' : '' }}>Province</option>
                            <option value="location" {{ $sortBy == 'location' ? 'selected' : '' }}>Location</option>
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
                        <a href="{{ route('hospital.index') }}" class="btn-filter btn-filter-secondary">
                            <i class="fas fa-times"></i>
                            Clear All
                        </a>
                    </div>
                </div>
            </form>
            
            @if(request('hospital_id') || request('name') || (request('province') && request('province') !== 'all') || (request('subscription_status') && request('subscription_status') !== 'all') || (request('sort_by') && request('sort_by') !== 'hospital_id') || (request('sort_order') && request('sort_order') !== 'asc'))
                <div class="active-filters">
                    @if(request('hospital_id'))
                        <span class="filter-tag">
                            <span>ID: {{ request('hospital_id') }}</span>
                            <button onclick="removeFilter('hospital_id')">×</button>
                        </span>
                    @endif
                    @if(request('name'))
                        <span class="filter-tag">
                            <span>Name: {{ request('name') }}</span>
                            <button onclick="removeFilter('name')">×</button>
                        </span>
                    @endif
                    @if(request('province'))
                        <span class="filter-tag">
                            <span>Province: {{ request('province') }}</span>
                            <button onclick="removeFilter('province')">×</button>
                        </span>
                    @endif
                    @if(request('subscription_status') && request('subscription_status') !== 'all')
                        <span class="filter-tag">
                            <span>Status: {{ ucfirst(request('subscription_status')) }}</span>
                            <button onclick="removeFilter('subscription_status')">×</button>
                        </span>
                    @endif
                    @if(request('sort_by') && request('sort_by') !== 'hospital_id')
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
            <div class="flex justify-between items-center mb-4">
                <h2>Hospital List</h2>
                <a href="{{ route('hospital.create') }}" class="btn bg-blue-500 hover:bg-blue-600 text-white">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Add New Hospital
                </a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Province</th>
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
                            <td>{{ $hospital->province ?? 'N/A' }}</td>
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
                                <td colspan="7" class="text-center py-4 text-gray-500">No hospitals found</td>
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
        
        function removeFilter(filterName) {
            const url = new URL(window.location);
            url.searchParams.delete(filterName);
            window.location.href = url.toString();
        }
    </script>
@endsection
