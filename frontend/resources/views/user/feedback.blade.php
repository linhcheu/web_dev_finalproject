@extends('layouts.app')

@section('title', 'Feedback - CareConnect')

@section('content')
<div class="feedback-container">
    <div class="container">
        <!-- Header: Blue, with texture -->
        <div class="feedback-header section-blue section-texture" style="text-align:center;margin-bottom:2rem;padding:2.5rem 1.5rem 2rem 1.5rem;border-radius:1.2rem;color:white;">
            <h1 style="color:var(--primary-color);margin-bottom:0.5rem;"><i class="fas fa-comment-dots" style="margin-right:0.7rem;"></i>Share Your Feedback</h1>
            <p style="color:black;font-size:1.125rem;">Help us improve CareConnect by sharing your experience</p>
        </div>
        <div class="feedback-content" style="display:grid;grid-template-columns:2fr 1fr;gap:2rem;max-width:1200px;margin:0 auto;">
            <!-- Feedback Form: White, glassmorphism -->
            <div class="feedback-form-section section-white" style="background:rgba(255,255,255,0.85);backdrop-filter:blur(8px);border-radius:1.2rem;box-shadow:0 4px 24px 0 rgba(0,0,0,0.10);padding:2.5rem 2rem;">
                <div class="form-card" style="background:transparent;box-shadow:none;padding:0;">
                    <div class="form-header" style="text-align:center;margin-bottom:2rem;">
                        <h2 style="color:var(--primary-color);margin-bottom:0.5rem;"><i class="fas fa-paper-plane"></i> Submit Feedback</h2>
                        <p style="color:#64748b;">We value your opinion and use it to improve our platform</p>
                    </div>
                    @if(session('success'))
                        <div class="alert alert-success" style="padding:0.7rem;border-radius:0.5rem;margin-bottom:1rem;background:#d1fae5;border:1px solid #a7f3d0;color:#065f46;box-shadow:0 2px 8px rgba(16,185,129,0.07);">{{ session('success') }}</div>
                    @endif
                    @if($errors->any())
                        <div class="alert alert-danger" style="padding:0.7rem;border-radius:0.5rem;margin-bottom:1rem;background:#fef2f2;border:1px solid #fecaca;color:#dc2626;box-shadow:0 2px 8px rgba(239,68,68,0.07);">
                            <ul style="margin:0;padding-left:1.2rem;">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <form method="POST" action="{{ route('feedback.store') }}" class="feedback-form">
                        @csrf
                        <div class="form-group" style="margin-bottom:1.2rem;">
                            <label for="comments" class="form-label" style="font-weight:600;"><i class="fas fa-comment"></i> Your Feedback</label>
                            <textarea id="comments" name="comments" class="form-control" rows="6" placeholder="Please share your experience with CareConnect, suggestions for improvement, or any issues you encountered..." required style="width:100%;padding:0.7rem 1rem;border:1.5px solid #e5e7eb;border-radius:0.7rem;font-size:1rem;box-shadow:0 1.5px 6px 0 rgba(0,0,0,0.03);resize:vertical;">{{ old('comments') }}</textarea>
                            <small class="form-text" style="color:#64748b;">Your feedback helps us improve the platform for everyone</small>
                        </div>
                        <div class="form-actions" style="display:flex;gap:1.2rem;justify-content:center;align-items:center;margin-top:2rem;">
                            <button type="submit" class="btn btn-primary" style="font-size:1.1rem;padding:0.7em 2.2em;border-radius:2em;box-shadow:0 2px 8px rgba(37,99,235,0.07);font-weight:600;transition:transform 0.12s;"><i class="fas fa-paper-plane"></i> Submit Feedback</button>
                        </div>
                    </form>
                </div>
            </div>
            <!-- Info Section: Blue, with texture -->
            <div class="feedback-info-section section-blue section-texture" style="background:linear-gradient(135deg, var(--primary-color), var(--secondary-color));color:white;border-radius:1.2rem;box-shadow:0 4px 24px 0 rgba(0,0,0,0.10);padding:2.5rem 2rem;">
                <div class="info-card" style="background:rgba(255,255,255,0.08);border-radius:0.5rem;box-shadow:0 2px 8px rgba(0,0,0,0.07);padding:1.5rem;margin-bottom:1.5rem;">
                    <div class="info-header" style="margin-bottom:1.5rem;padding-bottom:1rem;border-bottom:1px solid #e5e7eb;">
                        <h3 style="color:white;margin:0;"><i class="fas fa-lightbulb"></i> Why Your Feedback Matters</h3>
                    </div>
                    <div class="info-content" style="display:flex;flex-direction:column;gap:1.5rem;">
                        <div class="info-item" style="display:flex;gap:1rem;align-items:flex-start;">
                            <i class="fas fa-lightbulb" style="color:var(--primary-color);"></i>
                            <div>
                                <h4 style="color:white;margin:0 0 0.5rem 0;font-size:1rem;">Improve Our Platform</h4>
                                <p style="color:#e0e7ef;margin:0;">Your suggestions help us enhance features and user experience</p>
                            </div>
                        </div>
                        <div class="info-item" style="display:flex;gap:1rem;align-items:flex-start;">
                            <i class="fas fa-users" style="color:var(--primary-color);"></i>
                            <div>
                                <h4 style="color:white;margin:0 0 0.5rem 0;font-size:1rem;">Help Other Users</h4>
                                <p style="color:#e0e7ef;margin:0;">Your feedback benefits the entire CareConnect community</p>
                            </div>
                        </div>
                        <div class="info-item" style="display:flex;gap:1rem;align-items:flex-start;">
                            <i class="fas fa-cog" style="color:var(--primary-color);"></i>
                            <div>
                                <h4 style="color:white;margin:0 0 0.5rem 0;font-size:1rem;">Shape Future Features</h4>
                                <p style="color:#e0e7ef;margin:0;">We use feedback to prioritize new features and improvements</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="info-card" style="background:rgba(255,255,255,0.08);border-radius:0.5rem;box-shadow:0 2px 8px rgba(0,0,0,0.07);padding:1.5rem;">
                    <div class="info-header" style="margin-bottom:1.5rem;padding-bottom:1rem;border-bottom:1px solid #e5e7eb;">
                        <h3 style="color:white;margin:0;"><i class="fas fa-list"></i> What to Include</h3>
                    </div>
                    <div class="info-content" style="display:flex;flex-direction:column;gap:1rem;">
                        <ul class="feedback-tips" style="color:#e0e7ef;">
                            <li>Your experience with the appointment booking process</li>
                            <li>Any issues you encountered while using the platform</li>
                            <li>Suggestions for new features or improvements</li>
                            <li>Feedback about the hospital selection and information</li>
                            <li>Overall satisfaction with the platform</li>
                        </ul>
                    </div>
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
.btn-primary {
    background: var(--primary-color);
    color: white;
    border: none;
    font-weight: 600;
    border-radius: 2em;
    padding: 0.7em 2em;
    box-shadow: 0 2px 8px rgba(0,0,0,0.07);
    transition: background 0.2s, color 0.2s;
}
.btn-primary:hover {
    background: var(--secondary-color);
    color: #fff;
}
.form-card input:focus, .form-card textarea:focus {
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
@media (max-width: 900px) {
    .feedback-content {
        grid-template-columns: 1fr;
    }
}
@media (max-width: 700px) {
    .feedback-header, .feedback-form-section, .feedback-info-section {
        padding: 1.2rem 0.5rem !important;
    }
    .form-actions {
        flex-direction: column !important;
        gap: 0.7rem !important;
    }
}
</style>
@endsection 