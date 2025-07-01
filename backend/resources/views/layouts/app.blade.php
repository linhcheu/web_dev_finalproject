<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>CareConnect Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @keyframes pulse {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: 0.7;
            }
        }

        /* Sidebar active animation */
        @keyframes slideIn {
            0% {
                transform: translateX(-10px);
                opacity: 0;
            }
            100% {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes glow {
            0%, 100% {
                box-shadow: 0 0 5px rgba(59, 130, 246, 0.5);
            }
            50% {
                box-shadow: 0 0 20px rgba(59, 130, 246, 0.8), 0 0 30px rgba(147, 51, 234, 0.5);
            }
        }

        .nav-item {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            color: #374151; /* Ensure default text color is always visible */
        }

        .nav-item:hover {
            transform: translateX(5px);
        }

        .nav-item.active {
            animation: slideIn 0.5s ease-out, glow 2s ease-in-out infinite;
            background: linear-gradient(135deg, #3B82F6 0%, #8B5CF6 100%);
            box-shadow: 0 4px 15px rgba(59, 130, 246, 0.4);
            color: white !important; /* Force white text for active items */
        }

        .nav-item.active::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            animation: shimmer 2s infinite;
        }

        @keyframes shimmer {
            0% {
                left: -100%;
            }
            100% {
                left: 100%;
            }
        }

        .nav-item.active svg,
        .nav-item.active span {
            color: white !important; /* Force white color for active items */
            filter: drop-shadow(0 1px 2px rgba(0, 0, 0, 0.1));
        }

        .nav-item:not(.active):hover {
            background: linear-gradient(135deg, #F3F4F6 0%, #E5E7EB 100%);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            color: #1F2937; /* Darker text on hover */
        }

        /* Ensure nav items are always visible */
        .nav-item svg,
        .nav-item span {
            color: inherit; /* Inherit the parent's color */
        }

        /* Dropdown animation */
        .dropdown-menu {
            opacity: 0;
            transform: translateY(-10px) scale(0.95);
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            pointer-events: none;
        }

        .dropdown-menu.show {
            opacity: 1;
            transform: translateY(0) scale(1);
            pointer-events: auto;
        }

        @keyframes ripple {
            to {
                transform: scale(4);
                opacity: 0;
            }
        }

        .btn-delete:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(239, 68, 68, 0.4);
        }

        /* Modern Action Buttons */
        .action-buttons {
            display: flex;
            gap: 8px;
            align-items: center;
        }

        .action-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 8px 12px;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            min-width: 40px;
            height: 40px;
        }

        .action-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        .action-btn:hover::before {
            left: 100%;
        }

        .action-btn-edit {
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);
        }

        .action-btn-edit:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(59, 130, 246, 0.4);
        }

        .action-btn-delete {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(239, 68, 68, 0.3);
        }

        .action-btn-delete:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(239, 68, 68, 0.4);
        }

        .action-btn svg {
            width: 16px;
            height: 16px;
        }

        .action-btn-text {
            margin-left: 6px;
            font-size: 12px;
        }

        /* Loading state for action buttons */
        .action-btn.loading {
            pointer-events: none;
            opacity: 0.7;
        }

        .action-btn.loading::after {
            content: '';
            position: absolute;
            width: 16px;
            height: 16px;
            border: 2px solid transparent;
            border-top: 2px solid currentColor;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Success/Error notifications */
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

        /* Loading states */
        .loading {
            opacity: 0.6;
            pointer-events: none;
        }

        .action-btn.loading::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 16px;
            height: 16px;
            margin: -8px 0 0 -8px;
            border: 2px solid transparent;
            border-top: 2px solid currentColor;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        .btn.loading::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 16px;
            height: 16px;
            margin: -8px 0 0 -8px;
            border: 2px solid transparent;
            border-top: 2px solid currentColor;
            border-radius: 50%;
            animation: spin 1s linear infinite;
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
            background-color: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(4px);
        }

        .modal-content {
            background-color: white;
            margin: 15% auto;
            padding: 2rem;
            border-radius: 12px;
            width: 90%;
            max-width: 500px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            position: relative;
        }

        .modal h3 {
            margin-top: 0;
            color: #1f2937;
            font-size: 1.25rem;
            font-weight: 600;
        }

        .modal-buttons {
            display: flex;
            gap: 1rem;
            justify-content: flex-end;
            margin-top: 1.5rem;
        }

        .btn-cancel {
            padding: 0.5rem 1rem;
            border: 1px solid #d1d5db;
            background: white;
            color: #374151;
            border-radius: 0.375rem;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-cancel:hover {
            background: #f9fafb;
        }

        .btn-confirm {
            padding: 0.5rem 1rem;
            border: none;
            background: #ef4444;
            color: white;
            border-radius: 0.375rem;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-confirm:hover {
            background: #dc2626;
        }

        @media (max-width: 640px) {
            .modal-content {
                padding: 1rem;
                max-width: 98vw;
            }
            .action-buttons {
                flex-direction: column;
                gap: 0.5rem;
            }
            .main-content, .container, .appointments-table, .appointments-box {
                padding: 0.5rem !important;
            }
            .dropdown-menu {
                min-width: 90vw !important;
                right: 0 !important;
                left: 0 !important;
            }
        }

        @media (max-width: 768px) {
            .mx-auto.md\:mx-0.md\:ml-12 {
                margin-left: auto !important;
                margin-right: auto !important;
            }
            header .flex-row {
                flex-direction: row !important;
            }
            #sidebarToggle {
                left: 0.5rem !important;
                right: auto !important;
            }
            .absolute.right-0 {
                right: 0.5rem !important;
            }
        }
    </style>
</head>
<body>
<!-- Header -->
<header class="bg-white shadow-sm border-b fixed w-full z-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full">
        <div class="flex flex-row justify-between items-center h-16 w-full relative">
            <!-- Hamburger for mobile (far left) -->
            <button id="sidebarToggle" class="md:hidden p-2 rounded hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 absolute left-0 top-1/2 transform -translate-y-1/2 z-50" aria-label="Open sidebar">
                
                <svg class="w-7 h-7 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
                
            </button>
            <!-- Logo (centered on mobile, left on desktop) -->
            <a href="{{ url('/') }}" class="flex items-center space-x-3 mx-auto md:mx-0 md:ml-12">
                <img src="{{ asset('images/logo.png') }}" alt="CareConnect Logo" class="w-12 h-12 object-contain rounded-lg bg-white p-1 shadow"/>
            </a>
            <!-- User Info/Profile Dropdown (far right) -->
            <div class="flex items-center space-x-4 absolute right-0 top-1/2 transform -translate-y-1/2">
                <div class="relative">
                    <button id="userDropdownButton" class="flex items-center space-x-2 hover:bg-gray-50 rounded-lg p-2 transition-colors duration-200">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center overflow-hidden">
                            @if(Auth::guard('admin')->check() && Auth::guard('admin')->user()->profile_picture)
                                <img src="{{ asset('storage/' . Auth::guard('admin')->user()->profile_picture) }}">
                            @elseif(Auth::guard('web')->check() && Auth::guard('web')->user()->profile_picture)
                                <img src="{{ asset('storage/' . Auth::guard('web')->user()->profile_picture) }}">
                            @endif
                        </div>
                        <span class="text-sm font-medium text-gray-700">
                            @if(Auth::guard('admin')->check())
                                {{ Auth::guard('admin')->user()->username }}
                            @elseif(Auth::guard('web')->check())
                                {{ Auth::guard('web')->user()->first_name }} {{ Auth::guard('web')->user()->last_name }}
                            @else
                                Guest
                            @endif
                        </span>
                        <svg class="w-4 h-4 text-gray-500 transition-transform duration-200" id="dropdownArrow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <!-- Dropdown Menu -->
                    <div id="userDropdownMenu" class="dropdown-menu absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-2 z-20">
                        <a href="{{ route('profile.edit') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors duration-200">
                            <svg class="w-4 h-4 mr-3 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            Profile Settings
                        </a>
                        <div class="border-t border-gray-100 my-1"></div>
                        <form method="POST" action="{{ route('admin.logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="flex items-center w-full px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors duration-200">
                                <svg class="w-4 h-4 mr-3 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                </svg>
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>

<div class="flex pt-16">
    <!-- Sidebar -->
    <aside id="sidebar" class="w-64 bg-white shadow-sm min-h-screen fixed md:static z-30 top-0 left-0 h-full transform -translate-x-full md:translate-x-0 transition-transform duration-200 ease-in-out">
        <nav class="mt-8 px-4 space-y-2">
            <a href="/" class="nav-item flex items-center px-4 py-3 text-sm font-medium rounded-lg">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M8 5a2 2 0 012-2h4a2 2 0 012 2v6H8V5z"></path>
                </svg>
                <span>Dashboard</span>
            </a>

            <a href="/appointment" class="nav-item flex items-center px-4 py-3 text-sm font-medium rounded-lg">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                <span>Appointment</span>
            </a>

            <a href="/user_management" class="nav-item flex items-center px-4 py-3 text-sm font-medium rounded-lg">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z"></path>
                </svg>
                <span>User Management</span>
            </a>

            <a href="/hospital" class="nav-item flex items-center px-4 py-3 text-sm font-medium rounded-lg">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
                <span>Hospital</span>
            </a>

            <a href="/feedback" class="nav-item flex items-center px-4 py-3 text-sm font-medium rounded-lg">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                </svg>
                <span>Feedback</span>
            </a>
        </nav>
    </aside>
    <!-- Overlay for mobile -->
    <div id="sidebarOverlay" class="fixed inset-0 bg-black bg-opacity-30 z-20 hidden md:hidden"></div>
    <!-- Main Content -->
    <main class="flex-1">
        @yield('content')
    </main>
</div>

<script>
    // Sidebar toggle for mobile
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('sidebar');
    const sidebarOverlay = document.getElementById('sidebarOverlay');
    sidebarToggle.addEventListener('click', function() {
        sidebar.classList.toggle('-translate-x-full');
        sidebarOverlay.classList.toggle('hidden');
    });
    sidebarOverlay.addEventListener('click', function() {
        sidebar.classList.add('-translate-x-full');
        sidebarOverlay.classList.add('hidden');
    });
    // Dropdown functionality
    const dropdownButton = document.getElementById('userDropdownButton');
    const dropdownMenu = document.getElementById('userDropdownMenu');
    const dropdownArrow = document.getElementById('dropdownArrow');
    dropdownButton.addEventListener('click', function() {
        dropdownMenu.classList.toggle('show');
        dropdownArrow.style.transform = dropdownMenu.classList.contains('show') ? 'rotate(180deg)' : 'rotate(0deg)';
    });
    // Close dropdown when clicking outside
    document.addEventListener('click', function(event) {
        if (!dropdownButton.contains(event.target)) {
            dropdownMenu.classList.remove('show');
            dropdownArrow.style.transform = 'rotate(0deg)';
        }
    });
    // Set active nav item based on current page
    const currentPath = window.location.pathname;
    const navItems = document.querySelectorAll('.nav-item');
    navItems.forEach(item => {
        const link = item.getAttribute('href');
        if (link === currentPath || (currentPath === '/' && link === '/')) {
            item.classList.add('active');
        } else {
            item.classList.remove('active');
        }
    });
</script>
</body>
</html>
