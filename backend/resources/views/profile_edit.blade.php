@extends("layouts.app")

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
            max-width: 700px;
            margin: 0 auto;
            animation: fadeInUp 0.8s ease-out;
            border: 1px solid rgba(255, 255, 255, 0.2);
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

        .section-title::before {
            content: '';
            margin-right: 10px;
            font-size: 24px;
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

        .profile-picture-label {
            display: block;
            margin-bottom: 10px;
            font-weight: 600;
            color: #374151;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .profile-picture-info {
            font-size: 12px;
            color: #6b7280;
            margin-top: 10px;
        }

        .file-upload-area {
            border: 2px dashed #cbd5e1;
            border-radius: 12px;
            padding: 30px;
            text-align: center;
            background: rgba(255, 255, 255, 0.5);
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .file-upload-area:hover {
            border-color: #667eea;
            background: rgba(255, 255, 255, 0.8);
        }

        .file-upload-area.dragover {
            border-color: #667eea;
            background: rgba(102, 126, 234, 0.1);
        }

        .upload-icon {
            font-size: 48px;
            color: #667eea;
            margin-bottom: 15px;
        }

        .upload-text {
            color: #374151;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .upload-hint {
            color: #6b7280;
            font-size: 12px;
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

        .basic-info-section .section-title::before {
            content: '👤';
        }

        .email-section .section-title::before {
            content: '📧';
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
            .form-container, .profile-picture-section, .basic-info-section, .email-section, .password-section {
                width: 100% !important;
                margin-bottom: 1rem !important;
                padding: 0.5rem !important;
            }
            .form-actions {
                text-align: center !important;
                padding-top: 1rem !important;
            }
            .btn {
                width: 100%;
                margin: 0.5rem 0 !important;
            }
        }
    </style>

    <div class="main px-2 sm:px-4 md:px-8">
        <div class="form-container flex flex-col gap-6">
            <h2 class="page-title">Edit Profile</h2>
            @if(session('success'))
                <div class="success">
                    {{ session('success') }}
                </div>
            @endif
            <div class="flex flex-col md:flex-row gap-6 md:items-start">
                <!-- Profile Picture Section -->
                <div class="profile-picture-section w-full md:w-1/3 mb-4 md:mb-0">
                    <h3 class="section-title">Profile Picture</h3>

                    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="section-form">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="update_type" value="profile_picture">

                        <div class="profile-picture-container">
                        <img src="{{ $user->profile_picture ? asset('storage/' . $user->profile_picture) : asset('images/default-profile.jpg') }}" alt="Profile Picture" class="profile-picture" id="profile-preview">                            <button type="button" class="profile-picture-upload" onclick="document.getElementById('profile_picture').click()">
                                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                            </button>
                        </div>

                        <input type="file" id="profile_picture" name="profile_picture" class="profile-picture-input" accept="image/*">
                        
                        <div class="profile-picture-info">
                            Supported formats: JPEG, PNG, JPG, GIF (Max: 2MB)
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">Update Profile Picture</button>
                        </div>
                    </form>
                </div>
                <div class="flex-1 flex flex-col gap-6">
                    <!-- Basic Information Section -->
                    <div class="basic-info-section w-full">
                        <h3 class="section-title">Basic Information</h3>

                        <form method="POST" action="{{ route('profile.update') }}" class="section-form">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="update_type" value="basic_info">

                            @if($isAdmin)
                                <div class="form-group">
                                    <label for="name">Username</label>
                                    <input type="text" id="name" name="name" value="{{ old('name', $user->username) }}" required placeholder="Enter your username">
                                    @error('name')
                                    <div class="error">{{ $message }}</div>
                                    @enderror
                                </div>
                            @else
                                <div class="form-group">
                                    <label for="first_name">First Name</label>
                                    <input type="text" id="first_name" name="first_name" value="{{ old('first_name', $user->first_name) }}" required placeholder="Enter your first name">
                                    @error('first_name')
                                    <div class="error">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="last_name">Last Name</label>
                                    <input type="text" id="last_name" name="last_name" value="{{ old('last_name', $user->last_name) }}" required placeholder="Enter your last name">
                                    @error('last_name')
                                    <div class="error">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="phone">Phone Number</label>
                                    <input type="tel" id="phone" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="Enter your phone number">
                                    @error('phone')
                                    <div class="error">{{ $message }}</div>
                                    @enderror
                                </div>
                            @endif

                            <div class="form-actions">
                                <button type="submit" class="btn btn-primary">Update Basic Information</button>
                            </div>
                        </form>
                    </div>
                    <!-- Email Section -->
                    <div class="email-section w-full">
                        <h3 class="section-title">Email Address</h3>

                        <form method="POST" action="{{ route('profile.update') }}" class="section-form">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="update_type" value="email">

                            <div class="form-group">
                                <label for="email">Email Address</label>
                                <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required placeholder="Enter your email address">
                                @error('email')
                                <div class="error">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-actions">
                                <button type="submit" class="btn btn-primary">Update Email Address</button>
                            </div>
                        </form>
                    </div>
                    <!-- Password Section -->
                    <div class="password-section w-full">
                        <h3 class="section-title">Change Password</h3>

                        <form method="POST" action="{{ route('profile.update') }}" class="section-form">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="update_type" value="password">

                            <div class="form-group">
                                <label for="current_password">Current Password</label>
                                <div class="password-toggle">
                                    <input type="password" id="current_password" name="current_password" placeholder="Enter current password" required>
                                    <button type="button" onclick="togglePassword('current_password')">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </button>
                                </div>
                                @error('current_password')
                                <div class="error">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="new_password">New Password</label>
                                <div class="password-toggle">
                                    <input type="password" id="new_password" name="new_password" placeholder="Enter new password" required>
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
                                    <input type="password" id="new_password_confirmation" name="new_password_confirmation" placeholder="Confirm new password" required>
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
                </div>
            </div>
            <div class="flex justify-end space-x-4 mt-8">
                <a href="/" class="btn btn-secondary">Back to Dashboard</a>
            </div>
        </div>
    </div>

    <script>
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

        // Profile picture preview functionality
        document.getElementById('profile_picture').addEventListener('change', function(e) {
            const file = e.target.files[0];
            const preview = document.getElementById('profile-preview');
            
            if (file) {
                // Validate file size (2MB = 2 * 1024 * 1024 bytes)
                if (file.size > 2 * 1024 * 1024) {
                    alert('File size must be less than 2MB');
                    this.value = '';
                    return;
                }

                // Validate file type
                const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
                if (!allowedTypes.includes(file.type)) {
                    alert('Please select a valid image file (JPEG, PNG, JPG, GIF)');
                    this.value = '';
                    return;
                }

                // Create preview
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.animation = 'pulse 0.5s ease-in-out';
                    setTimeout(() => {
                        preview.style.animation = '';
                    }, 500);
                };
                reader.readAsDataURL(file);
            }
        });

        // Drag and drop functionality
        const profileSection = document.querySelector('.profile-picture-section');
        const fileInput = document.getElementById('profile_picture');

        profileSection.addEventListener('dragover', function(e) {
            e.preventDefault();
            this.style.borderColor = '#667eea';
            this.style.background = 'linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%)';
        });

        profileSection.addEventListener('dragleave', function(e) {
            e.preventDefault();
            this.style.borderColor = '#cbd5e1';
            this.style.background = 'linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%)';
        });

        profileSection.addEventListener('drop', function(e) {
            e.preventDefault();
            this.style.borderColor = '#cbd5e1';
            this.style.background = 'linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%)';
            
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                fileInput.files = files;
                fileInput.dispatchEvent(new Event('change'));
            }
        });

        // Click to upload functionality
        profileSection.addEventListener('click', function(e) {
            if (e.target === this || e.target.classList.contains('profile-picture')) {
                fileInput.click();
            }
        });
    </script>
@endsection
