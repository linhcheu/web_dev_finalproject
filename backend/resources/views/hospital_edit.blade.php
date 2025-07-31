@extends('layouts.app')

@section('content')
<style>
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }
    @keyframes slideIn {
        from { opacity: 0; transform: translateX(-20px); }
        to { opacity: 1; transform: translateX(0); }
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
    .section-container {
        background: rgba(255, 255, 255, 0.8);
        border-radius: 15px;
        padding: 30px;
        margin-bottom: 30px;
        border: 1px solid rgba(102, 126, 234, 0.1);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.05);
        animation: slideIn 0.6s ease-out;
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
    .form-row {
        display: flex;
        gap: 12px;
    }
    .form-row .form-group {
        flex: 1;
        margin-bottom: 0;
    }
    .form-group {
        margin-bottom: 25px;
        animation: slideIn 0.6s ease-out;
    }
    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: #374151;
        font-size: 14px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .form-group input,
    .form-group select,
    .form-group textarea {
        width: 100%;
        padding: 15px 20px;
        border: 2px solid #e5e7eb;
        border-radius: 12px;
        font-size: 16px;
        transition: all 0.3s ease;
        background: rgba(255, 255, 255, 0.8);
    }
    .form-group input:focus,
    .form-group select:focus,
    .form-group textarea:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
        transform: translateY(-2px);
        background: white;
    }
    .form-group textarea {
        resize: vertical;
        min-height: 70px;
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
        width: 100%;
        margin-top: 10px;
    }
    .btn-primary:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
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
    
    /* Enhanced Image Upload Styles */
    .image-upload-container {
        position: relative;
        margin-bottom: 20px;
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
    
    .profile-picture.updated {
        border-color: #10b981;
        box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
        animation: pulse 0.6s ease-in-out;
    }
    
    .image-preview {
        width: 120px;
        height: 120px;
        border-radius: 8px;
        object-fit: cover;
        border: 3px solid #e5e7eb;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
        margin-bottom: 15px;
    }
    
    .image-preview:hover {
        transform: scale(1.05);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    }
    
    .image-preview.updated {
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
    
    @media (max-width: 640px) {
        .hospital-edit-form {
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
        /* Header responsive styles */
        .page-title {
            font-size: 20px !important;
        }
        .btn-secondary {
            padding: 8px 12px !important;
            font-size: 12px !important;
        }
        .btn-secondary i {
            margin-right: 4px !important;
        }
    }
    
    @media (max-width: 480px) {
        .page-title {
            font-size: 18px !important;
        }
        .btn-secondary {
            padding: 6px 10px !important;
            font-size: 11px !important;
        }
        .btn-secondary i {
            margin-right: 3px !important;
        }
    }
</style>
<div class="main px-2 sm:px-4 md:px-8">
    <div class="form-container">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h2 class="page-title" style="margin-bottom: 0;">Edit Hospital</h2>
            <a href="{{ route('hospital.index') }}" class="btn btn-secondary" style="margin-right: 0; width: auto;">
                <i class="fas fa-arrow-left" style="margin-right: 8px;"></i>
                Back to Hospitals
            </a>
        </div>
        @if(session('success'))
            <div class="success">{{ session('success') }}</div>
        @endif

    

        <!-- Hospital Information Section -->
        <div class="section-container">
            <div class="section-title">Hospital Information</div>
            <form method="POST" action="{{ route('hospital.update', $hospital->hospital_id) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" name="update_type" value="basic_info">
                <div class="form-group">
                    <label for="hospital_name">Hospital Name</label>
                    <input type="text" id="hospital_name" name="name" value="{{ old('name', $hospital->name) }}" required>
                    @error('name')<div class="error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label for="province">Province / City</label>
                    <select id="province" name="province" required>
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
                            <option value="{{ $province }}" {{ old('province', $hospital->province) == $province ? 'selected' : '' }}>{{ $province }}</option>
                        @endforeach
                    </select>
                    @error('province')<div class="error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label for="location">Hospital Location</label>
                    <textarea id="location" name="location" required>{{ old('location', $hospital->location) }}</textarea>
                    @error('location')<div class="error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label for="information">Hospital Information (About)</label>
                    <textarea id="information" name="information">{{ old('information', $hospital->information) }}</textarea>
                    @error('information')<div class="error">{{ $message }}</div>@enderror
                </div>
                <button type="submit" class="btn btn-primary">Update Hospital Information</button>
            </form>
        </div>

        <!-- Hospital Profile Picture Section -->
        <div class="section-container">
            <div class="section-title">Hospital Profile Picture</div>
            <form method="POST" action="{{ route('hospital.update', $hospital->hospital_id) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" name="update_type" value="hospital_profile_picture">
                
                <div class="image-upload-container" data-input-name="hospital_profile_picture">
                    <div class="profile-picture-container">
                        <div style="position: relative; display: inline-block;">
                            @if($hospital->profile_picture)
                                <img src="{{ asset('storage/' . $hospital->profile_picture) }}?v={{ time() }}" alt="Hospital Profile Picture" class="profile-picture" id="hospital-profile-preview">
                                <!-- Debug: {{ $hospital->profile_picture }} -->
                                <!-- Debug URL: {{ asset('storage/' . $hospital->profile_picture) }} -->
                            @else
                                <img src="{{ asset('images/default-profile.jpg') }}?v={{ time() }}" alt="Default Profile" class="profile-picture" id="hospital-profile-preview">
                                <!-- Debug: No profile picture found -->
                            @endif
                            <div class="preview-status" id="hospital-profile-status">NEW</div>
                        </div>
                    </div>
                    
                    <div class="upload-area" id="hospital-profile-upload-area">
                        <div class="upload-icon">🏥</div>
                        <div class="upload-text">Upload Hospital Profile Picture</div>
                        <div class="upload-hint">Drag & drop, paste from clipboard, or click to browse</div>
                        <div class="upload-options">
                            <button type="button" class="upload-btn" onclick="document.getElementById('hospital_profile_picture').click()">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                Browse Files
                            </button>
                            <button type="button" class="upload-btn" onclick="pasteFromClipboard('hospital_profile_picture')">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Paste Image
                            </button>
                        </div>
                    </div>
                    <input type="file" id="hospital_profile_picture" name="hospital_profile_picture" class="file-input" accept="image/*">
                    <div class="image-ready-indicator" id="hospital-profile-ready-indicator">✓ Image Ready to Update</div>
                    
                    <div style="margin-top: 15px;">
                        <button type="submit" class="btn btn-primary">Update Hospital Profile Picture</button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Contact Information Section -->
        <div class="section-container">
            <div class="section-title">Contact Information</div>
            <form method="POST" action="{{ route('hospital.update', $hospital->hospital_id) }}">
                @csrf
                @method('PUT')
                <input type="hidden" name="update_type" value="contact_info">
                <div class="form-group">
                    <label for="contact_info">Contact Information</label>
                    <textarea id="contact_info" name="contact_info" required>{{ old('contact_info', $hospital->contact_info) }}</textarea>
                    @error('contact_info')<div class="error">{{ $message }}</div>@enderror
                </div>
                <button type="submit" class="btn btn-primary">Update Contact Information</button>
            </form>
            
        </div>
        
    </div>
    
</div>

<script>
// Enhanced Image Upload Functionality
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, initializing hospital image upload...');
    
    // Initialize upload area
    initializeUploadArea();
    
    // Global paste listener
    document.addEventListener('paste', handleGlobalPaste);
    
    // Global drag and drop listener
    document.addEventListener('dragover', handleGlobalDragOver);
    document.addEventListener('drop', handleGlobalDrop);
    
    // Test if elements exist
    setTimeout(() => {
        console.log('=== Post-load element check ===');
        const input = document.getElementById('hospital_profile_picture');
        const preview = document.getElementById('hospital-profile-preview');
        console.log('Hospital input found:', !!input);
        console.log('Hospital preview found:', !!preview);
        if (input && preview) {
            console.log('✅ All elements found, upload should work');
        } else {
            console.log('❌ Missing elements, upload will not work');
        }
    }, 1000);
});

function initializeUploadArea() {
    console.log('Initializing hospital upload area...');
    const uploadArea = document.getElementById('hospital-profile-upload-area');
    const input = document.getElementById('hospital_profile_picture');
    const preview = document.getElementById('hospital-profile-preview');
    
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
                const input = document.getElementById('hospital_profile_picture');
                const preview = document.getElementById('hospital-profile-preview');
                
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
            const input = document.getElementById('hospital_profile_picture');
            const preview = document.getElementById('hospital-profile-preview');
            
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
                        const preview = document.getElementById('hospital-profile-preview');
                        
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
        const status = document.getElementById('hospital-profile-status');
        if (status) {
            status.textContent = 'NEW';
            status.classList.add('show');
            console.log('Status badge updated');
        }
        
        // Show ready indicator
        const readyIndicator = document.getElementById('hospital-profile-ready-indicator');
        if (readyIndicator) {
            readyIndicator.classList.add('show');
            console.log('Ready indicator shown');
        }
        
        // Update upload area appearance
        const uploadArea = document.getElementById('hospital-profile-upload-area');
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
    const input = document.getElementById('hospital_profile_picture');
    const preview = document.getElementById('hospital-profile-preview');
    
    console.log('Input found:', !!input);
    console.log('Preview found:', !!preview);
    
    if (preview) {
        console.log('Preview src:', preview.src);
        console.log('Preview style:', preview.style.cssText);
    }
}

function testImageUpdate() {
    console.log('=== Testing Image Update ===');
    const preview = document.getElementById('hospital-profile-preview');
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
    const input = document.getElementById('hospital_profile_picture');
    if (input) {
        console.log('File input found, triggering click');
        input.click();
    } else {
        console.error('File input not found');
    }
}

// Function to update image preview with cache busting
function updateImagePreview(imageElement, file) {
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            imageElement.src = e.target.result;
            imageElement.style.display = 'block';
            
            // Add cache-busting parameter for future updates
            const timestamp = new Date().getTime();
            imageElement.dataset.cacheBuster = timestamp;
        };
        reader.readAsDataURL(file);
    }
}

