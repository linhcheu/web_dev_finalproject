@extends('layouts.app')

@section('title', 'Hospital Profile - CareConnect')

@section('content')
<div class="profile-container" style="padding:1rem 0;background:var(--light-color);min-height:100vh;">
    <div class="container" style="max-width:900px;margin:0 auto;">
        <!-- Header: Blue, with texture -->
        <div class="profile-header section-blue section-texture" style="text-align:center;margin-bottom:1.5rem;padding:2.5rem 1.5rem 2rem 1.5rem;border-radius:1.2rem;color:var(--primary-color);">
            <h1 style="color:var(--primary-color);margin-bottom:0.3rem;"><i class="fas fa-hospital-user" style="margin-right:0.7rem;"></i>Hospital Profile</h1>
            <p style="color:var(--primary-color);font-size:1rem;">View and update your hospital's information</p>
        </div>
        <!-- Form Section: White -->
         <div> 
            <div>
                <h2>User Information</h2>
                @if($errors->any() && request('update_type') === 'user_info')
                    <div class="alert alert-danger" style="padding:0.7rem;border-radius:0.5rem;margin-bottom:1rem;background:#fee2e2;border:1px solid #fecaca;color:#991b1b;box-shadow:0 2px 8px rgba(239,68,68,0.07);">
                        <ul style="margin:0;padding-left:1rem;">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <form method="POST" action="{{ route('hospital.profile.update') }}" class="profile-form" id="userProfileForm" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="update_type" value="user_info">
                    <div class="form-row" style="display:flex;gap:1.2rem;flex-wrap:wrap;margin-bottom:1.2rem;">
                        <div class="form-group" style="flex:1;min-width:140px;">
                            <label for="last_name" class="form-label" style="display:block;margin-bottom:0.4rem;font-weight:600;color:var(--dark-color);font-size:1rem;"><i class="fas fa-user"></i> Last Name</label>
                            <input type="text" id="last_name" name="last_name" class="form-control user-info-input" value="{{ auth()->user()->last_name }}" required style="width:100%;padding:0.7rem 1rem;border:1.5px solid #e5e7eb;border-radius:0.7rem;font-size:1rem;box-shadow:0 1.5px 6px 0 rgba(0,0,0,0.03);">
                        </div>
                        <div class="form-group" style="flex:1;min-width:140px;">
                            <label for="first_name" class="form-label" style="display:block;margin-bottom:0.4rem;font-weight:600;color:var(--dark-color);font-size:1rem;"><i class="fas fa-user"></i> First Name</label>
                            <input type="text" id="first_name" name="first_name" class="form-control user-info-input" value="{{ auth()->user()->first_name }}" required style="width:100%;padding:0.7rem 1rem;border:1.5px solid #e5e7eb;border-radius:0.7rem;font-size:1rem;box-shadow:0 1.5px 6px 0 rgba(0,0,0,0.03);">
                        </div>
                        <div class="form-group" style="flex:1;min-width:140px;">
                            <label for="user_email" class="form-label" style="display:block;margin-bottom:0.4rem;font-weight:600;color:var(--dark-color);font-size:1rem;"><i class="fas fa-envelope"></i> Email</label>
                            <input type="email" id="user_email" name="user_email" class="form-control user-info-input" value="{{ auth()->user()->email }}" required style="width:100%;padding:0.7rem 1rem;border:1.5px solid #e5e7eb;border-radius:0.7rem;font-size:1rem;box-shadow:0 1.5px 6px 0 rgba(0,0,0,0.03);">
                        </div>
                    </div>
                    <div class="form-row" style="display:flex;gap:1.2rem;flex-wrap:wrap;margin-bottom:1.2rem;">
                        <div class="form-group" style="flex:1;min-width:140px;">
                            <label for="user_phone" class="form-label" style="display:block;margin-bottom:0.4rem;font-weight:600;color:var(--dark-color);font-size:1rem;"><i class="fas fa-phone"></i> Phone</label>
                            <input type="text" id="user_phone" name="user_phone" class="form-control user-info-input" value="{{ auth()->user()->phone }}" required style="width:100%;padding:0.7rem 1rem;border:1.5px solid #e5e7eb;border-radius:0.7rem;font-size:1rem;box-shadow:0 1.5px 6px 0 rgba(0,0,0,0.03);">
                        </div>
                        <div class="form-group" style="flex:1;min-width:140px;">
                            <label for="user_profile_picture" class="form-label" style="display:block;margin-bottom:0.4rem;font-weight:600;color:var(--dark-color);font-size:1rem;"><i class="fas fa-user"></i> Admin Profile Picture</label>
                            @if(auth()->user()->profile_picture)
                                <div style="margin-bottom:0.5rem;"><img src="{{ asset('storage/' . auth()->user()->profile_picture) }}" alt="Profile Picture" style="max-width:80px;border-radius:8px;box-shadow:0 2px 8px rgba(0,0,0,0.07);"></div>
                            @endif
                                                        <div class="upload-container" id="userProfileUploadContainer" style="border:2px solid #e5e7eb;border-radius:0.7rem;padding:1.5rem;background:#f9fafb;transition:all 0.3s ease;">
                                <div class="upload-header" style="text-align:center;margin-bottom:1rem;">
                                    <i class="fas fa-user-circle" style="font-size:2rem;color:#6b7280;margin-bottom:0.5rem;"></i>
                                    <h4 style="margin:0;color:#374151;font-size:1.1rem;">Admin Profile Picture</h4>
                                    <p style="margin:0.5rem 0 0 0;color:#6b7280;font-size:0.9rem;">Choose your preferred upload method</p>
                                </div>
                                
                                <div class="upload-methods" style="display:flex;gap:0.8rem;justify-content:center;flex-wrap:wrap;margin-bottom:1rem;">
                                    <button type="button" class="upload-btn" onclick="triggerFileUpload('user_profile_picture')" style="display:flex;align-items:center;gap:0.5rem;padding:0.7rem 1.2rem;background:#3b82f6;color:white;border:none;border-radius:0.5rem;font-size:0.9rem;cursor:pointer;transition:all 0.2s;">
                                        <i class="fas fa-folder-open"></i>
                                        Browse Files
                                    </button>
                                    <button type="button" class="upload-btn" onclick="enableDragDrop('userProfileDragDrop')" style="display:flex;align-items:center;gap:0.5rem;padding:0.7rem 1.2rem;background:#10b981;color:white;border:none;border-radius:0.5rem;font-size:0.9rem;cursor:pointer;transition:all 0.2s;">
                                        <i class="fas fa-mouse-pointer"></i>
                                        Drag & Drop
                                    </button>
                                    <button type="button" class="upload-btn" onclick="enablePasteMode('userProfilePasteArea')" style="display:flex;align-items:center;gap:0.5rem;padding:0.7rem 1.2rem;background:#f59e0b;color:white;border:none;border-radius:0.5rem;font-size:0.9rem;cursor:pointer;transition:all 0.2s;">
                                        <i class="fas fa-clipboard"></i>
                                        Paste Image
                                    </button>
                                </div>
                                
                                <div class="drag-drop-area" id="userProfileDragDrop" style="display:none;border:2px dashed #d1d5db;border-radius:0.5rem;padding:2rem;text-align:center;background:#f3f4f6;transition:all 0.3s ease;cursor:pointer;">
                                    <i class="fas fa-cloud-upload-alt" style="font-size:2rem;color:#6b7280;margin-bottom:0.5rem;"></i>
                                    <p style="margin:0;color:#6b7280;font-weight:500;">Drag & drop your image here</p>
                                    <p style="margin:0.5rem 0 0 0;color:#9ca3af;font-size:0.8rem;">Release to upload</p>
                                </div>
                                
                                <div class="paste-area" id="userProfilePasteArea" style="display:none;border:2px dashed #f59e0b;border-radius:0.5rem;padding:2rem;text-align:center;background:#fef3c7;transition:all 0.3s ease;">
                                    <i class="fas fa-clipboard-check" style="font-size:2rem;color:#f59e0b;margin-bottom:0.5rem;"></i>
                                    <p style="margin:0;color:#92400e;font-weight:500;">Paste mode active</p>
                                    <p style="margin:0.5rem 0 0 0;color:#a16207;font-size:0.8rem;">Press Ctrl+V to paste your image</p>
                                    <button type="button" class="btn btn-sm btn-secondary" onclick="disablePasteMode('userProfilePasteArea')" style="margin-top:1rem;padding:0.4em 0.8em;font-size:0.8rem;">
                                        Cancel Paste Mode
                                    </button>
                                </div>
                                
                                <input type="file" id="user_profile_picture" name="user_profile_picture" class="upload-input" accept="image/*" style="display:none;">
                                <div class="upload-preview" id="userProfilePreview" style="display:none;margin-top:1rem;text-align:center;">
                                    <img id="userProfilePreviewImg" src="" alt="Preview" style="max-width:120px;max-height:120px;border-radius:8px;box-shadow:0 2px 8px rgba(0,0,0,0.1);object-fit:cover;">
                                    <button type="button" class="btn btn-sm btn-danger" onclick="removeUserProfilePreview(event)" style="margin-top:0.5rem;padding:0.3em 0.8em;font-size:0.8rem;">
                                        <i class="fas fa-times"></i> Remove
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-actions user-info-actions" id="userInfoActions" style="display:none;margin-top:1.5rem;gap:1rem;justify-content:center;align-items:center;flex-wrap:wrap;">
                        <button type="submit" class="btn btn-primary action-btn save-btn" style="font-size:1rem;padding:0.8em 2em;border-radius:2.5rem;background:linear-gradient(135deg, #10b981, #059669);color:white;border:none;box-shadow:0 4px 15px rgba(16,185,129,0.3);font-weight:600;transition:all 0.3s ease;display:flex;align-items:center;gap:0.5rem;">
                            <i class="fas fa-save"></i> Save Changes
                        </button>
                        <button type="button" class="btn btn-secondary action-btn cancel-btn" onclick="cancelUserEdit()" style="font-size:1rem;padding:0.8em 2em;border-radius:2.5rem;background:linear-gradient(135deg, #6b7280, #4b5563);color:white;border:none;box-shadow:0 4px 15px rgba(107,114,128,0.3);font-weight:600;transition:all 0.3s ease;display:flex;align-items:center;gap:0.5rem;">
                            <i class="fas fa-times"></i> Cancel
                        </button>
                    </div>
                    <button type="button" class="btn btn-light edit-btn" id="editUserInfoBtn" onclick="toggleUserEdit()" style="font-size:1rem;padding:0.7em 1.8em;border-radius:2.5rem;background:linear-gradient(135deg, #3b82f6, #2563eb);color:white;border:none;box-shadow:0 4px 15px rgba(59,130,246,0.3);font-weight:600;transition:all 0.3s ease;margin-top:1rem;display:flex;align-items:center;gap:0.5rem;">
                        <i class="fas fa-edit"></i> Edit User <Info></Info>
                    </button>
                </form>
            </div>
            <!-- Password Change Section -->
            <div style="margin-top:2rem;">
                <h2>Change Password</h2>
                @if($errors->any() && request('update_type') === 'password')
                    <div class="alert alert-danger" style="padding:0.7rem;border-radius:0.5rem;margin-bottom:1rem;background:#fee2e2;border:1px solid #fecaca;color:#991b1b;box-shadow:0 2px 8px rgba(239,68,68,0.07);">
                        <ul style="margin:0;padding-left:1rem;">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <form method="POST" action="{{ route('hospital.profile.update') }}" class="profile-form" id="userPasswordForm">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="update_type" value="password">
                    <div class="form-row" style="display:flex;gap:1.2rem;flex-wrap:wrap;margin-bottom:1.2rem;">
                        <div class="form-group" style="flex:1;min-width:140px;">
                            <label for="current_password" class="form-label" style="display:block;margin-bottom:0.4rem;font-weight:600;color:var(--dark-color);font-size:1rem;"><i class="fas fa-lock"></i> Current Password</label>
                            <input type="password" id="current_password" name="current_password" class="form-control password-input" value="" autocomplete="current-password" style="width:100%;padding:0.7rem 1rem;border:1.5px solid #e5e7eb;border-radius:0.7rem;font-size:1rem;box-shadow:0 1.5px 6px 0 rgba(0,0,0,0.03);">
                        </div>
                        <div class="form-group" style="flex:1;min-width:140px;">
                            <label for="new_password" class="form-label" style="display:block;margin-bottom:0.4rem;font-weight:600;color:var(--dark-color);font-size:1rem;"><i class="fas fa-lock"></i> New Password</label>
                            <input type="password" id="new_password" name="new_password" class="form-control password-input" value="" autocomplete="new-password" style="width:100%;padding:0.7rem 1rem;border:1.5px solid #e5e7eb;border-radius:0.7rem;font-size:1rem;box-shadow:0 1.5px 6px 0 rgba(0,0,0,0.03);">
                        </div>
                        <div class="form-group" style="flex:1;min-width:140px;">
                            <label for="new_password_confirmation" class="form-label" style="display:block;margin-bottom:0.4rem;font-weight:600;color:var(--dark-color);font-size:1rem;"><i class="fas fa-lock"></i> Confirm New Password</label>
                            <input type="password" id="new_password_confirmation" name="new_password_confirmation" class="form-control password-input" value="" autocomplete="new-password" style="width:100%;padding:0.7rem 1rem;border:1.5px solid #e5e7eb;border-radius:0.7rem;font-size:1rem;box-shadow:0 1.5px 6px 0 rgba(0,0,0,0.03);">
                        </div>
                    </div>
                                    <div class="form-actions password-actions" id="passwordActions" style="display:none;margin-top:1.5rem;gap:1rem;justify-content:center;align-items:center;flex-wrap:wrap;">
                    <button type="submit" class="btn btn-primary action-btn save-btn" style="font-size:1rem;padding:0.8em 2em;border-radius:2.5rem;background:linear-gradient(135deg, #10b981, #059669);color:white;border:none;box-shadow:0 4px 15px rgba(16,185,129,0.3);font-weight:600;transition:all 0.3s ease;display:flex;align-items:center;gap:0.5rem;">
                        <i class="fas fa-save"></i> Save Password
                    </button>
                    <button type="button" class="btn btn-secondary action-btn cancel-btn" onclick="cancelPasswordEdit()" style="font-size:1rem;padding:0.8em 2em;border-radius:2.5rem;background:linear-gradient(135deg, #6b7280, #4b5563);color:white;border:none;box-shadow:0 4px 15px rgba(107,114,128,0.3);font-weight:600;transition:all 0.3s ease;display:flex;align-items:center;gap:0.5rem;">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                </div>
                <button type="button" class="btn btn-light edit-btn" id="editPasswordBtn" onclick="togglePasswordEdit()" style="font-size:1rem;padding:0.7em 1.8em;border-radius:2.5rem;background:linear-gradient(135deg, #3b82f6, #2563eb);color:white;border:none;box-shadow:0 4px 15px rgba(59,130,246,0.3);font-weight:600;transition:all 0.3s ease;margin-top:1rem;display:flex;align-items:center;gap:0.5rem;">
                    <i class="fas fa-edit"></i> Edit Password
                </button>
                </form>
            </div>
         </div>
        <div class="profile-content section-white" style="display:flex;flex-direction:column;gap:1.5rem;background:rgba(255,255,255,0.75);backdrop-filter:blur(8px);border-radius:1.5rem;padding:2.5rem 2rem;box-shadow:0 8px 32px 0 rgba(0,0,0,0.12);">
            <div class="profile-section" style="width:100%;">
                <div class="profile-card card" style="background:rgba(255,255,255,0.85);backdrop-filter:blur(6px);border-radius:1.2rem;box-shadow:0 4px 24px 0 rgba(0,0,0,0.10);padding:2rem 1.5rem;">
                    <div class="card-header" style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;padding-bottom:0.7rem;border-bottom:1.5px solid #e5e7eb;">
                        <h2 style="color:var(--dark-color);margin:0;font-size:1.2rem;"><i class="fas fa-info-circle" style="margin-right:0.5rem;"></i>Hospital Information</h2>
                        <button class="btn btn-light edit-btn" id="editProfileBtn" onclick="delayedToggleEdit(event)" style="font-size:1rem;padding:0.7em 1.8em;border-radius:2.5rem;background:linear-gradient(135deg, #3b82f6, #2563eb);color:white;border:none;box-shadow:0 4px 15px rgba(59,130,246,0.3);font-weight:600;transition:all 0.3s ease;display:flex;align-items:center;gap:0.5rem;">
                            <span class="edit-btn-text"><i class="fas fa-edit"></i> Edit Profile</span>
                            <span class="edit-btn-spinner" style="display:none;"><i class="fas fa-spinner fa-spin"></i> Loading...</span>
                        </button>
                    </div>
                    @if(session('success'))<div class="alert alert-success" style="padding:0.7rem;border-radius:0.5rem;margin-bottom:1rem;background:#d1fae5;border:1px solid #a7f3d0;color:#065f46;box-shadow:0 2px 8px rgba(16,185,129,0.07);">{{ session('success') }}</div>@endif
                    @if($errors->any() && request('update_type') === 'hospital_info')
                        <div class="alert alert-danger" style="padding:0.7rem;border-radius:0.5rem;margin-bottom:1rem;background:#fee2e2;border:1px solid #fecaca;color:#991b1b;box-shadow:0 2px 8px rgba(239,68,68,0.07);">
                            <ul style="margin:0;padding-left:1rem;">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                
                    <form method="POST" action="{{ route('hospital.profile.update') }}" class="profile-form" id="profileForm" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="update_type" value="hospital_info">
                        <div class="form-row" style="display:flex;gap:1.2rem;flex-wrap:wrap;margin-bottom:1.2rem;">
                            <div class="form-group" style="flex:1;min-width:140px;">
                                <label for="name" class="form-label" style="display:block;margin-bottom:0.4rem;font-weight:600;color:var(--dark-color);font-size:1rem;"><i class="fas fa-hospital"></i> Hospital Name</label>
                                <input type="text" id="name" name="name" class="form-control" value="{{ $hospital->name }}" required style="width:100%;padding:0.7rem 1rem;border:1.5px solid #e5e7eb;border-radius:0.7rem;font-size:1rem;box-shadow:0 1.5px 6px 0 rgba(0,0,0,0.03);">
                            </div>
                            <div class="form-group" style="flex:1;min-width:140px;">
                                <label for="location" class="form-label" style="display:block;margin-bottom:0.4rem;font-weight:600;color:var(--dark-color);font-size:1rem;"><i class="fas fa-map-marker-alt"></i> Location</label>
                                <input type="text" id="location" name="location" class="form-control" value="{{ $hospital->location }}" required style="width:100%;padding:0.7rem 1rem;border:1.5px solid #e5e7eb;border-radius:0.7rem;font-size:1rem;box-shadow:0 1.5px 6px 0 rgba(0,0,0,0.03);">
                            </div>
                        </div>
                        <div class="form-row" style="margin-bottom:1.2rem;">
                            <div class="form-group" style="width:100%;">
                                <label for="province" class="form-label" style="display:block;margin-bottom:0.4rem;font-weight:600;color:var(--dark-color);font-size:1rem;"><i class="fas fa-map"></i> Province / City</label>
                                <select id="province" name="province" class="form-control" required style="width:100%;padding:0.7rem 1rem;border:1.5px solid #e5e7eb;border-radius:0.7rem;font-size:1rem;box-shadow:0 1.5px 6px 0 rgba(0,0,0,0.03);">
                                    @php
                                        $provinces = [
                                            'Banteay Meanchey',
                                            'Battambang',
                                            'Kampong Cham',
                                            'Kampong Chhnang',
                                            'Kampong Speu',
                                            'Kampong Thom',
                                            'Kampot',
                                            'Kandal',
                                            'Kep',
                                            'Koh Kong',
                                            'Kratie',
                                            'Mondulkiri',
                                            'Oddar Meanchey',
                                            'Pailin',
                                            'Preah Vihear',
                                            'Pursat',
                                            'Prey Veng',
                                            'Ratanakiri',
                                            'Siem Reap',
                                            'Preah Sihanouk',
                                            'Stung Treng',
                                            'Svay Rieng',
                                            'Takeo',
                                            'Tbong Khmum',
                                            'Phnom Penh'
                                        ];
                                    @endphp
                                    @foreach($provinces as $province)
                                        <option value="{{ $province }}" {{ $hospital->province == $province ? 'selected' : '' }}>{{ $province }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-row" style="display:flex;gap:1.2rem;flex-wrap:wrap;margin-bottom:1.2rem;">
                            <div class="form-group" style="flex:1;min-width:140px;">
                                <label for="contact_info" class="form-label" style="display:block;margin-bottom:0.4rem;font-weight:600;color:var(--dark-color);font-size:1rem;"><i class="fas fa-phone"></i> Contact Info</label>
                                <input type="text" id="contact_info" name="contact_info" class="form-control" value="{{ $hospital->contact_info }}" required style="width:100%;padding:0.7rem 1rem;border:1.5px solid #e5e7eb;border-radius:0.7rem;font-size:1rem;box-shadow:0 1.5px 6px 0 rgba(0,0,0,0.03);">
                            </div>
                            <div class="form-group" style="flex:1;min-width:140px;">
                                <label for="profile_picture" class="form-label" style="display:block;margin-bottom:0.4rem;font-weight:600;color:var(--dark-color);font-size:1rem;"><i class="fas fa-image"></i> Hospital Profile Picture</label>
                                @if($hospital->profile_picture)
                                    <div style="margin-bottom:0.5rem;"><img src="{{ asset('storage/' . $hospital->profile_picture) }}" alt="Profile Picture" style="max-width:80px;border-radius:8px;box-shadow:0 2px 8px rgba(0,0,0,0.07);"></div>
                                @endif
                                                                <div class="upload-container" id="hospitalProfileUploadContainer" style="border:2px solid #e5e7eb;border-radius:0.7rem;padding:1.5rem;background:#f9fafb;transition:all 0.3s ease;">
                                    <div class="upload-header" style="text-align:center;margin-bottom:1rem;">
                                        <i class="fas fa-hospital" style="font-size:2rem;color:#6b7280;margin-bottom:0.5rem;"></i>
                                        <h4 style="margin:0;color:#374151;font-size:1.1rem;">Hospital Profile Picture</h4>
                                        <p style="margin:0.5rem 0 0 0;color:#6b7280;font-size:0.9rem;">Choose your preferred upload method</p>
                                    </div>
                                    
                                    <div class="upload-methods" style="display:flex;gap:0.8rem;justify-content:center;flex-wrap:wrap;margin-bottom:1rem;">
                                        <button type="button" class="upload-btn" onclick="triggerFileUpload('profile_picture')" style="display:flex;align-items:center;gap:0.5rem;padding:0.7rem 1.2rem;background:#3b82f6;color:white;border:none;border-radius:0.5rem;font-size:0.9rem;cursor:pointer;transition:all 0.2s;">
                                            <i class="fas fa-folder-open"></i>
                                            Browse Files
                                        </button>
                                        <button type="button" class="upload-btn" onclick="enableDragDrop('hospitalProfileDragDrop')" style="display:flex;align-items:center;gap:0.5rem;padding:0.7rem 1.2rem;background:#10b981;color:white;border:none;border-radius:0.5rem;font-size:0.9rem;cursor:pointer;transition:all 0.2s;">
                                            <i class="fas fa-mouse-pointer"></i>
                                            Drag & Drop
                                        </button>
                                        <button type="button" class="upload-btn" onclick="enablePasteMode('hospitalProfilePasteArea')" style="display:flex;align-items:center;gap:0.5rem;padding:0.7rem 1.2rem;background:#f59e0b;color:white;border:none;border-radius:0.5rem;font-size:0.9rem;cursor:pointer;transition:all 0.2s;">
                                            <i class="fas fa-clipboard"></i>
                                            Paste Image
                                        </button>
                                    </div>
                                    
                                    <div class="drag-drop-area" id="hospitalProfileDragDrop" style="display:none;border:2px dashed #d1d5db;border-radius:0.5rem;padding:2rem;text-align:center;background:#f3f4f6;transition:all 0.3s ease;cursor:pointer;">
                                        <i class="fas fa-cloud-upload-alt" style="font-size:2rem;color:#6b7280;margin-bottom:0.5rem;"></i>
                                        <p style="margin:0;color:#6b7280;font-weight:500;">Drag & drop your image here</p>
                                        <p style="margin:0.5rem 0 0 0;color:#9ca3af;font-size:0.8rem;">Release to upload</p>
                                    </div>
                                    
                                    <div class="paste-area" id="hospitalProfilePasteArea" style="display:none;border:2px dashed #f59e0b;border-radius:0.5rem;padding:2rem;text-align:center;background:#fef3c7;transition:all 0.3s ease;">
                                        <i class="fas fa-clipboard-check" style="font-size:2rem;color:#f59e0b;margin-bottom:0.5rem;"></i>
                                        <p style="margin:0;color:#92400e;font-weight:500;">Paste mode active</p>
                                        <p style="margin:0.5rem 0 0 0;color:#a16207;font-size:0.8rem;">Press Ctrl+V to paste your image</p>
                                        <button type="button" class="btn btn-sm btn-secondary" onclick="disablePasteMode('hospitalProfilePasteArea')" style="margin-top:1rem;padding:0.4em 0.8em;font-size:0.8rem;">
                                            Cancel Paste Mode
                                        </button>
                                    </div>
                                    
                                    <input type="file" id="profile_picture" name="profile_picture" class="upload-input" accept="image/*" style="display:none;">
                                    <div class="upload-preview" id="hospitalProfilePreview" style="display:none;margin-top:1rem;text-align:center;">
                                        <img id="hospitalProfilePreviewImg" src="" alt="Preview" style="max-width:120px;max-height:120px;border-radius:8px;box-shadow:0 2px 8px rgba(0,0,0,0.1);object-fit:cover;">
                                        <button type="button" class="btn btn-sm btn-danger" onclick="removeHospitalProfilePreview(event)" style="margin-top:0.5rem;padding:0.3em 0.8em;font-size:0.8rem;">
                                            <i class="fas fa-times"></i> Remove
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group" style="margin-bottom:1.2rem;">
                            <label for="information" class="form-label" style="display:block;margin-bottom:0.4rem;font-weight:600;color:var(--dark-color);font-size:1rem;"><i class="fas fa-info-circle"></i> Description</label>
                            <textarea id="information" name="information" class="form-control" rows="4" required style="width:100%;padding:0.7rem 1rem;border:1.5px solid #e5e7eb;border-radius:0.7rem;font-size:1rem;box-shadow:0 1.5px 6px 0 rgba(0,0,0,0.03);resize:vertical;">{{ $hospital->information }}</textarea>
                        </div>
                        <div class="form-actions" id="formActions" style="display:none;margin-top:2rem;padding-top:1.5rem;border-top:1.5px solid #e5e7eb;gap:1rem;justify-content:center;align-items:center;flex-wrap:wrap;">
                            <button type="submit" class="btn btn-primary action-btn save-btn" style="font-size:1rem;padding:0.8em 2em;border-radius:2.5rem;background:linear-gradient(135deg, #10b981, #059669);color:white;border:none;box-shadow:0 4px 15px rgba(16,185,129,0.3);font-weight:600;transition:all 0.3s ease;display:flex;align-items:center;gap:0.5rem;">
                                <i class="fas fa-save"></i> Save Changes
                            </button>
                            <button type="button" class="btn btn-secondary action-btn cancel-btn" onclick="cancelEdit()" style="font-size:1rem;padding:0.8em 2em;border-radius:2.5rem;background:linear-gradient(135deg, #6b7280, #4b5563);color:white;border:none;box-shadow:0 4px 15px rgba(107,114,128,0.3);font-weight:600;transition:all 0.3s ease;display:flex;align-items:center;gap:0.5rem;">
                                <i class="fas fa-times"></i> Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
.section-blue {
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    color: white;
    background-image: url('https://www.transparenttextures.com/patterns/cubes.png');
    background-blend-mode: lighten;
}
.section-white {
    background: #fff;
    color: var(--dark-color);
}
.profile-card input:focus, .profile-card select:focus, .profile-card textarea:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(37,99,235,0.10);
    outline: none;
    background: rgba(237,242,255,0.25);
    transition: box-shadow 0.2s, border-color 0.2s;
}
.form-actions button:hover, .form-actions button:focus {
    transform: scale(1.04);
    box-shadow: 0 4px 16px 0 rgba(37,99,235,0.13);
}

