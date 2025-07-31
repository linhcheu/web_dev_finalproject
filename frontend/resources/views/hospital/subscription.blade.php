@extends('layouts.app')

@section('title', 'Hospital Subscription - CareConnect')

@section('content')
<div class="hospital-subscription-container">
    <div class="container">
        <div class="subscription-header">
            <h1>Subscription Management</h1>
            <p>View and manage your hospital's subscription plan</p>
        </div>
        <!-- Image Placeholder: Add your static image here -->
        <div class="image-placeholder" style="width:100%;max-width:420px;height:180px;margin:0 auto 2rem auto;display:flex;align-items:center;justify-content:center;background:linear-gradient(135deg,#e0e7ef 60%,#f8fafc 100%);border-radius:1rem;box-shadow:0 2px 12px rgba(0,0,0,0.07);color:#b0b7c3;font-size:1.1rem;">
            <!-- Example: <img src="{{ asset('images/subscription-hero.png') }}" alt="Subscription Image" style="max-width:100%;max-height:100%;border-radius:1rem;" /> -->
            <span>Image Placeholder</span>
        </div>

        <div class="subscription-content">
            @if($subscription)
                <div class="subscription-card">
                    <h2>Current Plan</h2>
                    <div class="plan-details">
                        <p><strong>Plan Type:</strong> {{ $subscription->plan_type ?? 'N/A' }}</p>
                        <p><strong>Status:</strong> <span class="status-badge status-{{ $subscription->status ?? 'inactive' }}">{{ ucfirst($subscription->status ?? 'inactive') }}</span></p>
                        <p><strong>Start Date:</strong> {{ $subscription->start_date ? $subscription->start_date->format('M d, Y') : 'N/A' }}</p>
                        <p><strong>End Date:</strong> {{ $subscription->end_date ? $subscription->end_date->format('M d, Y') : 'N/A' }}</p>
                    </div>
                </div>

                @if($subscription->end_date && $subscription->end_date->diffInDays(now()) <= 3 && $subscription->status === 'active')
                    <div class="alert alert-warning" style="margin-bottom:1rem;">Your subscription will expire in {{ $subscription->end_date->diffInDays(now()) }} day(s). Please renew to avoid interruption.</div>
                @endif
            @else
                <div class="subscription-card">
                    <h2>No Active Subscription</h2>
                    <div class="plan-details">
                        <p>You don't have an active subscription plan. Please select a plan below to get started.</p>
                    </div>
                </div>
            @endif

            <div class="subscription-actions">
                <h3>Upgrade or Renew Plan</h3>
                <form method="POST" action="{{ route('hospital.subscription.update') }}" class="upgrade-form" id="subscriptionForm">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <label for="plan_type" class="form-label">Select Plan</label>
                        <select id="plan_type" name="plan_type" class="form-control" required>
                            <option value="basic" @if(($subscription->plan_type ?? '') == 'basic') selected @endif>Basic</option>
                            <option value="premium" @if(($subscription->plan_type ?? '') == 'premium') selected @endif>Premium</option>
                            <option value="enterprise" @if(($subscription->plan_type ?? '') == 'enterprise') selected @endif>Enterprise</option>
                        </select>
                    </div>
                    <button type="button" class="btn btn-primary mt-2" onclick="showPaymentModal(); return false;">Proceed to Payment</button>
                </form>
                @if($subscription && $subscription->status === 'active')
                <form method="POST" action="{{ route('hospital.subscription.cancel') }}" style="margin-top:1rem;">
                    @csrf
                    @method('PUT')
                    <button type="submit" class="btn btn-danger">Cancel Subscription</button>
                </form>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Payment Modal -->
<div id="paymentModal" class="modal" style="display:none; align-items: flex-end; justify-content: center;">
    <div class="modal-content slide-up-modal" style="max-width: 400px; width: 100vw; padding: 2.5rem 2rem; border-radius: 1.2rem 1.2rem 0 0; box-shadow: 0 -8px 32px rgba(0,0,0,0.15);">
        <span class="close" onclick="closePaymentModal()" style="position: absolute; right: 1.5rem; top: 1.5rem; font-size: 2rem; cursor: pointer;">&times;</span>
        <h2 style="margin-bottom: 1.5rem; color: var(--primary-color); font-weight: 700;">Visa Card Payment (Simulated)</h2>
        <form id="fakePaymentForm" onsubmit="event.preventDefault(); confirmPayment();">
            <div class="form-group">
                <label for="card_number" class="form-label">Card Number</label>
                <input type="text" id="card_number" name="card_number" class="form-control" maxlength="19" placeholder="4111 1111 1111 1111" required style="font-size: 1.1rem;">
            </div>
            <div class="form-row" style="display: flex; gap: 1rem;">
                <div class="form-group" style="flex:1;">
                    <label for="expiry" class="form-label">Expiry</label>
                    <input type="text" id="expiry" name="expiry" class="form-control" maxlength="5" placeholder="MM/YY" required style="font-size: 1.1rem;">
                </div>
                <div class="form-group" style="flex:1;">
                    <label for="cvv" class="form-label">CVV</label>
                    <input type="text" id="cvv" name="cvv" class="form-control" maxlength="4" placeholder="123" required style="font-size: 1.1rem;">
                </div>
            </div>
            <div class="form-group">
                <label for="card_name" class="form-label">Name on Card</label>
                <input type="text" id="card_name" name="card_name" class="form-control" placeholder="Cardholder Name" required style="font-size: 1.1rem;">
            </div>
            <div id="paymentSuccessMsg" style="display:none; color: #16a34a; font-weight: bold; margin-bottom: 1rem;">Payment Successful!</div>
            <button type="submit" class="btn btn-success" style="width: 100%; margin-bottom: 0.5rem; background: #16a34a; color: #fff; font-size: 1.1rem;">Pay Now</button>
            <button type="button" class="btn btn-secondary" onclick="closePaymentModal()" style="width: 100%; background: #e5e7eb; color: #374151; font-size: 1.1rem;">Cancel</button>
        </form>
    </div>
