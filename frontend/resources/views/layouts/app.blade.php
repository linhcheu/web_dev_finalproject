<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'CareConnect - Connecting Users with Hospitals')</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @yield('styles')
    <style>
        body {
            background: linear-gradient(135deg, #f8fafc 0%, #e0e7ef 100%), url('https://www.transparenttextures.com/patterns/cubes.png');
            background-blend-mode: lighten;
            min-height: 100vh;
        }
        .card, .profile-card, .form-card, .info-card, .sidebar-card, .dashboard-card, .subscription-card, .hospital-card, .appointment-card, .stat-card, .activity-section, .section {
            background: linear-gradient(135deg, #fff 80%, #f3f4f6 100%);
            box-shadow: 0 4px 24px 0 rgba(0, 0, 0, 0.08), 0 1.5px 4px 0 rgba(0,188,212,0.07);
            border-radius: 18px;
            border: 1.5px solid #e0e7ef;
            position: relative;
            overflow: hidden;
        }
        .card-header, .section-header {
            background: linear-gradient(90deg, #f3f4f6 60%, #e0e7ef 100%);
            box-shadow: 0 2px 8px 0 rgba(0,188,212,0.04);
            border-bottom: 1px solid #e0e7ef;
            border-top-left-radius: 18px;
            border-top-right-radius: 18px;
        }
        .card:hover, .profile-card:hover, .form-card:hover, .info-card:hover, .sidebar-card:hover, .dashboard-card:hover, .subscription-card:hover, .hospital-card:hover, .appointment-card:hover, .stat-card:hover, .activity-section:hover, .section:hover {
            box-shadow: 0 12px 36px 0 rgba(0, 188, 212, 0.13), 0 4px 16px 0 rgba(0,0,0,0.10);
            transform: translateY(-4px) scale(1.012);
        }
        /* Modern Animations and Interactive Elements */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px);}
            to { opacity: 1; transform: translateY(0);}
        }
        @keyframes slideInLeft {
            from { opacity: 0; transform: translateX(-30px);}
            to { opacity: 1; transform: translateX(0);}
        }
        @keyframes slideInRight {
            from { opacity: 0; transform: translateX(30px);}
            to { opacity: 1; transform: translateX(0);}
        }
        @keyframes pulse {
            0%, 100% { opacity: 1;}
            50% { opacity: 0.7;}
        }
        @keyframes glow {
            0%, 100% { box-shadow: 0 0 5px rgba(0, 188, 212, 0.5);}
            50% { box-shadow: 0 0 20px rgba(0, 188, 212, 0.8), 0 0 30px rgba(147, 51, 234, 0.5);}
        }
        @keyframes shimmer {
            0% { left: -100%;}
            100% { left: 100%;}
        }
        @keyframes ripple {
            to { transform: scale(4); opacity: 0;}
        }
        /* Enhanced Logo and Navigation */
        .logo-image {
            width: 35px;
            height: 35px;
            object-fit: contain;
            transition: transform 0.3s ease;
        }
        .nav-logo a:hover .logo-image {
            transform: scale(1.1) rotate(5deg);
        }
        .nav-menu {
            display: flex;
            align-items: center;
            gap: 1.2rem;
            flex-wrap: wrap;
        }
        .nav-link {
            display: inline-block;
            padding: 0.55em 1.3em;
            border-radius: 2em;
            font-size: 1.05rem;
            font-weight: 500;
            color: #374151;
            background: #fff;
            border: 1.5px solid #e0e7ef;
            margin: 0 0.1em;
            text-decoration: none;
            transition: background 0.18s, color 0.18s, box-shadow 0.18s, border-color 0.18s, transform 0.18s;
            box-shadow: 0 2px 8px rgba(0,0,0,0.03);
        }
        .nav-link:hover, .nav-link:focus {
            background: #f3f4f6;
            color: #2563eb;
            border-color: #a5b4fc;
            box-shadow: 0 4px 16px rgba(37,99,235,0.09);
            transform: translateY(-2px) scale(1.04);
        }
        .nav-link.active {
            background: #e0e7ef;
            color: #2563eb;
            border-color: #a5b4fc;
        }
        .nav-link:last-child {
            margin-right: 0;
        }
        .nav-link.btn-outline {
            background: #fff;
            color: #2563eb;
            border: 1.5px solid #a5b4fc;
        }
        .nav-link.btn-primary {
            background: linear-gradient(90deg, #2563eb 60%, #38bdf8 100%);
            color: #fff;
            border: none;
            box-shadow: 0 4px 16px rgba(37,99,235,0.13);
        }
        .nav-link.btn-primary:hover, .nav-link.btn-primary:focus {
            background: linear-gradient(90deg, #38bdf8 60%, #2563eb 100%);
            color: #fff;
            border: none;
        }
        @media (max-width: 900px) {
            .nav-menu {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.7rem;
                padding: 1.2rem 0.5rem;
            }
            .nav-link {
                width: 100%;
                margin: 0.2em 0;
                font-size: 1.12rem;
                text-align: left;
            }
        }
        /* Enhanced Buttons */
        .btn {
            position: relative;
            overflow: hidden;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }
        .btn:hover::before {
            left: 100%;
        }
        .btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0, 188, 212, 0.3);
        }
        /* Enhanced User Menu */
        .user-menu {
            position: relative;
            display: inline-block;
            vertical-align: middle;
        }
        .user-menu-btn {
            display: flex;
            align-items: center;
            gap: 0.7rem;
            background: #fff;
            border: 1.5px solid #e0e7ef;
            padding: 0.5rem 1.2rem 0.5rem 0.7rem;
            border-radius: 2em;
            cursor: pointer;
            font-size: 1rem;
            font-weight: 500;
            color: #374151;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
            transition: box-shadow 0.2s, border-color 0.2s, background 0.2s;
        }
        .user-menu-btn:hover, .user-menu-btn.active {
            background: #f3f4f6;
            border-color: #a5b4fc;
            box-shadow: 0 4px 16px rgba(37,99,235,0.09);
        }
        .user-avatar {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #e0e7ef;
            background: #f3f4f6;
        }
        .user-menu-btn i.fa-chevron-down {
            font-size: 1rem;
            color: #6b7280;
            margin-left: 0.2rem;
            transition: transform 0.3s;
        }
        .user-dropdown {
            position: absolute;
            top: calc(100% + 0.5rem);
            right: 0;
            min-width: 270px;
            background: #fff;
            border-radius: 1.5em;
            box-shadow: 0 16px 40px rgba(0,0,0,0.16), 0 2px 8px rgba(0,188,212,0.07);
            border: 1.5px solid #e0e7ef;
            opacity: 0;
            visibility: hidden;
            transform: scale(0.96) translateY(-10px);
            transition: all 0.28s cubic-bezier(0.4,0,0.2,1);
            z-index: 1000;
            pointer-events: none;
            padding: 0.7rem 0;
        }
        .user-dropdown.show {
            opacity: 1;
            visibility: visible;
            transform: scale(1.04) translateY(0);
            pointer-events: auto;
        }
        .user-dropdown::before {
            content: '';
            position: absolute;
            top: -8px;
            right: 40px;
            width: 18px;
            height: 18px;
            background: #fff;
            border-left: 1.5px solid #e0e7ef;
            border-top: 1.5px solid #e0e7ef;
            transform: rotate(45deg);
            z-index: 1;
        }
        .dropdown-item {
            display: flex;
            align-items: center;
            gap: 1.1rem;
            padding: 1.18rem 2rem;
            color: #374151;
            text-decoration: none;
            font-size: 1.18rem;
            font-weight: 500;
            background: transparent;
            border: none;
            width: 100%;
            text-align: left;
            cursor: pointer;
            border-radius: 1em;
            transition: background 0.18s, color 0.18s, transform 0.18s;
        }
        .dropdown-item:hover, .dropdown-item:focus {
            background: #f3f4f6;
            color: #2563eb;
            transform: translateX(4px);
        }
        .dropdown-item i {
            width: 24px;
            text-align: center;
            font-size: 1.25rem;
            color: #6b7280;
            transition: color 0.2s;
        }
        .dropdown-item:hover i, .dropdown-item:focus i {
            color: #2563eb;
        }
        .dropdown-item.logout-item {
            border-top: 1px solid #e5e7eb;
            margin-top: 0.25rem;
            color: #dc2626;
        }
        .dropdown-item.logout-item:hover, .dropdown-item.logout-item:focus {
            background: #fee2e2;
            color: #dc2626;
        }
        .dropdown-item.logout-item i {
            color: #dc2626;
        }
        .dropdown-item.logout-item:hover i, .dropdown-item.logout-item:focus i {
            color: #dc2626;
        }
        /* Enhanced Cards */
        .card, .profile-card, .form-card, .info-card, .sidebar-card, .dashboard-card, .subscription-card, .hospital-card, .appointment-card, .stat-card {
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }
        .card::before, .profile-card::before, .form-card::before, .info-card::before, .sidebar-card::before, .dashboard-card::before, .subscription-card::before, .hospital-card::before, .appointment-card::before, .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(0, 188, 212, 0.05), transparent);
            transition: left 0.6s;
        }
        .card:hover::before, .profile-card:hover::before, .form-card:hover::before, .info-card:hover::before, .sidebar-card:hover::before, .dashboard-card:hover::before, .subscription-card:hover::before, .hospital-card:hover::before, .appointment-card:hover::before, .stat-card:hover::before {
            left: 100%;
        }
        .card:hover, .profile-card:hover, .form-card:hover, .info-card:hover, .sidebar-card:hover, .dashboard-card:hover, .subscription-card:hover, .hospital-card:hover, .appointment-card:hover, .stat-card:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 20px 40px rgba(0, 188, 212, 0.15);
        }
        /* Enhanced Form Controls */
        .form-control, .form-select, textarea {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border-radius: 12px;
        }
        .form-control:focus, .form-select:focus, textarea:focus {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 188, 212, 0.2);
            border-color: var(--primary-color);
        }
        /* Loading States */
        .loading {
            opacity: 0.6;
            pointer-events: none;
        }
        .loading::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 20px;
            height: 20px;
            margin: -10px 0 0 -10px;
            border: 2px solid transparent;
            border-top: 2px solid var(--primary-color);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg);}
            100% { transform: rotate(360deg);}
        }
        /* Success/Error Notifications */
        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 20px;
            border-radius: 12px;
            color: white;
            font-weight: 600;
            z-index: 1000;
            transform: translateX(100%);
            transition: transform 0.3s ease;
            backdrop-filter: blur(10px);
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
        /* Enhanced Mobile Navigation */
        @media (max-width: 900px) {
            .nav-menu {
                position: absolute;
                top: 70px;
                left: 0;
                right: 0;
                background: rgba(255,255,255,0.98);
                backdrop-filter: blur(10px);
                box-shadow: 0 8px 32px rgba(0,188,212,0.15);
                border-bottom-left-radius: 1.5rem;
                border-bottom-right-radius: 1.5rem;
                display: none;
                flex-direction: column;
                align-items: flex-start;
                padding: 1.5rem 2rem;
                z-index: 1001;
                transition: all 0.3s cubic-bezier(.4,2,.6,1);
                animation: fadeInUp 0.4s ease-out;
            }
            .nav-menu.nav-menu-open {
                display: flex;
            }
            .nav-toggle {
                display: block;
                cursor: pointer;
                font-size: 2rem;
                color: var(--primary-color);
                margin-left: 1rem;
                transition: transform 0.3s ease;
            }
            .nav-toggle:hover {
                transform: scale(1.1);
            }
            .nav-auth {
                display: none;
            }
            .nav-menu .nav-mobile-auth {
                display: flex;
                flex-direction: column;
                width: 100%;
                margin-top: 1.5rem;
                gap: 0.5rem;
            }
            .nav-menu .btn {
                width: 100%;
                min-width: unset;
                margin-bottom: 0.5rem;
                font-size: 1.08rem;
                animation: slideInLeft 0.3s ease-out;
            }
            .user-dropdown {
                right: 0;
                left: 0;
                min-width: 96vw;
                max-width: 99vw;
                margin: 0 auto;
                border-radius: 1.5em;
            }
            .user-menu-btn {
                width: 100%;
                justify-content: flex-start;
            }
            .dropdown-item {
                font-size: 1.22rem;
                padding: 1.2rem 1.2rem;
            }
        }
        /* Enhanced Footer */
        .footer {
            background: linear-gradient(90deg, #e3f6f9 60%, #ede9fe 100%);
            color: var(--dark-color);
            padding: 3rem 0 1.5rem 0;
            border-top: 2px solid var(--primary-color);
            box-shadow: 0 -2px 16px 0 rgba(0,188,212,0.06);
        }
        .footer-section {
            transition: all 0.3s ease;
        }
        .footer-section:hover {
            transform: translateY(-5px);
        }
        .footer-section h3 {
            color: var(--primary-color);
            font-size: 1.2rem;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: color 0.3s ease;
        }
        .footer-section:hover h3 {
            color: var(--secondary-color);
        }
        .footer-section ul li a {
            color: var(--dark-color);
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            position: relative;
            overflow: hidden;
        }
        .footer-section ul li a::before {
            content: '';
            position: absolute;
            bottom: 0;
            left: -100%;
            width: 100%;
            height: 2px;
            background: var(--primary-color);
            transition: left 0.3s ease;
        }
        .footer-section ul li a:hover::before {
            left: 0;
        }
        .footer-section ul li a:hover {
            color: var(--primary-color);
            transform: translateX(5px);
        }
        .newsletter-form button {
            background: linear-gradient(90deg, var(--primary-color) 60%, var(--secondary-color) 100%);
            color: var(--white-color);
            border: none;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        .newsletter-form button::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }
        .newsletter-form button:hover::before {
            left: 100%;
        }
        .newsletter-form button:hover {
            background: linear-gradient(90deg, var(--secondary-color) 60%, var(--primary-color) 100%);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 188, 212, 0.3);
        }
        /* Page Transitions */
        .main-content {
            animation: fadeInUp 0.6s ease-out;
        }
        /* Enhanced Grid Animations */
        .grid > * {
            animation: fadeInUp 0.6s ease-out;
        }
        .grid > *:nth-child(1) { animation-delay: 0.1s;}
        .grid > *:nth-child(2) { animation-delay: 0.2s;}
        .grid > *:nth-child(3) { animation-delay: 0.3s;}
        .grid > *:nth-child(4) { animation-delay: 0.4s;}
        /* Responsive Enhancements */
        @media (max-width: 900px) {
            .footer-grid {
                grid-template-columns: 1fr;
                gap: 2rem;
            }
            .footer-section {
                border-radius: 1.2rem;
                background: var(--glass-bg, #fff);
                box-shadow: 0 2px 8px rgba(0,188,212,0.08);
                padding: 1.5rem 1rem;
                margin-bottom: 1rem;
            }
        }
        .nav-mobile-auth {
            display: none;
        }
        @media (max-width: 900px) {
            .nav-menu .nav-mobile-auth {
                display: flex !important;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-logo">
                <a href="{{ route('home') }}">
                    <img src="{{ asset('images/logo.png') }}" alt="CareConnect" class="logo-image">
                    <span>CareConnect</span>
                </a>
            </div>
            <div class="nav-menu" id="mainNavMenu">
                <a href="{{ route('hospitals.index') }}" class="nav-link">Hospitals</a>
                <a href="{{ route('subscription_view') }}" class="nav-link">Subscription Plans</a>
                <a href="{{ route('about') }}" class="nav-link">About Us</a>
                @auth
                    <div class="user-menu" style="display:inline-block;position:relative;">
                        <button class="user-menu-btn" id="userMenuBtn" type="button" aria-haspopup="true" aria-expanded="false" tabindex="0">
                            @if(auth()->user()->profile_picture)
                                <img src="{{ asset('storage/' . auth()->user()->profile_picture) }}" alt="Profile" class="user-avatar">
                            @else
                                <img src="https://ui-avatars.com/api/?name={{ auth()->user()->first_name }}" alt="Profile" class="user-avatar">
                            @endif
                            <span>{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}</span>
                            <i class="fas fa-chevron-down"></i>
                        </button>
                        <div class="user-dropdown" id="userDropdown" aria-label="User menu" tabindex="-1">
                            @if(auth()->user()->role === 'hospital_admin')
                                <a href="{{ route('hospital.profile') }}" class="dropdown-item"><i class="fas fa-user"></i> Profile</a>
                                <a href="{{ route('hospital.dashboard') }}" class="dropdown-item"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                                <a href="{{ route('hospital.appointments') }}" class="dropdown-item"><i class="fas fa-calendar-alt"></i> Manage Appointments</a>
                                <a href="{{ route('hospital.feedback') }}" class="dropdown-item"><i class="fas fa-comments"></i> Feedback</a>
                            @else
                                <a href="{{ route('profile') }}" class="dropdown-item"><i class="fas fa-user"></i> Profile</a>
                                <a href="{{ route('appointments') }}" class="dropdown-item"><i class="fas fa-calendar-alt"></i> View Appointments</a>
                                <a href="{{ route('appointments.create') }}" class="dropdown-item"><i class="fas fa-calendar-plus"></i> Book Appointment</a>
                                <a href="{{ route('user.feedback') }}" class="dropdown-item"><i class="fas fa-comments"></i> Feedback</a>
                            @endif
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item logout-item"><i class="fas fa-sign-out-alt"></i> Logout</button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="nav-link">Login</a>
                    <a href="{{ route('register') }}" class="nav-link">Sign Up</a>
                @endauth
            </div>
            <div class="nav-auth" style="display:none;"></div>
            <div class="nav-toggle" id="navToggle" tabindex="0" aria-label="Open navigation menu">
                <i class="fas fa-bars"></i>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="main-content">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-container">
            <div class="footer-grid">
                <div class="footer-section">
                    <h3><i class="fas fa-heartbeat"></i> CareConnect</h3>
                    <p>Connecting patients with the best healthcare providers for better health outcomes.</p>
                </div>
                <div class="footer-section">
                    <h3><i class="fas fa-link"></i> Quick Links</h3>
                    <ul>
                        <li><a href="{{ route('home') }}">Home</a></li>
                        <li><a href="{{ route('hospitals.index') }}">Hospitals</a></li>
                        <li><a href="{{ route('about') }}">About Us</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h3><i class="fas fa-envelope"></i> Connect With Us</h3>
                    <ul>
                        <li><a href="https://facebook.com" target="_blank"><i class="fab fa-facebook"></i> Facebook</a></li>
                        <li><a href="https://twitter.com" target="_blank"><i class="fab fa-twitter"></i> Twitter</a></li>
                        <li><a href="https://instagram.com" target="_blank"><i class="fab fa-instagram"></i> Instagram</a></li>
                    
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2024 CareConnect. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script>
        // Enhanced Mobile Navigation
        const navToggle = document.getElementById('navToggle');
        const navMenu = document.getElementById('mainNavMenu');
        navToggle.addEventListener('click', function(e) {
            e.stopPropagation();
            navMenu.classList.toggle('nav-menu-open');
            const icon = navToggle.querySelector('i');
            if (navMenu.classList.contains('nav-menu-open')) {
                icon.className = 'fas fa-times';
            } else {
                icon.className = 'fas fa-bars';
            }
        });
        // Close mobile menu when clicking outside
        document.addEventListener('click', function(event) {
            if (!navToggle.contains(event.target) && !navMenu.contains(event.target)) {
                navMenu.classList.remove('nav-menu-open');
                const icon = navToggle.querySelector('i');
                icon.className = 'fas fa-bars';
            }
        });

        // User Dropdown (Profile) - robust, accessible, and mobile-friendly
        (function() {
            const userMenuBtn = document.getElementById('userMenuBtn');
            const userDropdown = document.getElementById('userDropdown');
            let dropdownOpen = false;

            function openDropdown() {
                userDropdown.classList.add('show');
                userMenuBtn.classList.add('active');
                userMenuBtn.setAttribute('aria-expanded', 'true');
                dropdownOpen = true;
                // Rotate chevron
                const chevron = userMenuBtn.querySelector('i.fa-chevron-down');
                if (chevron) chevron.style.transform = 'rotate(180deg)';
            }
            function closeDropdown() {
                userDropdown.classList.remove('show');
                userMenuBtn.classList.remove('active');
                userMenuBtn.setAttribute('aria-expanded', 'false');
                dropdownOpen = false;
                // Reset chevron
                const chevron = userMenuBtn.querySelector('i.fa-chevron-down');
                if (chevron) chevron.style.transform = 'rotate(0deg)';
            }
            function toggleDropdown(e) {
                e.stopPropagation();
                if (dropdownOpen) {
                    closeDropdown();
                } else {
                    openDropdown();
                }
            }
            if (userMenuBtn && userDropdown) {
                userMenuBtn.addEventListener('click', toggleDropdown);
                userMenuBtn.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter' || e.key === ' ') {
                        e.preventDefault();
                        toggleDropdown(e);
                    }
                    if (e.key === 'Escape') {
                        closeDropdown();
                        userMenuBtn.blur();
                    }
                });
                // Close dropdown when clicking outside
                document.addEventListener('click', function(e) {
                    if (dropdownOpen && !userMenuBtn.contains(e.target) && !userDropdown.contains(e.target)) {
                        closeDropdown();
                    }
                });
                // Close dropdown on Escape key
                document.addEventListener('keydown', function(e) {
                    if (dropdownOpen && e.key === 'Escape') {
                        closeDropdown();
                        userMenuBtn.blur();
                    }
                });
                // Close dropdown when clicking any menu item
                userDropdown.querySelectorAll('.dropdown-item').forEach(item => {
                    item.addEventListener('click', function() {
                        closeDropdown();
                    });
                });
                // Optional: close on focus out (accessibility)
                userDropdown.addEventListener('focusout', function(e) {
                    if (!userDropdown.contains(e.relatedTarget)) {
                        closeDropdown();
                    }
                });
            }
        })();

        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Enhanced form interactions
        document.querySelectorAll('.form-control, .form-select').forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.classList.add('focused');
            });
            input.addEventListener('blur', function() {
                if (!this.value) {
                    this.parentElement.classList.remove('focused');
                }
            });
        });

        // Notification system
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
        window.showNotification = showNotification;

        // Enhanced button interactions
        document.querySelectorAll('.btn').forEach(button => {
            button.addEventListener('click', function(e) {
                // Create ripple effect
                const ripple = document.createElement('span');
                const rect = this.getBoundingClientRect();
                const size = Math.max(rect.width, rect.height);
                const x = e.clientX - rect.left - size / 2;
                const y = e.clientY - rect.top - size / 2;
                ripple.style.width = ripple.style.height = size + 'px';
                ripple.style.left = x + 'px';
                ripple.style.top = y + 'px';
                ripple.classList.add('ripple');
                this.appendChild(ripple);
                setTimeout(() => {
                    ripple.remove();
                }, 600);
            });
        });

        // Enhanced card animations on scroll
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, observerOptions);
        document.querySelectorAll('.card, .profile-card, .form-card, .info-card, .sidebar-card, .dashboard-card, .subscription-card, .hospital-card, .appointment-card, .stat-card').forEach(card => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(30px)';
            card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
            observer.observe(card);
        });

        // Enhanced loading states
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function() {
                const submitBtn = this.querySelector('button[type="submit"]');
                if (submitBtn) {
                    submitBtn.classList.add('loading');
                    submitBtn.disabled = true;
                }
            });
        });

        // Auto-hide notifications after 5 seconds
        document.querySelectorAll('.notification').forEach(notification => {
            setTimeout(() => {
                if (notification.classList.contains('show')) {
                    notification.classList.remove('show');
                    setTimeout(() => notification.remove(), 300);
                }
            }, 5000);
        });

        // Enhanced hover effects for interactive elements
        document.querySelectorAll('.nav-link, .btn, .card, .dropdown-item').forEach(element => {
            element.addEventListener('mouseenter', function() {
                this.style.transform = (this.style.transform || '') + ' scale(1.02)';
            });
            element.addEventListener('mouseleave', function() {
                this.style.transform = (this.style.transform || '').replace(' scale(1.02)', '');
            });
        });

        // Page load animations
        document.addEventListener('DOMContentLoaded', function() {
            // Animate page content on load
            const mainContent = document.querySelector('.main-content');
            if (mainContent) {
                mainContent.style.opacity = '0';
                mainContent.style.transform = 'translateY(20px)';
                setTimeout(() => {
                    mainContent.style.transition = 'opacity 0.8s ease, transform 0.8s ease';
                    mainContent.style.opacity = '1';
                    mainContent.style.transform = 'translateY(0)';
                }, 100);
            }
            // Animate navigation items
            const navItems = document.querySelectorAll('.nav-link');
            navItems.forEach((item, index) => {
                item.style.opacity = '0';
                item.style.transform = 'translateY(-10px)';
                setTimeout(() => {
                    item.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                    item.style.opacity = '1';
                    item.style.transform = 'translateY(0)';
                }, 200 + (index * 100));
            });
        });
    </script>
    @yield('scripts')
</body>
</html>