// Function to refresh image after successful upload
function refreshImageAfterUpload(imageElement, imagePath) {
    if (imagePath) {
        const timestamp = new Date().getTime();
        imageElement.src = `{{ asset('storage/') }}/${imagePath}?v=${timestamp}`;
        imageElement.style.display = 'block';
    }
}

// Check for success message and refresh image if needed
document.addEventListener('DOMContentLoaded', function() {
    const successMessage = document.querySelector('.alert-success');
    if (successMessage && successMessage.textContent.includes('profile picture')) {
        // Force reload the page to show the new image
        setTimeout(function() {
            window.location.reload();
        }, 1000);
    }
    
    // Check if we have updated profile picture in session
    @if(session('updated_hospital_profile_picture'))
        const updatedImagePath = '{{ session('updated_hospital_profile_picture') }}';
        const imageElement = document.getElementById('hospital-profile-preview');
        if (imageElement && updatedImagePath) {
            const timestamp = new Date().getTime();
            imageElement.src = `{{ asset('storage/') }}/${updatedImagePath}?v=${timestamp}`;
            imageElement.style.display = 'block';
        }
        // Clear the session data
        fetch('{{ route('hospital.edit', $hospital->hospital_id) }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ clear_session: 'hospital_profile_picture' })
        });
    @endif
});
</script>
@endsection 