</div>
@endsection

@section('styles')
<style>
.hospital-subscription-container {
    padding: 2rem 0;
    background: linear-gradient(135deg, #f8fafc 0%, #e0e7ef 100%);
    min-height: 100vh;
}
.subscription-header {
    text-align: center;
    margin-bottom: 2rem;
}
.subscription-header h1 {
    color: var(--dark-color);
    margin-bottom: 0.5rem;
    font-weight: 500;
    letter-spacing: 0.01em;
}
.subscription-header p {
    color: #6b7280;
    font-size: 1.125rem;
    font-weight: 400;
}
.subscription-content {
    display: flex;
    flex-direction: column;
    gap: 2rem;
    max-width: 600px;
    margin: 0 auto;
}
.subscription-card {
    background: linear-gradient(135deg, #fff 80%, #f3f4f6 100%);
    border-radius: 0.75rem;
    box-shadow: 0 4px 24px 0 rgba(0, 0, 0, 0.08), 0 1.5px 4px 0 rgba(0,188,212,0.07);
    padding: 2rem;
    border: 1.5px solid #e0e7ef;
    position: relative;
    overflow: hidden;
}
.plan-details p {
    margin: 0.5rem 0;
    color: var(--dark-color);
    font-weight: 400;
}
.status-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 1rem;
    font-size: 0.85rem;
    font-weight: 500;
    display: inline-block;
}
.status-active {
    background: #d1fae5;
    color: #065f46;
}
.status-inactive {
    background: #fee2e2;
    color: #b91c1c;
}
.subscription-actions {
    background: linear-gradient(135deg, #fff 80%, #f3f4f6 100%);
    border-radius: 0.75rem;
    box-shadow: 0 4px 24px 0 rgba(0, 0, 0, 0.08), 0 1.5px 4px 0 rgba(0,188,212,0.07);
    padding: 2rem;
    border: 1.5px solid #e0e7ef;
    position: relative;
    overflow: hidden;
}
.form-group {
    margin-bottom: 1.5rem;
}
.form-label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 500;
    color: var(--dark-color);
}
.form-control {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid var(--border-color);
    border-radius: 0.375rem;
    font-size: 0.875rem;
    transition: border-color 0.3s ease;
    font-weight: 400;
}
.form-control:focus {
    outline: none;
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
}
.btn {
    padding: 0.5rem 1.5rem;
    border-radius: 0.375rem;
    font-size: 1rem;
    font-weight: 500;
    border: none;
    cursor: pointer;
}
.btn-primary {
    background: var(--primary-color);
    color: white;
}
.mt-2 {
    margin-top: 1rem;
}
@media (max-width: 768px) {
    .subscription-content, .subscription-card, .subscription-actions {
        padding: 1rem;
    }
}
</style>
@endsection

@section('scripts')
@parent
<script>
// Modal styling (for demo)
const modalStyle = document.createElement('style');
modalStyle.innerHTML = `
.modal { position: fixed; z-index: 9999; left: 0; top: 0; width: 100vw; height: 100vh; overflow: auto; background: rgba(0,0,0,0.4); display: flex; align-items: flex-end; justify-content: center; }
.slide-up-modal { background: #fff; padding: 2.5rem 2rem; border-radius: 1.2rem 1.2rem 0 0; box-shadow: 0 -8px 32px rgba(0,0,0,0.15); max-width: 400px; width: 100vw; position: relative; transform: translateY(100%); opacity: 0; transition: transform 0.4s cubic-bezier(.4,2,.6,1), opacity 0.3s; }
.modal.show .slide-up-modal { transform: translateY(0); opacity: 1; }
.close { position: absolute; right: 1.5rem; top: 1.5rem; font-size: 2rem; cursor: pointer; }
.btn-success { background: #16a34a; color: #fff; margin-bottom: 0.5rem; width: 100%; font-size: 1.1rem; }
.btn-secondary { background: #e5e7eb; color: #374151; width: 100%; font-size: 1.1rem; }
.form-row { display: flex; gap: 1rem; }
@media (max-width: 600px) {
  .slide-up-modal { padding: 1.2rem 0.5rem; }
}
`;
document.head.appendChild(modalStyle);

// Show/hide modal with animation
function showPaymentModal() {
    const modal = document.getElementById('paymentModal');
    modal.style.display = 'flex';
    setTimeout(() => modal.classList.add('show'), 10);
    document.getElementById('fakePaymentForm').reset();
    document.getElementById('paymentSuccessMsg').style.display = 'none';
}
function closePaymentModal() {
    const modal = document.getElementById('paymentModal');
    modal.classList.remove('show');
    setTimeout(() => { modal.style.display = 'none'; }, 400);
    document.getElementById('paymentSuccessMsg').style.display = 'none';
}
function confirmPayment() {
    document.getElementById('paymentSuccessMsg').style.display = 'block';
    setTimeout(function() {
        closePaymentModal();
        document.getElementById('subscriptionForm').submit();
    }, 1200);
}
</script>
@endsection 