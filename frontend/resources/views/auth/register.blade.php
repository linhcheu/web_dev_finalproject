@extends('layouts.app')

@section('title', 'Register - CareConnect')

@section('content')
<div class="auth-container">
    <div class="container">
        <div class="auth-card">
            <div class="auth-header">
                <h2>Create Account</h2>
                <p>Join CareConnect as a user</p>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}" class="auth-form">
                @csrf
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="first_name" class="form-label">First Name</label>
                        <input type="text" id="first_name" name="first_name" class="form-control" value="{{ old('first_name') }}" required>
                    </div>

                    <div class="form-group">
                        <label for="last_name" class="form-label">Last Name</label>
                        <input type="text" id="last_name" name="last_name" class="form-control" value="{{ old('last_name') }}" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" id="email" name="email" class="form-control" value="{{ old('email') }}" required>
                </div>

                <div class="form-group">
                    <label for="phone" class="form-label">Phone Number</label>
                    <input type="tel" id="phone" name="phone" class="form-control" value="{{ old('phone') }}" required>
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <div class="password-input-container">
                        <input type="password" id="password" name="password" class="form-control" required>
                        <button type="button" class="password-toggle" onclick="togglePassword('password')">
                            <i class="fas fa-eye" id="password-icon"></i>
                        </button>
                    </div>
                </div>

                <div class="form-group">
                    <label for="password_confirmation" class="form-label">Confirm Password</label>
                    <div class="password-input-container">
                        <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" required>
                        <button type="button" class="password-toggle" onclick="togglePassword('password_confirmation')">
                            <i class="fas fa-eye" id="password_confirmation-icon"></i>
                        </button>
                    </div>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary w-full">Create Account</button>
                </div>

               
            </form>

            <div class="auth-footer">
                <p>Already have an account? <a href="{{ route('login') }}" class="auth-link">Sign in</a></p>
                <p>Want to register a hospital? <a href="{{ route('hospital.register') }}" class="auth-link">Hospital Registration</a></p>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function togglePassword(fieldId) {
    const passwordField = document.getElementById(fieldId);
    const icon = document.getElementById(fieldId + '-icon');
    
    if (passwordField.type === 'password') {
        passwordField.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        passwordField.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}
</script>
@endsection

@section('styles')
<style>
.auth-container {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    padding: 2rem 0;
}

.auth-card {
    background: white;
    border-radius: 1rem;
    box-shadow: var(--shadow-lg);
    padding: 3rem;
    max-width: 500px;
    width: 100%;
}

.auth-header {
    text-align: center;
    margin-bottom: 2rem;
}

.auth-header h2 {
    color: var(--dark-color);
    margin-bottom: 0.5rem;
}

.auth-header p {
    color: var(--gray-color);
}

.auth-form {
    margin-bottom: 2rem;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
}

.auth-form .form-group {
    margin-bottom: 1.5rem;
}

.auth-form .btn {
    width: 100%;
    padding: 1rem;
    font-size: 1rem;
}

.google-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
}

.auth-divider {
    text-align: center;
    margin: 1.5rem 0;
    position: relative;
}

.auth-divider::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 0;
    right: 0;
    height: 1px;
    background: var(--border-color);
}

.auth-divider span {
    background: white;
    padding: 0 1rem;
    color: var(--gray-color);
    font-size: 0.875rem;
}

.auth-footer {
    text-align: center;
    color: var(--gray-color);
}

.auth-footer p {
    margin-bottom: 0.5rem;
}

.auth-link {
    color: var(--primary-color);
    text-decoration: none;
    font-weight: 500;
}

.auth-link:hover {
    text-decoration: underline;
}

.alert {
    padding: 1rem;
    border-radius: 0.375rem;
    margin-bottom: 1.5rem;
}

.alert-danger {
    background-color: #fef2f2;
    border: 1px solid #fecaca;
    color: #dc2626;
}

.alert ul {
    margin: 0;
    padding-left: 1.5rem;
}

.w-full {
    width: 100%;
}

.password-input-container {
    position: relative;
    display: flex;
    align-items: center;
}

.password-input-container .form-control {
    padding-right: 3rem;
}

.password-toggle {
    position: absolute;
    right: 0.75rem;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    color: var(--gray-color);
    cursor: pointer;
    padding: 0.25rem;
    border-radius: 0.25rem;
    transition: color 0.3s ease;
}

.password-toggle:hover {
    color: var(--primary-color);
}

.password-toggle:focus {
    outline: none;
    color: var(--primary-color);
}

@media (max-width: 768px) {
    .auth-card {
        margin: 1rem;
        padding: 2rem;
    }
    
    .form-row {
        grid-template-columns: 1fr;
    }
}

.btn-google {
    background: #fff;
    color: #444;
    border: 1px solid #ddd;
    border-radius: 0.375rem;
    font-weight: 500;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    width: 100%;
    padding: 0.75rem 1rem;
    margin-bottom: 1.5rem;
    transition: box-shadow 0.2s, border-color 0.2s;
}
.btn-google:hover {
    border-color: #4285f4;
    box-shadow: 0 2px 8px rgba(66,133,244,0.08);
    color: #4285f4;
}
</style>
@endsection 