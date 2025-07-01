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
    }
</style>
<div class="main px-2 sm:px-4 md:px-8">
    <div class="form-container">
        <h2 class="page-title">Edit Hospital</h2>
        @if(session('success'))
            <div class="success">{{ session('success') }}</div>
        @endif

        <!-- Administrator Information Section -->
        <div class="section-container">
            <div class="section-title">Administrator Information</div>
            <form method="POST" action="{{ route('hospital.update', $hospital->hospital_id) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" name="update_type" value="administrator">
                <div class="form-row">
                    <div class="form-group">
                        <label for="first_name">First Name</label>
                        <input type="text" id="first_name" name="first_name" value="{{ old('first_name', $hospital->owner->first_name ?? '') }}" required>
                        @error('first_name')<div class="error">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label for="last_name">Last Name</label>
                        <input type="text" id="last_name" name="last_name" value="{{ old('last_name', $hospital->owner->last_name ?? '') }}" required>
                        @error('last_name')<div class="error">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="form-group">
                    <label for="email">Professional Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email', $hospital->owner->email ?? '') }}" required>
                    @error('email')<div class="error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" autocomplete="new-password">
                    @error('password')<div class="error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label for="password_confirmation">Confirm Password</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" autocomplete="new-password">
                </div>
                <div class="form-group">
                    <label for="admin_profile_picture">Admin Profile Picture</label>
                    @if($hospital->owner && $hospital->owner->profile_picture)
                    <div style="margin-bottom: 1rem;"><img src="http://127.0.0.1:8000/storage/{{ $hospital->profile_picture }}" alt="Hospital Profile Picture" style="max-width: 120px; border-radius: 8px;"></div>
                    @endif
                    <input type="file" id="admin_profile_picture" name="admin_profile_picture" class="form-control" accept="image/*">
                </div>
                <button type="submit" class="btn btn-primary">Update Administrator Information</button>
            </form>
        </div>

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
                    <label for="location">Hospital Location</label>
                    <textarea id="location" name="location" required>{{ old('location', $hospital->location) }}</textarea>
                    @error('location')<div class="error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label for="information">Hospital Information (About)</label>
                    <textarea id="information" name="information">{{ old('information', $hospital->information) }}</textarea>
                    @error('information')<div class="error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label for="hospital_profile_picture">Hospital Profile Picture</label>
                    @if($hospital->profile_picture)
                        <div style="margin-bottom: 1rem;"><img src="http://127.0.0.1:8000/storage/{{ $hospital->profile_picture }}" alt="Hospital Profile Picture" style="max-width: 120px; border-radius: 8px;"></div>
                    @endif
                    <input type="file" id="hospital_profile_picture" name="hospital_profile_picture" class="form-control" accept="image/*">
                </div>
                <button type="submit" class="btn btn-primary">Update Hospital Information</button>
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
@endsection 