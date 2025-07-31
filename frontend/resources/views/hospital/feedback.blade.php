@extends('layouts.app')

@section('title', 'Feedback - Hospital Dashboard')

@section('content')
<div class="container">
    <!-- Feedback Form: Blue, with texture -->
    <div class="feedback-section section-blue section-texture" style="padding:2.5rem 1.5rem 2rem 1.5rem;border-radius:1.2rem;margin-bottom:2rem;color:var(--primary-color);">
        <div class="card-header" style="background:transparent;border:none;padding:0;margin-bottom:1.2rem;display:flex;align-items:center;justify-content:center;gap:0.7rem;">
            <i class="fas fa-comment-dots" style="font-size:1.5rem;"></i>
            <h2 style="margin:0; color:var(--primary-color);">Share Your Feedback</h2>
        </div>
        <div class="card-body" style="background:rgba(255,255,255,0.08);border-radius:0.7rem;padding:1.5rem;margin-top:1.2rem;">
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif
            <form method="POST" action="{{ route('hospital.feedback.store') }}">
                @csrf
                <div class="form-group">
                    <label for="comments" class="form-label">Your Feedback</label>
                    <textarea 
                        name="comments" 
                        id="comments" 
                        rows="6" 
                        class="form-control @error('comments') is-invalid @enderror" 
                        placeholder="Please share your thoughts about our platform, suggestions for improvement, or any issues you've encountered..."
                        required
                    >{{ old('comments') }}</textarea>
                    @error('comments')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                    <small class="form-text text-muted" style="color:black;">
                        Minimum 10 characters, maximum 1000 characters.
                    </small>
                </div>
                <div class="form-group mt-4" style="display:flex;gap:1rem;justify-content:center;align-items:center;">
                    <button type="submit" class="btn btn-light" style="background:white;color:var(--primary-color);font-weight:600;"><i class="fas fa-paper-plane"></i> Submit Feedback</button>
                    <a href="{{ route('hospital.dashboard') }}" class="btn btn-outline-light" style="border:2px solid #fff;color:var(--primary-color);font-weight:600;"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
                </div>
            </form>
        </div>
    </div>
    <!-- Guidelines: White -->
    <div class="feedback-section section-white" style="background:white;padding:2rem 1.5rem;border-radius:1.2rem;margin-bottom:2rem;box-shadow:0 4px 24px 0 rgba(0,0,0,0.08);">
        <div class="card-header" style="display:flex;align-items:center;gap:0.7rem;background:transparent;border:none;padding:0;margin-bottom:1.2rem;">
            <i class="fas fa-info-circle" style="font-size:1.3rem;color:var(--primary-color);"></i>
            <h5 style="margin:0;">Feedback Guidelines</h5>
        </div>
        <ul class="list-unstyled mb-0">
            <li><i class="fas fa-check text-success me-2"></i> Be specific about your experience</li>
            <li><i class="fas fa-check text-success me-2"></i> Share suggestions for platform improvements</li>
            <li><i class="fas fa-check text-success me-2"></i> Report any technical issues you encounter</li>
            <li><i class="fas fa-check text-success me-2"></i> Provide constructive feedback</li>
        </ul>
    </div>
    <!-- Smiley Reaction Ranking: Blue, with texture -->
    <div class="feedback-section section-blue section-texture" style="padding:1.5rem 1.5rem 2rem 1.5rem;border-radius:1.2rem;margin-bottom:2rem;color:black;display:flex;align-items:center;justify-content:center;flex-direction:column;">
        <div style="display:flex;gap:2.5rem;justify-content:center;align-items:center;margin-bottom:1rem;">
            <div style="text-align:center;">
                <span style="font-size:2.5rem;display:block;">😡</span>
                <div style="font-size:1rem;margin-top:0.3rem;">Very Dissatisfied</div>
            </div>
            <div style="text-align:center;">
                <span style="font-size:2.5rem;display:block;">😞</span>
                <div style="font-size:1rem;margin-top:0.3rem;">Dissatisfied</div>
            </div>
            <div style="text-align:center;">
                <span style="font-size:2.5rem;display:block;">😐</span>
                <div style="font-size:1rem;margin-top:0.3rem;">Neutral</div>
            </div>
            <div style="text-align:center;">
                <span style="font-size:2.5rem;display:block;">😊</span>
                <div style="font-size:1rem;margin-top:0.3rem;">Satisfied</div>
            </div>
            <div style="text-align:center;">
                <span style="font-size:2.5rem;display:block;">😍</span>
                <div style="font-size:1rem;margin-top:0.3rem;">Very Satisfied</div>
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
.btn-light {
    background: #fff;
    color: var(--primary-color);
    border: none;
    font-weight: 600;
    border-radius: 0.5rem;
    padding: 0.7rem 1.5rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.07);
    transition: background 0.2s, color 0.2s;
}
.btn-light:hover {
    background: var(--secondary-color);
    color: #fff;
}
.btn-outline-light {
    background: transparent;
    color: #fff;
    border: 2px solid #fff;
    font-weight: 600;
    border-radius: 0.5rem;
    padding: 0.7rem 1.5rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.07);
    transition: background 0.2s, color 0.2s;
}
.btn-outline-light:hover {
    background: #fff;
    color: var(--primary-color);
}
</style>
@endsection 