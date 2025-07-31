@extends('layouts.app')

@section('title', 'User Profile - CareConnect')

@section('content')
<div class="profile-container" style="padding:1rem 0;background:var(--light-color);min-height:100vh;">
    <div class="container" style="max-width:900px;margin:0 auto;">
        <!-- Header: Blue, with texture -->
        <div class="profile-header section-blue section-texture" style="text-align:center;margin-bottom:1.5rem;padding:2.5rem 1.5rem 2rem 1.5rem;border-radius:1.2rem;color:var(--primary-color);">
            <h1 style="color:var(--primary-color);margin-bottom:0.3rem;"><i class="fas fa-user-circle" style="margin-right:0.7rem;"></i>User Profile</h1>
            <p style="color:var(--primary-color);font-size:1rem;">View and update your personal information</p>
        </div>
        <!-- User Info Section -->
        <div>
            <h2>User Information</h2>
            <form method="POST" action="{{ route('profile.update') }}" class="profile-form" id="userProfileForm" enctype="multipart/form-data">
                @csrf
                @method('PUT')
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
                        <label for="email" class="form-label" style="display:block;margin-bottom:0.4rem;font-weight:600;color:var(--dark-color);font-size:1rem;"><i class="fas fa-envelope"></i> Email</label>
                        <input type="email" id="email" name="email" class="form-control user-info-input" value="{{ auth()->user()->email }}" required style="width:100%;padding:0.7rem 1rem;border:1.5px solid #e5e7eb;border-radius:0.7rem;font-size:1rem;box-shadow:0 1.5px 6px 0 rgba(0,0,0,0.03);">
                    </div>
                </div>
                <div class="form-row" style="display:flex;gap:1.2rem;flex-wrap:wrap;margin-bottom:1.2rem;">
                    <div class="form-group" style="flex:1;min-width:140px;">
                        <label for="phone" class="form-label" style="display:block;margin-bottom:0.4rem;font-weight:600;color:var(--dark-color);font-size:1rem;"><i class="fas fa-phone"></i> Phone</label>
                        <input type="text" id="phone" name="phone" class="form-control user-info-input" value="{{ auth()->user()->phone }}" required style="width:100%;padding:0.7rem 1rem;border:1.5px solid #e5e7eb;border-radius:0.7rem;font-size:1rem;box-shadow:0 1.5px 6px 0 rgba(0,0,0,0.03);">
                    </div>
                    <div class="form-group" style="flex:1;min-width:140px;">
                        <label for="profile_picture" class="form-label" style="display:block;margin-bottom:0.4rem;font-weight:600;color:var(--dark-color);font-size:1rem;"><i class="fas fa-image"></i> Profile Picture</label>
                        @if(auth()->user()->profile_picture)
                            <div style="margin-bottom:0.5rem;"><img src="{{ asset('storage/' . auth()->user()->profile_picture) }}" alt="Profile Picture" style="max-width:80px;border-radius:8px;box-shadow:0 2px 8px rgba(0,0,0,0.07);"></div>
                        @endif
                        <div class="upload-container" id="userProfileUploadContainer" style="border:2px solid #e5e7eb;border-radius:0.7rem;padding:1.5rem;background:#f9fafb;transition:all 0.3s ease;">
                            <div class="upload-header" style="text-align:center;margin-bottom:1rem;">
                                <i class="fas fa-user-circle" style="font-size:2rem;color:#6b7280;margin-bottom:0.5rem;"></i>
                                <h4 style="margin:0;color:#374151;font-size:1.1rem;">Profile Picture</h4>
                                <p style="margin:0.5rem 0 0 0;color:#6b7280;font-size:0.9rem;">Choose your preferred upload method</p>
                            </div>
                            
                            <div class="upload-methods" style="display:flex;gap:0.8rem;justify-content:center;flex-wrap:wrap;margin-bottom:1rem;">
                                <button type="button" class="upload-btn" onclick="triggerFileUpload('profile_picture')" style="display:flex;align-items:center;gap:0.5rem;padding:0.7rem 1.2rem;background:#3b82f6;color:white;border:none;border-radius:0.5rem;font-size:0.9rem;cursor:pointer;transition:all 0.2s;">
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
                            
                            <input type="file" id="profile_picture" name="profile_picture" class="upload-input user-info-input" accept="image/*" style="display:none;">
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
                    <i class="fas fa-edit"></i> Edit User Info
                </button>
            </form>
        </div>
        <!-- Password Change Section -->
        <div style="margin-top:2rem;">
            <h2>Change Password</h2>
            <form method="POST" action="{{ route('profile.update') }}" class="profile-form" id="userPasswordForm">
                @csrf
                @method('PUT')
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
    return 'profile_picture';
}

function getPreviewIdFromDragDropId(dragDropId) {
    return 'userProfilePreview';
}

function getPreviewImgIdFromDragDropId(dragDropId) {
    return 'userProfilePreviewImg';
}

function getInputIdFromPasteAreaId(pasteAreaId) {
    return 'profile_picture';
}

function getPreviewIdFromPasteAreaId(pasteAreaId) {
    return 'userProfilePreview';
}

function getPreviewImgIdFromPasteAreaId(pasteAreaId) {
    return 'userProfilePreviewImg';
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
    
    const input = document.getElementById('profile_picture');
    const preview = document.getElementById('userProfilePreview');
    const previewImg = document.getElementById('userProfilePreviewImg');
    
    input.value = '';
    preview.style.display = 'none';
    previewImg.src = '';
}

document.addEventListener('DOMContentLoaded', function() {
    // Initialize file input change handlers
    const fileInputs = document.querySelectorAll('.upload-input');
    fileInputs.forEach(input => {
        input.addEventListener('change', function(e) {
            if (e.target.files.length > 0) {
                const file = e.target.files[0];
                console.log('File selected:', file.name, file.type);
                handleFileSelect(file, e.target, document.getElementById('userProfilePreview'), document.getElementById('userProfilePreviewImg'));
            }
        });
    });
    
    const userInputs = document.querySelectorAll('.user-info-input');
    userInputs.forEach(input => { input.disabled = true; });
    const passwordInputs = document.querySelectorAll('.password-input');
    passwordInputs.forEach(input => { input.disabled = true; });
});
</script>
@endsection