/* Enhanced Button Styles */
.action-btn {
    position: relative;
    overflow: hidden;
}

.action-btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
    transition: left 0.5s;
}

.action-btn:hover::before {
    left: 100%;
}

.save-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(16,185,129,0.4);
}

.cancel-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(107,114,128,0.4);
}

.edit-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(59,130,246,0.4);
}

.action-btn:active {
    transform: translateY(0);
}

/* Button loading state */
.edit-btn-spinner {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

/* Responsive button adjustments */
@media (max-width: 768px) {
    .action-btn {
        font-size: 0.9rem !important;
        padding: 0.7em 1.5em !important;
    }
    
    .edit-btn {
        font-size: 0.9rem !important;
        padding: 0.6em 1.5em !important;
    }
}

/* Drag and Drop Styles */
.drag-drop-container {
    border: 2px dashed #d1d5db;
    border-radius: 0.7rem;
    padding: 1.5rem;
    text-align: center;
    background: #f9fafb;
    transition: all 0.3s ease;
    cursor: pointer;
    position: relative;
    min-height: 120px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.drag-drop-container:hover {
    border-color: #9ca3af;
    background: #f3f4f6;
}

.drag-drop-container.dragover {
    border-color: #3b82f6;
    background: #eff6ff;
    transform: scale(1.02);
}

.drag-drop-content {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.5rem;
    pointer-events: none;
}

.drag-drop-input {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    opacity: 0;
    cursor: pointer;
}

.drag-drop-preview {
    margin-top: 1rem;
    text-align: center;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.5rem;
}

.drag-drop-preview img {
    max-width: 120px;
    max-height: 120px;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    object-fit: cover;
}

.btn-sm {
    padding: 0.3em 0.8em;
    font-size: 0.8rem;
    border-radius: 0.5rem;
}

.upload-btn {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.7rem 1.2rem;
    border: none;
    border-radius: 0.5rem;
    font-size: 0.9rem;
    cursor: pointer;
    transition: all 0.2s;
    font-weight: 500;
}

.upload-btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.upload-btn:active {
    transform: translateY(0);
}

.drag-drop-area {
    border: 2px dashed #d1d5db;
    border-radius: 0.5rem;
    padding: 2rem;
    text-align: center;
    background: #f3f4f6;
    transition: all 0.3s ease;
    cursor: pointer;
}

.drag-drop-area:hover {
    border-color: #9ca3af;
    background: #e5e7eb;
}

.paste-area {
    border: 2px dashed #f59e0b;
    border-radius: 0.5rem;
    padding: 2rem;
    text-align: center;
    background: #fef3c7;
    transition: all 0.3s ease;
}

.upload-preview {
    margin-top: 1rem;
    text-align: center;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.5rem;
}

.upload-preview img {
    max-width: 120px;
    max-height: 120px;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    object-fit: cover;
}
@media (max-width: 700px) {
    .profile-content, .profile-card {
        padding: 1.2rem 0.5rem !important;
    }
    .form-row {
        flex-direction: column !important;
        gap: 0.7rem !important;
    }
    .form-actions {
        flex-direction: column !important;
        gap: 0.7rem !important;
    }
}
</style>
@endsection

@section('scripts')
<script>
function delayedToggleEdit(e) {
    e.preventDefault();
    const btn = document.getElementById('editProfileBtn');
    const text = btn.querySelector('.edit-btn-text');
    const spinner = btn.querySelector('.edit-btn-spinner');
    btn.disabled = true;
    text.style.display = 'none';
    spinner.style.display = 'inline-flex';
    setTimeout(() => {
        btn.disabled = false;
        text.style.display = 'inline-flex';
        spinner.style.display = 'none';
        toggleEdit();
    }, 1000);
}
function toggleEdit() {
    const form = document.getElementById('profileForm');
    const actions = document.getElementById('formActions');
    const inputs = form.querySelectorAll('input:not([type="file"]), textarea, select');
    inputs.forEach(input => { input.disabled = false; });
    actions.style.display = 'flex';
}
function cancelEdit() {
    const form = document.getElementById('profileForm');
    const actions = document.getElementById('formActions');
    const inputs = form.querySelectorAll('input:not([type="file"]), textarea, select');
    inputs.forEach(input => { input.disabled = true; });
    actions.style.display = 'none';
}
function toggleUserEdit() {
    const inputs = document.querySelectorAll('.user-info-input');
    const actions = document.getElementById('userInfoActions');
    const editBtn = document.getElementById('editUserInfoBtn');
    inputs.forEach(input => { input.disabled = false; });
    actions.style.display = 'flex';
    editBtn.style.display = 'none';
}
function cancelUserEdit() {
    const inputs = document.querySelectorAll('.user-info-input');
    const actions = document.getElementById('userInfoActions');
    const editBtn = document.getElementById('editUserInfoBtn');
    inputs.forEach(input => { input.disabled = true; });
    actions.style.display = 'none';
    editBtn.style.display = 'inline-flex';
}
function togglePasswordEdit() {
    const inputs = document.querySelectorAll('.password-input');
    const actions = document.getElementById('passwordActions');
    const editBtn = document.getElementById('editPasswordBtn');
    inputs.forEach(input => { input.disabled = false; });
    actions.style.display = 'flex';
    editBtn.style.display = 'none';
}
function cancelPasswordEdit() {
    const inputs = document.querySelectorAll('.password-input');
    const actions = document.getElementById('passwordActions');
    const editBtn = document.getElementById('editPasswordBtn');
    inputs.forEach(input => { input.disabled = true; });
    actions.style.display = 'none';
    editBtn.style.display = 'inline-flex';
}

// Upload functionality functions
function triggerFileUpload(inputId) {
    const input = document.getElementById(inputId);
    input.click();
}

function enableDragDrop(dragDropId) {
    // Hide all upload areas first
    hideAllUploadAreas();
    
    // Show drag drop area
    const dragDropArea = document.getElementById(dragDropId);
    dragDropArea.style.display = 'block';
    
    // Initialize drag and drop for this area
    initializeDragDrop(dragDropId, getInputIdFromDragDropId(dragDropId), getPreviewIdFromDragDropId(dragDropId), getPreviewImgIdFromDragDropId(dragDropId));
}

function enablePasteMode(pasteAreaId) {
    // Hide all upload areas first
    hideAllUploadAreas();
    
    // Show paste area
    const pasteArea = document.getElementById(pasteAreaId);
    pasteArea.style.display = 'block';
    
    // Focus on the paste area to enable paste events
    pasteArea.focus();
    
    // Add paste event listener
    const inputId = getInputIdFromPasteAreaId(pasteAreaId);
    const previewId = getPreviewIdFromPasteAreaId(pasteAreaId);
    const previewImgId = getPreviewImgIdFromPasteAreaId(pasteAreaId);
    
    pasteArea.addEventListener('paste', function(e) {
        e.preventDefault();
        const items = e.clipboardData.items;
        
        for (let i = 0; i < items.length; i++) {
            const item = items[i];
            if (item.type.startsWith('image/')) {
                const file = item.getAsFile();
                console.log('Image pasted:', file.name, file.type);
                handleFileSelect(file, document.getElementById(inputId), document.getElementById(previewId), document.getElementById(previewImgId));
                disablePasteMode(pasteAreaId);
                break;
            }
        }
    });
}

function disablePasteMode(pasteAreaId) {
    const pasteArea = document.getElementById(pasteAreaId);
    pasteArea.style.display = 'none';
}

function hideAllUploadAreas() {
    // Hide all drag drop areas
    const dragDropAreas = document.querySelectorAll('.drag-drop-area');
    dragDropAreas.forEach(area => area.style.display = 'none');
    
    // Hide all paste areas
    const pasteAreas = document.querySelectorAll('.paste-area');
    pasteAreas.forEach(area => area.style.display = 'none');
}

function getInputIdFromDragDropId(dragDropId) {
    const mapping = {
        'userProfileDragDrop': 'user_profile_picture',
        'hospitalProfileDragDrop': 'profile_picture'
    };
    return mapping[dragDropId];
}

function getPreviewIdFromDragDropId(dragDropId) {
    const mapping = {
        'userProfileDragDrop': 'userProfilePreview',
        'hospitalProfileDragDrop': 'hospitalProfilePreview'
    };
    return mapping[dragDropId];
}

function getPreviewImgIdFromDragDropId(dragDropId) {
    const mapping = {
        'userProfileDragDrop': 'userProfilePreviewImg',
        'hospitalProfileDragDrop': 'hospitalProfilePreviewImg'
    };
    return mapping[dragDropId];
}

function getInputIdFromPasteAreaId(pasteAreaId) {
    const mapping = {
        'userProfilePasteArea': 'user_profile_picture',
        'hospitalProfilePasteArea': 'profile_picture'
    };
    return mapping[pasteAreaId];
}

function getPreviewIdFromPasteAreaId(pasteAreaId) {
    const mapping = {
        'userProfilePasteArea': 'userProfilePreview',
        'hospitalProfilePasteArea': 'hospitalProfilePreview'
    };
    return mapping[pasteAreaId];
}

function getPreviewImgIdFromPasteAreaId(pasteAreaId) {
    const mapping = {
        'userProfilePasteArea': 'userProfilePreviewImg',
        'hospitalProfilePasteArea': 'hospitalProfilePreviewImg'
    };
    return mapping[pasteAreaId];
}

// Drag and Drop functionality
function initializeDragDrop(containerId, inputId, previewId, previewImgId) {
    const container = document.getElementById(containerId);
    const input = document.getElementById(inputId);
    const preview = document.getElementById(previewId);
    const previewImg = document.getElementById(previewImgId);

    // Drag and drop events
    container.addEventListener('dragover', function(e) {
        e.preventDefault();
        container.classList.add('dragover');
    });

    container.addEventListener('dragleave', function(e) {
        e.preventDefault();
        container.classList.remove('dragover');
    });

    container.addEventListener('drop', function(e) {
        e.preventDefault();
        e.stopPropagation();
        container.classList.remove('dragover');
        
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            const file = files[0];
            console.log('File dropped:', file.name, file.type);
            handleFileSelect(file, input, preview, previewImg);
        }
    });

    // Click to browse
    container.addEventListener('click', function(e) {
        // Don't trigger if clicking on the preview or remove button
        if (!e.target.closest('.drag-drop-preview')) {
            input.click();
        }
    });

    // File input change
    input.addEventListener('change', function(e) {
        if (e.target.files.length > 0) {
            const file = e.target.files[0];
            console.log('File selected:', file.name, file.type);
            handleFileSelect(file, input, preview, previewImg);
        }
    });

    // Paste from clipboard
    container.addEventListener('paste', function(e) {
        e.preventDefault();
        const items = e.clipboardData.items;
        
        for (let i = 0; i < items.length; i++) {
            const item = items[i];
            if (item.type.startsWith('image/')) {
                const file = item.getAsFile();
                console.log('Image pasted:', file.name, file.type);
                handleFileSelect(file, input, preview, previewImg);
                break;
            }
        }
    });
}

function handleFileSelect(file, input, preview, previewImg) {
    console.log('Handling file:', file.name, file.type, file.size);
    
    if (file.type.startsWith('image/')) {
        const reader = new FileReader();
        reader.onload = function(e) {
            console.log('File loaded successfully');
            previewImg.src = e.target.result;
            preview.style.display = 'block';
            
            // Also update the file input value
            const dt = new DataTransfer();
            dt.items.add(file);
            input.files = dt.files;
            
            console.log('Preview should now be visible');
        };
        reader.onerror = function(e) {
            console.error('Error reading file:', e);
            alert('Error reading the image file.');
        };
        reader.readAsDataURL(file);
    } else {
        alert('Please select an image file.');
    }
}

function removeUserProfilePreview(event) {
    event.preventDefault();
    event.stopPropagation();
    
    const input = document.getElementById('user_profile_picture');
    const preview = document.getElementById('userProfilePreview');
    const previewImg = document.getElementById('userProfilePreviewImg');
    
    input.value = '';
    preview.style.display = 'none';
    previewImg.src = '';
}

function removeHospitalProfilePreview(event) {
    event.preventDefault();
    event.stopPropagation();
    
    const input = document.getElementById('profile_picture');
    const preview = document.getElementById('hospitalProfilePreview');
    const previewImg = document.getElementById('hospitalProfilePreviewImg');
    
    input.value = '';
    preview.style.display = 'none';
    previewImg.src = '';
}

// Form submission handlers to ensure fields are enabled
document.addEventListener('DOMContentLoaded', function() {
    // Initialize file input change handlers
    const fileInputs = document.querySelectorAll('.upload-input');
    fileInputs.forEach(input => {
        input.addEventListener('change', function(e) {
            if (e.target.files.length > 0) {
                const file = e.target.files[0];
                console.log('File selected:', file.name, file.type);
                
                // Find the corresponding preview elements
                const inputId = e.target.id;
                const previewId = inputId === 'user_profile_picture' ? 'userProfilePreview' : 'hospitalProfilePreview';
                const previewImgId = inputId === 'user_profile_picture' ? 'userProfilePreviewImg' : 'hospitalProfilePreviewImg';
                
                handleFileSelect(file, e.target, document.getElementById(previewId), document.getElementById(previewImgId));
            }
        });
    });
    
    // Initially disable fields
    const userInputs = document.querySelectorAll('.user-info-input');
    userInputs.forEach(input => { input.disabled = true; });
    const passwordInputs = document.querySelectorAll('.password-input');
    passwordInputs.forEach(input => { input.disabled = true; });
    
    // Hospital info form - enable fields before submission
    const profileForm = document.getElementById('profileForm');
    if (profileForm) {
        profileForm.addEventListener('submit', function(e) {
            console.log('Hospital form submitting...');
            const inputs = this.querySelectorAll('input:not([type="file"]), textarea, select');
            inputs.forEach(input => { 
                input.disabled = false; 
                console.log('Field:', input.name, 'Value:', input.value, 'Disabled:', input.disabled);
            });
        });
    }
    
    // User info form - enable fields before submission
    const userProfileForm = document.getElementById('userProfileForm');
    if (userProfileForm) {
        userProfileForm.addEventListener('submit', function(e) {
            console.log('User form submitting...');
            const inputs = this.querySelectorAll('input:not([type="file"]), textarea, select');
            inputs.forEach(input => { 
                input.disabled = false; 
                console.log('Field:', input.name, 'Value:', input.value, 'Disabled:', input.disabled);
            });
        });
    }
    
    // Password form - enable fields before submission
    const userPasswordForm = document.getElementById('userPasswordForm');
    if (userPasswordForm) {
        userPasswordForm.addEventListener('submit', function(e) {
            console.log('Password form submitting...');
            const inputs = this.querySelectorAll('input:not([type="file"]), textarea, select');
            inputs.forEach(input => { 
                input.disabled = false; 
                console.log('Field:', input.name, 'Value:', input.value, 'Disabled:', input.disabled);
            });
        });
    }
});
</script>
@endsection 