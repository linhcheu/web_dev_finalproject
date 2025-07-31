@extends('layouts.app')

@section('title', 'About Us - CareConnect')

@section('content')
<div class="about-container section-blue section-texture" style="padding:2rem 0;background:linear-gradient(135deg, var(--primary-color), var(--secondary-color)), url('https://www.transparenttextures.com/patterns/cubes.png');background-blend-mode:lighten;min-height:100vh;">
    <div class="container">
        <div class="about-header" style="text-align:center;margin-bottom:3rem;">
            <h1 style="color:white;margin-bottom:0.5rem;font-weight:600;letter-spacing:0.01em;"><i class="fas fa-info-circle" style="margin-right:0.7rem;"></i>About CareConnect</h1>
            <p style="color:#fff;font-size:1.125rem;font-weight:400;">Connecting healthcare providers with patients for better healthcare access</p>
        </div>
        <div style="width:100%;max-width:420px;margin:0 auto 2rem auto;">
            <img src="{{ asset('images/about-hero.png') }}" alt="About Image" style="width:100%;height:auto;display:block;border-radius:1rem;box-shadow:0 2px 12px rgba(0,0,0,0.07);" />
        </div>
        <div class="about-content" style="max-width:800px;margin:0 auto;">
            <div class="about-section section-white" style="background:white;backdrop-filter:blur(8px);padding:2rem;border-radius:0.75rem;box-shadow:0 4px 24px 0 rgba(0,0,0,0.08),0 1.5px 4px 0 rgba(0,188,212,0.07);margin-bottom:2rem;border:1.5px solid #e0e7ef;position:relative;overflow:hidden;">
                <h2 style="color:var(--primary-color);margin-bottom:1rem;font-weight:600;letter-spacing:0.01em;"><i class="fas fa-bullseye"></i> Our Mission</h2>
                <p style="color:#64748b;line-height:1.6;margin-bottom:1rem;font-weight:400;">CareConnect is dedicated to bridging the gap between healthcare providers and patients by providing a secure, efficient, and user-friendly platform for appointment booking and healthcare management.</p>
            </div>
            <div class="about-section section-white" style="background:white;backdrop-filter:blur(8px);padding:2rem;border-radius:0.75rem;box-shadow:0 4px 24px 0 rgba(0,0,0,0.08),0 1.5px 4px 0 rgba(0,188,212,0.07);margin-bottom:2rem;border:1.5px solid #e0e7ef;position:relative;overflow:hidden;">
                <h2 style="color:var(--primary-color);margin-bottom:1rem;font-weight:600;letter-spacing:0.01em;"><i class="fas fa-hands-helping"></i> What We Do</h2>
                <p style="color:#64748b;line-height:1.6;margin-bottom:1rem;font-weight:400;">We provide a comprehensive healthcare platform that allows patients to easily find and book appointments with trusted hospitals and medical facilities. Our platform also enables hospitals to manage their appointments and patient interactions efficiently.</p>
            </div>
            <div class="about-section section-white" style="background: white;backdrop-filter:blur(8px);padding:2rem;border-radius:0.75rem;box-shadow:0 4px 24px 0 rgba(0,0,0,0.08),0 1.5px 4px 0 rgba(0,188,212,0.07);margin-bottom:2rem;border:1.5px solid #e0e7ef;position:relative;overflow:hidden;">
                <h2 style="color:var(--primary-color);margin-bottom:1rem;font-weight:600;letter-spacing:0.01em;"><i class="fas fa-envelope"></i> Contact Us</h2>
                <p style="color:#64748b;line-height:1.6;margin-bottom:1rem;font-weight:400;">For any inquiries or support, please reach out to us:</p>
                <ul style="list-style:none;padding:0;">
                    <li style="padding:0.5rem 0;color:#64748b;font-weight:400;"><strong style="font-weight:500;color:var(--primary-color);"><i class="fas fa-envelope"></i> Email:</strong> haha@careconnect.com</li>
                    <li style="padding:0.5rem 0;color:#64748b;font-weight:400;"><strong style="font-weight:500;color:var(--primary-color);"><i class="fas fa-phone"></i> Phone:</strong> +99 999999</li>
                    <li style="padding:0.5rem 0;color:#64748b;font-weight:400;"><strong style="font-weight:500;color:var(--primary-color);"><i class="fas fa-map-marker-alt"></i> Address:</strong> Paragon.U</li>
                </ul>
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
@media (max-width: 700px) {
    .about-header, .about-section {
        padding: 1.2rem 0.5rem !important;
    }
}
</style>
@endsection 