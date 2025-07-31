@extends('layouts.app')

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

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        .main {
            flex-grow: 1;
            padding: 30px;
            background: linear-gradient(135deg, #c3c5d3 0%, #b3b0b6 100%);
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

        .user-info {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            padding: 25px;
            border-radius: 15px;
            margin-bottom: 30px;
            border: 1px solid #cbd5e1;
        }

        .user-info h4 {
            margin-bottom: 20px;
            color: #374151;
            font-size: 18px;
            font-weight: 600;
        }

        .user-info-grid {
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

        .role-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .role-user {
            background: #dbeafe;
            color: #1e40af;
        }

        .role-hospital {
            background: #fef3c7;
            color: #d97706;
        }

        .form-group {
            margin-bottom: 25px;
            animation: slideIn 0.6s ease-out;
        }

        .form-group:nth-child(1) { animation-delay: 0.1s; }
        .form-group:nth-child(2) { animation-delay: 0.2s; }
        .form-group:nth-child(3) { animation-delay: 0.3s; }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #374151;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .form-group input {
            width: 100%;
            padding: 15px 20px;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            font-size: 16px;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.8);
        }

        .form-group input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
            transform: translateY(-2px);
            background: white;
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
        }

        .btn-secondary {
            background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%);
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
            display: flex;
            align-items: center;
            animation: pulse 0.3s ease-in-out;
        }

        .error::before {
            content: '⚠';
            margin-right: 5px;
            font-size: 14px;
        }

        .success {
            color: #059669;
            font-size: 14px;
            margin-bottom: 25px;
            padding: 15px;
            background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
            border-radius: 12px;
            border: 1px solid #10b981;
            display: flex;
            align-items: center;
            animation: slideIn 0.5s ease-out;
        }

        .success::before {
            content: '✓';
            margin-right: 10px;
            font-size: 18px;
            font-weight: bold;
        }

        .password-section {
            background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 25px;
            border: 1px solid #fca5a5;
            position: relative;
            overflow: hidden;
        }

        .password-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, #ef4444, #dc2626);
        }

        .password-toggle {
            position: relative;
        }

        .password-toggle input {
            padding-right: 50px;
        }

        .password-toggle button {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: #6b7280;
            padding: 5px;
            border-radius: 6px;
            transition: all 0.2s ease;
        }

        .password-toggle button:hover {
            background: rgba(102, 126, 234, 0.1);
            color: #667eea;
        }

        .section-title {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            position: relative;
            z-index: 1;
        }

        .page-title {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-size: 28px;
            font-weight: 800;
            margin-bottom: 30px;
            text-align: center;
            position: relative;
        }

        .page-title::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 60px;
            height: 3px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 2px;
        }

        .profile-picture-section {
            background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 25px;
            border: 1px solid #86efac;
            position: relative;
            overflow: hidden;
            text-align: center;
        }

        .profile-picture-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, #22c55e, #16a34a);
        }

        .profile-picture-container {
            position: relative;
            display: inline-block;
            margin-bottom: 20px;
        }

        .profile-picture {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid white;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
            transition: all 0.3s ease;
        }

        .profile-picture:hover {
            transform: scale(1.05);
            box-shadow: 0 12px 35px rgba(0, 0, 0, 0.2);
        }

        .profile-picture-upload {
            position: absolute;
            bottom: 0;
            right: 0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
            transition: all 0.3s ease;
        }

        .profile-picture-upload:hover {
            transform: scale(1.1);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
        }

        .profile-picture-input {
            display: none;
        }

        .profile-picture-info {
            font-size: 12px;
            color: #6b7280;
            margin-top: 10px;
        }

        /* Enhanced Image Upload Styles */
        .image-upload-container {
            position: relative;
            margin-bottom: 20px;
        }
        
        .upload-area {
            border: 3px dashed #cbd5e1;
            border-radius: 15px;
            padding: 30px;
            text-align: center;
            background: rgba(255, 255, 255, 0.5);
            transition: all 0.3s ease;
            cursor: pointer;
            position: relative;
            margin-bottom: 15px;
        }
        
        .upload-area:hover {
            border-color: #667eea;
            background: rgba(102, 126, 234, 0.05);
            transform: translateY(-2px);
        }
        
        .upload-area.dragover {
            border-color: #667eea;
            background: rgba(102, 126, 234, 0.1);
            transform: scale(1.02);
        }
        
        .upload-area.dragover::after {
            content: 'Drop image here';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: rgba(102, 126, 234, 0.9);
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            z-index: 10;
        }
        
        .upload-area.has-image {
            border-color: #10b981;
            background: rgba(16, 185, 129, 0.05);
        }
        
        .upload-area.has-image .upload-text {
            color: #10b981;
        }
        
        .upload-area.has-image .upload-hint {
            color: #059669;
        }
        
        .upload-icon {
            font-size: 48px;
            color: #667eea;
            margin-bottom: 15px;
        }
        
        .upload-text {
            color: #374151;
            font-weight: 600;
            margin-bottom: 8px;
            font-size: 16px;
        }
        
        .upload-hint {
            color: #6b7280;
            font-size: 14px;
            margin-bottom: 15px;
        }
        
        .upload-options {
            display: flex;
            gap: 10px;
            justify-content: center;
            flex-wrap: wrap;
        }
        
        .upload-btn {
            padding: 8px 16px;
            border: 2px solid #667eea;
            border-radius: 8px;
            background: white;
            color: #667eea;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .upload-btn:hover {
            background: #667eea;
            color: white;
            transform: translateY(-1px);
        }
        
        .upload-btn svg {
            width: 16px;
            height: 16px;
        }
        
        .file-input {
            display: none;
        }
        
        .paste-hint {
            position: fixed;
            top: 20px;
            right: 20px;
            background: rgba(102, 126, 234, 0.9);
            color: white;
            padding: 10px 15px;
            border-radius: 8px;
            font-size: 14px;
            z-index: 1000;
            animation: slideIn 0.3s ease-out;
        }
        
        .profile-picture.updated {
            border-color: #10b981;
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
            animation: pulse 0.6s ease-in-out;
        }
        
        .preview-status {
            position: absolute;
            top: -10px;
            right: -10px;
            background: #10b981;
            color: white;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            opacity: 0;
            transform: scale(0.8);
            transition: all 0.3s ease;
            z-index: 10;
        }
        
        .preview-status.show {
            opacity: 1;
            transform: scale(1);
        }
        
        .image-ready-indicator {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 600;
            text-align: center;
            margin-top: 10px;
            opacity: 0;
            transform: translateY(-10px);
            transition: all 0.3s ease;
        }
        
        .image-ready-indicator.show {
            opacity: 1;
            transform: translateY(0);
        }

        .basic-info-section {
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
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
            background: linear-gradient(90deg, #0ea5e9, #0284c7);
        }

        .email-section {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 25px;
            border: 1px solid #fbbf24;
            position: relative;
            overflow: hidden;
        }

        .email-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, #f59e0b, #d97706);
        }

        .basic-info-section .section-title {
            background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .email-section .section-title {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .password-section .section-title {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .profile-picture-section .section-title {
            background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .basic-info-section .section-title::before {
            content: '👤';
        }

        .email-section .section-title::before {
            content: '📧';
        }

        .password-section .section-title::before {
            content: '🔒';
        }

        .profile-picture-section .section-title::before {
            content: '📷';
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

        @media (max-width: 640px) {
            .user-edit-form {
                padding: 0.5rem !important;
            }
            .form-group {
                flex-direction: column !important;
                gap: 0.5rem !important;
            }
            label, input, select, textarea {
                font-size: 14px !important;
            }
            .upload-options {
                flex-direction: column;
                align-items: center;
            }
            .upload-btn {
                width: 100%;
                justify-content: center;
            }
        }
    </style>

    <div class="main px-2 sm:px-4 md:px-8">
        <div class="form-container">
            <h2 class="page-title">Edit User: {{ $user->first_name }} {{ $user->last_name }}</h2>

            @if(session('success'))
                <div class="success">
                    {{ session('success') }}
                </div>
            @endif

            <!-- User Information Display -->
            <div class="user-info">
                <h4>Current User Information</h4>
                <div class="user-info-grid">
                    <div class="info-item">
                        <span class="info-label">User ID</span>
                        <span class="info-value">{{ $user->user_id }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Role</span>
                        <span class="role-badge role-{{ $user->role }}">
                            {{ ucfirst(str_replace('_', ' ', $user->role)) }}
                        </span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Status</span>
                        <span class="info-value {{ $user->is_active ? 'text-green-600' : 'text-red-600' }}">
                            {{ $user->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Created</span>
                        <span class="info-value">{{ $user->created_at ? $user->created_at->format('M d, Y') : 'N/A' }}</span>
                    </div>
                </div>
            </div>

            <!-- Profile Picture Section -->
            <div class="profile-picture-section">
                <h3 class="section-title">Profile Picture</h3>

                <form method="POST" action="{{ route('user.update', $user->user_id) }}" enctype="multipart/form-data" class="section-form">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="update_type" value="profile_picture">

                    <div class="image-upload-container" data-input-name="profile_picture">
                        <div class="profile-picture-container">
                            <div style="position: relative; display: inline-block;">
                                @if($user->profile_picture)
                                    <img src="{{ asset('storage/' . $user->profile_picture) }}?v={{ time() }}" alt="Profile Picture" class="profile-picture" id="profile-preview">
                                    <!-- Debug: {{ $user->profile_picture }} -->
                                    <!-- Debug URL: {{ asset('storage/' . $user->profile_picture) }} -->
                                @else
                                    <img src="{{ asset('images/default-profile.jpg') }}?v={{ time() }}" alt="Default Profile" class="profile-picture" id="profile-preview">
                                    <!-- Debug: No profile picture found -->
                                @endif
                                <div class="preview-status" id="profile-status">NEW</div>
                            </div>
                        </div>

                        <div class="upload-area" id="profile-upload-area">
                            <div class="upload-icon">📷</div>
                            <div class="upload-text">Upload Profile Picture</div>
                            <div class="upload-hint">Drag & drop, paste from clipboard, or click to browse</div>
                            <div class="upload-options">
                                <button type="button" class="upload-btn" onclick="document.getElementById('profile_picture').click()">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    Browse Files
                                </button>
                                <button type="button" class="upload-btn" onclick="pasteFromClipboard('profile_picture')">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    Paste Image
                                </button>
                            </div>
                        </div>

                        <input type="file" id="profile_picture" name="profile_picture" class="file-input" accept="image/*">
                        <div class="image-ready-indicator" id="profile-ready-indicator">✓ Image Ready to Update</div>
                        
                        <div class="profile-picture-info">
                            Supported formats: JPEG, PNG, JPG, GIF (Max: 2MB)
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">Update Profile Picture</button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Basic Information Section -->
            <div class="basic-info-section">
                <h3 class="section-title">Basic Information</h3>

                <form method="POST" action="{{ route('user.update', $user->user_id) }}" class="section-form">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="update_type" value="basic_info">

                    <div class="form-group">
                        <label for="first_name">First Name</label>
                        <input type="text" id="first_name" name="first_name" value="{{ old('first_name', $user->first_name) }}" required placeholder="Enter first name">
                        @error('first_name')
                        <div class="error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="last_name">Last Name</label>
                        <input type="text" id="last_name" name="last_name" value="{{ old('last_name', $user->last_name) }}" required placeholder="Enter last name">
                        @error('last_name')
                        <div class="error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input type="tel" id="phone" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="Enter phone number">
                        @error('phone')
                        <div class="error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Update Basic Information</button>
                    </div>
                </form>
            </div>

            <!-- Email Section -->
            <div class="email-section">
                <h3 class="section-title">Email Address</h3>

                <form method="POST" action="{{ route('user.update', $user->user_id) }}" class="section-form">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="update_type" value="email">

                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required placeholder="Enter email address">
                        @error('email')
                        <div class="error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Update Email Address</button>
                    </div>
                </form>
            </div>

            <!-- Password Change Section -->
            <div class="password-section">
                <h3 class="section-title">Change Password</h3>

                <form method="POST" action="{{ route('user.update', $user->user_id) }}" class="section-form">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="update_type" value="password">

                    <div class="form-group">
                        <label for="new_password">New Password</label>
                        <div class="password-toggle">
                            <input type="password" id="new_password" name="new_password" placeholder="Enter new password">
                            <button type="button" onclick="togglePassword('new_password')">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </button>
                        </div>
                        @error('new_password')
                        <div class="error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="new_password_confirmation">Confirm New Password</label>
                        <div class="password-toggle">
                            <input type="password" id="new_password_confirmation" name="new_password_confirmation" placeholder="Confirm new password">
                            <button type="button" onclick="togglePassword('new_password_confirmation')">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </button>
                        </div>
                        @error('new_password_confirmation')
                        <div class="error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Update Password</button>
                    </div>
                </form>
            </div>

            <div class="flex justify-end space-x-4 mt-8">
                <a href="{{ route('user.management') }}" class="btn btn-secondary">Back to User Management</a>
            </div>
            
            <!-- Debug Section -->
          
          
        </div>
    </div>

    <script>
        // Enhanced Image Upload Functionality
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM loaded, initializing user image upload...');
            
            // Initialize upload area
            initializeUploadArea();
            
            // Global paste listener
            document.addEventListener('paste', handleGlobalPaste);
            
            // Global drag and drop listener
            document.addEventListener('dragover', handleGlobalDragOver);
            document.addEventListener('drop', handleGlobalDrop);
        });

        function initializeUploadArea() {
            console.log('Initializing user upload area...');
            const uploadArea = document.getElementById('profile-upload-area');
            const input = document.getElementById('profile_picture');
            const preview = document.getElementById('profile-preview');
            
            console.log('Upload area found:', !!uploadArea);
            console.log('Input found:', !!input);
            console.log('Preview found:', !!preview);
            
            if (!uploadArea || !input || !preview) {
                console.error('Missing required elements for image upload');
                return;
            }
            
            // Drag and drop for upload area
            uploadArea.addEventListener('dragover', (e) => {
                e.preventDefault();
                uploadArea.classList.add('dragover');
                console.log('Drag over upload area');
            });
            
            uploadArea.addEventListener('dragleave', (e) => {
                e.preventDefault();
                uploadArea.classList.remove('dragover');
            });
            
            uploadArea.addEventListener('drop', (e) => {
                e.preventDefault();
                uploadArea.classList.remove('dragover');
                console.log('File dropped on upload area');
                
                const files = e.dataTransfer.files;
                if (files.length > 0) {
                    handleFileUpload(files[0], input, preview);
                }
            });
            
            // File input change
            input.addEventListener('change', (e) => {
                console.log('File input changed:', e.target.files.length, 'files');
                if (e.target.files.length > 0) {
                    handleFileUpload(e.target.files[0], input, preview);
                }
            });
        }

        function handleGlobalPaste(e) {
            console.log('Global paste event');
            const items = e.clipboardData.items;
            for (let item of items) {
                if (item.type.indexOf('image') !== -1) {
                    const file = item.getAsFile();
                    if (file) {
                        console.log('Image pasted:', file.name, file.type);
                        const input = document.getElementById('profile_picture');
                        const preview = document.getElementById('profile-preview');
                        
                        handleFileUpload(file, input, preview);
                        showPasteHint('Image pasted successfully!');
                    }
                }
            }
        }

        function handleGlobalDragOver(e) {
            e.preventDefault();
            if (e.target.closest('.upload-area')) {
                e.target.closest('.upload-area').classList.add('dragover');
            }
        }

        function handleGlobalDrop(e) {
            e.preventDefault();
            const uploadArea = e.target.closest('.upload-area');
            if (uploadArea) {
                uploadArea.classList.remove('dragover');
                const files = e.dataTransfer.files;
                if (files.length > 0) {
                    const input = document.getElementById('profile_picture');
                    const preview = document.getElementById('profile-preview');
                    
                    handleFileUpload(files[0], input, preview);
                }
            }
        }

        function pasteFromClipboard(inputId) {
            console.log('Paste from clipboard called for:', inputId);
            navigator.clipboard.read().then(data => {
                for (let item of data) {
                    for (let type of item.types) {
                        if (type.startsWith('image/')) {
                            item.getType(type).then(blob => {
                                const file = new File([blob], 'pasted-image.png', { type: type });
                                const input = document.getElementById(inputId);
                                const preview = document.getElementById('profile-preview');
                                
                                handleFileUpload(file, input, preview);
                                showPasteHint('Image pasted successfully!');
                            });
                            return;
                        }
                    }
                }
                showPasteHint('No image found in clipboard');
            }).catch(err => {
                console.error('Clipboard error:', err);
                showPasteHint('Failed to read clipboard. Try copying an image first.');
            });
        }

        function handleFileUpload(file, input, preview) {
            console.log('Handling file upload:', file.name, file.type, file.size);
            console.log('Preview element:', preview);
            console.log('Input element:', input);
            
            // Validate file size (2MB = 2 * 1024 * 1024 bytes)
            if (file.size > 2 * 1024 * 1024) {
                showPasteHint('File size must be less than 2MB');
                return;
            }

            // Validate file type
            const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
            if (!allowedTypes.includes(file.type)) {
                showPasteHint('Please select a valid image file (JPEG, PNG, JPG, GIF)');
                return;
            }

            // Create preview
            const reader = new FileReader();
            reader.onload = function(e) {
                console.log('File read successfully, updating preview');
                console.log('Preview element before update:', preview);
                console.log('New image data:', e.target.result.substring(0, 50) + '...');
                
                // Update the preview image
                preview.src = e.target.result;
                
                // Force a reflow to ensure the image updates
                preview.style.display = 'none';
                preview.offsetHeight; // Trigger reflow
                preview.style.display = '';
                
                // Add updated class and show status
                preview.classList.add('updated');
                
                // Show status badge
                const status = document.getElementById('profile-status');
                if (status) {
                    status.textContent = 'NEW';
                    status.classList.add('show');
                    console.log('Status badge updated');
                }
                
                // Show ready indicator
                const readyIndicator = document.getElementById('profile-ready-indicator');
                if (readyIndicator) {
                    readyIndicator.classList.add('show');
                    console.log('Ready indicator shown');
                }
                
                // Update upload area appearance
                const uploadArea = document.getElementById('profile-upload-area');
                if (uploadArea) {
                    uploadArea.classList.add('has-image');
                    console.log('Upload area updated');
                }
                
                // Animation
                preview.style.animation = 'pulse 0.6s ease-in-out';
                setTimeout(() => {
                    preview.style.animation = '';
                }, 600);
                
                console.log('Preview updated successfully');
                console.log('Preview src after update:', preview.src);
            };
            
            reader.onerror = function(e) {
                console.error('Error reading file:', e);
                showPasteHint('Error reading file');
            };
            
            reader.readAsDataURL(file);

            // Update the file input
            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(file);
            input.files = dataTransfer.files;
            
            console.log('File input updated');
        }

        function showPasteHint(message) {
            console.log('Showing hint:', message);
            const hint = document.createElement('div');
            hint.className = 'paste-hint';
            hint.textContent = message;
            document.body.appendChild(hint);
            
            setTimeout(() => {
                hint.remove();
            }, 3000);
        }

        // Test Functions
        function testElements() {
            console.log('=== Testing Elements ===');
            const input = document.getElementById('profile_picture');
            const preview = document.getElementById('profile-preview');
            
            console.log('Input found:', !!input);
            console.log('Preview found:', !!preview);
            
            if (preview) {
                console.log('Preview src:', preview.src);
                console.log('Preview style:', preview.style.cssText);
            }
        }

        function testImageUpdate() {
            console.log('=== Testing Image Update ===');
            const preview = document.getElementById('profile-preview');
            if (preview) {
                console.log('Current src:', preview.src);
                // Test with a simple data URL
                preview.src = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTAwIiBoZWlnaHQ9IjEwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMTAwIiBoZWlnaHQ9IjEwMCIgZmlsbD0icmVkIi8+PC9zdmc+';
                console.log('Updated src:', preview.src);
                preview.classList.add('updated');
                console.log('Added updated class');
            } else {
                console.error('Preview not found');
            }
        }

        function testFileInput() {
            console.log('=== Testing File Input ===');
            const input = document.getElementById('profile_picture');
            if (input) {
                console.log('File input found, triggering click');
                input.click();
            } else {
                console.error('File input not found');
            }
        }

        function togglePassword(inputId) {
            const input = document.getElementById(inputId);
            const button = input.nextElementSibling;
            const svg = button.querySelector('svg');

            if (input.type === 'password') {
                input.type = 'text';
                svg.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"></path>
                `;
            } else {
                input.type = 'password';
                svg.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                `;
            }
        }
    </script>
@endsection 