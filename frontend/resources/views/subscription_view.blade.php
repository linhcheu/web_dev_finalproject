@extends('layouts.app')

@section('title', 'Hospital Subscription Plans - CareConnect')

@section('content')
<div class="subscription-container section-blue section-texture" style="min-height:100vh;background:linear-gradient(135deg, var(--primary-color), var(--secondary-color));padding:3rem 0;display:flex;align-items:center;justify-content:center;">
    <div class="container">
        <div class="subscription-header" style="text-align:center;margin-bottom:2.5rem;">
            <h1 style="color:var(--light-color);font-size:2.2rem;font-weight:700;margin-bottom:0.5rem;"><i class="fas fa-credit-card" style="margin-right:0.7rem;"></i>Hospital Subscription Plans</h1>
            <p style="color:#fff;font-size:1.15rem;font-weight:500;">Choose the best plan for your hospital and unlock more features!</p>
        </div>
        <div class="plans-grid" style="display:flex;gap:2.5rem;justify-content:center;flex-wrap:wrap;">
            <!-- Basic Plan -->
            <div class="plan-card plan-basic section-white" style="background:rgba(255,255,255,0.85);backdrop-filter:blur(8px);border-radius:1.5rem;box-shadow:0 8px 32px rgba(0,0,0,0.10);padding:2.5rem 2rem;min-width:280px;max-width:340px;flex:1 1 300px;display:flex;flex-direction:column;align-items:center;transition:transform 0.2s, box-shadow 0.2s;border-left:6px solid #3b82f6;">
                <h2 style="font-size:1.5rem;font-weight:700;color:var(--primary-color);margin-bottom:1rem;"><i class="fas fa-leaf"></i> Basic</h2>
                <div class="plan-price" style="font-size:2.2rem;font-weight:800;color:var(--primary-color);margin-bottom:1.5rem;">$99<span style="font-size:1rem;color:#64748b;font-weight:400;">/1 month</span></div>
                <ul class="plan-features" style="list-style:none;padding:0;margin:0;color:var(--dark-color);font-size:1.08rem;">
                    <li><i class="fas fa-check-circle" style="color:#3b82f6;margin-right:0.5rem;"></i> Listing in hospital directory</li>
                    <li><i class="fas fa-check-circle" style="color:#3b82f6;margin-right:0.5rem;"></i> Appointment management</li>
                    <li><i class="fas fa-check-circle" style="color:#3b82f6;margin-right:0.5rem;"></i> Analytics dashboard</li>
                </ul>
            </div>
            <!-- Premium Plan -->
            <div class="plan-card plan-premium section-white" style="background:rgba(255,255,255,0.85);backdrop-filter:blur(8px);border-radius:1.5rem;box-shadow:0 8px 32px rgba(0,0,0,0.10);padding:2.5rem 2rem;min-width:280px;max-width:340px;flex:1 1 300px;display:flex;flex-direction:column;align-items:center;transition:transform 0.2s, box-shadow 0.2s;border-left:6px solid #f59e42;">
                <h2 style="font-size:1.5rem;font-weight:700;color:var(--primary-color);margin-bottom:1rem;"><i class="fas fa-star"></i> Premium</h2>
                <div class="plan-price" style="font-size:2.2rem;font-weight:800;color:var(--primary-color);margin-bottom:1.5rem;">$199<span style="font-size:1rem;color:#64748b;font-weight:400;">/3 months</span></div>
                <ul class="plan-features" style="list-style:none;padding:0;margin:0;color:var(--dark-color);font-size:1.08rem;">
                    <li><i class="fas fa-check-circle" style="color:#f59e42;margin-right:0.5rem;"></i> Listing in hospital directory</li>
                    <li><i class="fas fa-check-circle" style="color:#f59e42;margin-right:0.5rem;"></i> Appointment management</li>
                    <li><i class="fas fa-check-circle" style="color:#f59e42;margin-right:0.5rem;"></i> Analytics dashboard</li>
                </ul>
            </div>
            <!-- Enterprise Plan -->
            <div class="plan-card plan-enterprise section-white" style="background:rgba(255,255,255,0.85);backdrop-filter:blur(8px);border-radius:1.5rem;box-shadow:0 8px 32px rgba(0,0,0,0.10);padding:2.5rem 2rem;min-width:280px;max-width:340px;flex:1 1 300px;display:flex;flex-direction:column;align-items:center;transition:transform 0.2s, box-shadow 0.2s;border-left:6px solid #8b5cf6;">
                <h2 style="font-size:1.5rem;font-weight:700;color:var(--primary-color);margin-bottom:1rem;"><i class="fas fa-crown"></i> Enterprise</h2>
                <div class="plan-price" style="font-size:2.2rem;font-weight:800;color:var(--primary-color);margin-bottom:1.5rem;">$399<span style="font-size:1rem;color:#64748b;font-weight:400;">/6 months</span></div>
                <ul class="plan-features" style="list-style:none;padding:0;margin:0;color:var(--dark-color);font-size:1.08rem;">
                    <li><i class="fas fa-check-circle" style="color:#8b5cf6;margin-right:0.5rem;"></i> Listing in hospital directory</li>
                    <li><i class="fas fa-check-circle" style="color:#8b5cf6;margin-right:0.5rem;"></i> Appointment management</li>
                    <li><i class="fas fa-check-circle" style="color:#8b5cf6;margin-right:0.5rem;"></i> Analytics dashboard</li>
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
@media (max-width: 900px) {
    .plans-grid {
        flex-direction: column;
        align-items: center;
    }
    .plan-card {
        max-width: 98vw;
        min-width: 220px;
    }
}
</style>
@endsection
