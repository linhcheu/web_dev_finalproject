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
        .stat-icon.users { background-color: #dbeafe; color: #1d4ed8; }
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
        .notification-success {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        }
        .notification-error {
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
                <div class="stat-icon users">👥</div>
                <div class="stat-info">
                    <h3>Total Users</h3>
                    <div class="stat-number">{{ number_format($totalUsers) }}</div>
                </div>
            </div>
        </div>
            <div class="filter-section">
            <h3 style="margin-bottom: 15px; color: #111827; font-size: 16px; font-weight: 600;">🔍 Filter Users</h3>
            <form method="GET" action="{{ route('user.management') }}" id="filterForm">
                <div class="filter-grid">
                    <div class="filter-group">
                        <label for="user_id">User ID</label>
                        <input type="text" id="user_id" name="user_id" value="{{ request('user_id') }}" placeholder="Enter user ID">
                    </div>
                    <div class="filter-group">
                        <label for="name">Name</label>
                        <input type="text" id="name" name="name" value="{{ request('name') }}" placeholder="Enter name">
                    </div>
                    <div class="filter-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" value="{{ request('email') }}" placeholder="Enter email">
                    </div>
                    <div class="filter-group">
                        <label for="role">Role</label>
                        <select id="role" name="role">
                            <option value="all">All Roles</option>
                            @foreach($roles as $role)
                                <option value="{{ $role }}" {{ request('role') == $role ? 'selected' : '' }}>
                                    {{ ucfirst(str_replace('_', ' ', $role)) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="filter-group">
                        <label for="sort_by">Sort By</label>
                        <select id="sort_by" name="sort_by">
                            <option value="user_id" {{ $sortBy == 'user_id' ? 'selected' : '' }}>User ID</option>
                            <option value="first_name" {{ $sortBy == 'first_name' ? 'selected' : '' }}>First Name</option>
                            <option value="last_name" {{ $sortBy == 'last_name' ? 'selected' : '' }}>Last Name</option>
                            <option value="email" {{ $sortBy == 'email' ? 'selected' : '' }}>Email</option>
                            <option value="role" {{ $sortBy == 'role' ? 'selected' : '' }}>Role</option>
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
                        <a href="{{ route('user.management') }}" class="btn-filter btn-filter-secondary">
                            <i class="fas fa-times"></i>
                            Clear All
                        </a>
                    </div>
                </div>
            </form>
            
            @if(request('user_id') || request('name') || request('email') || (request('role') && request('role') !== 'all'))
                <div class="active-filters">
                    @if(request('user_id'))
                        <span class="filter-tag">
                            <span>ID: {{ request('user_id') }}</span>
                            <button onclick="removeFilter('user_id')">×</button>
                        </span>
                    @endif
                    @if(request('name'))
                        <span class="filter-tag">
                            <span>Name: {{ request('name') }}</span>
                            <button onclick="removeFilter('name')">×</button>
                        </span>
                    @endif
                    @if(request('email'))
                        <span class="filter-tag">
                            <span>Email: {{ request('email') }}</span>
                            <button onclick="removeFilter('email')">×</button>
                        </span>
                    @endif
                    @if(request('role') && request('role') !== 'all')
                        <span class="filter-tag">
                            <span>Role: {{ ucfirst(str_replace('_', ' ', request('role'))) }}</span>
                            <button onclick="removeFilter('role')">×</button>
                        </span>
                    @endif
                    @if(request('sort_by') && request('sort_by') !== 'user_id')
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
                <h2>User List</h2>
                <a href="{{ route('user.create') }}" class="btn bg-blue-500 hover:bg-blue-600 text-white">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Add New User
                </a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Phone Number</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td>{{ $user->user_id }}</td>
                            <td><strong>{{ $user->first_name }} {{ $user->last_name }}</strong></td>
                            <td>{{ $user->phone ?? 'N/A' }}</td>
                            <td>{{ $user->email }}</td>
                            <td>
                                <span class="px-2 py-1 text-xs rounded-full 
                                    @if($user->role == 'hospital_admin') bg-purple-100 text-purple-800
                                    @else bg-blue-100 text-blue-800
                                    @endif">
                                    {{ ucfirst(str_replace('_', ' ', $user->role)) }}
                                </span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="{{ route('user.edit', $user->user_id) }}" class="action-btn action-btn-edit" title="Edit User">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </a>
                                    <button class="action-btn action-btn-delete" onclick="deleteUser({{ $user->user_id }})" title="Delete User">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-gray-500">No users found</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
                @if($users->hasPages())
                    <div class="pagination">
                        @if($users->onFirstPage())
                            <button disabled>&lt;</button>
                        @else
                            <a href="{{ $users->previousPageUrl() }}"><button>&lt;</button></a>
                        @endif
                        @foreach($users->getUrlRange(1, $users->lastPage()) as $page => $url)
                            @if($page == $users->currentPage())
                                <button class="active">{{ $page }}</button>
                            @else
                                <a href="{{ $url }}"><button>{{ $page }}</button></a>
                            @endif
                        @endforeach
                        @if($users->hasMorePages())
                            <a href="{{ $users->nextPageUrl() }}"><button>&gt;</button></a>
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
        function deleteUser(userId) {
            if (confirm('Are you sure you want to delete this user? This action cannot be undone.')) {
                const button = event.target.closest('.action-btn');
                button.classList.add('loading');
                fetch(`/user_management/${userId}`, {
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
                        showNotification(data.message || 'Error deleting user', 'error');
                    }
                })
                .catch(error => {
                    showNotification('Error deleting user', 'error');
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
