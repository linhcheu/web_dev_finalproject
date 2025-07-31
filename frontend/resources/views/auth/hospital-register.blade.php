@extends('layouts.app')

@section('title', 'Hospital Registration - CareConnect')

@section('content')
<div class="auth-container">
    <div class="container">
        <div class="auth-card hospital-register">
            <div class="auth-header">
                <h2>Hospital Registration</h2>
                <p>Register your hospital on CareConnect</p>
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

            <form method="POST" action="{{ route('hospital.register') }}" class="auth-form" id="hospitalRegisterForm" onsubmit="event.preventDefault(); showPaymentModal();">
                @csrf
                
                <div class="form-section">
                    <h4>Administrator Information</h4>
                    
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
                        <label for="email" class="form-label">Professional Email</label>
                        <input type="email" id="email" name="email" class="form-control" value="{{ old('email') }}" required>
                    </div>

                    <div class="form-group">
                        <label for="phone" class="form-label">Phone Number</label>
                        <input type="tel" id="phone" name="phone" class="form-control" value="{{ old('phone') }}" required>
                    </div>

                    <div class="form-group">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" id="password" name="password" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="password_confirmation" class="form-label">Confirm Password</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" required>
                    </div>
                </div>

                <div class="form-section">
                    <h4>Hospital Information</h4>
                    
                    <div class="form-group">
                        <label for="hospital_name" class="form-label">Hospital Name</label>
                        <input type="text" id="hospital_name" name="hospital_name" class="form-control" value="{{ old('hospital_name') }}" required>
                    </div>

                    <div class="form-group">
                        <label for="province" class="form-label">Province / City</label>
                        <select id="province" name="province" class="form-control" required>
                            <option value="">Select Province</option>
                            <option value="Banteay Meanchey">Banteay Meanchey</option>
                            <option value="Battambang">Battambang</option>
                            <option value="Kampong Cham">Kampong Cham</option>
                            <option value="Kampong Chhnang">Kampong Chhnang</option>
                            <option value="Kampong Speu">Kampong Speu</option>
                            <option value="Kampong Thom">Kampong Thom</option>
                            <option value="Kampot">Kampot</option>
                            <option value="Kandal">Kandal</option>
                            <option value="Kep">Kep</option>
                            <option value="Koh Kong">Koh Kong</option>
                            <option value="Kratie">Kratie</option>
                            <option value="Mondulkiri">Mondulkiri</option>
                            <option value="Oddar Meanchey">Oddar Meanchey</option>
                            <option value="Pailin">Pailin</option>
                            <option value="Preah Vihear">Preah Vihear</option>
                            <option value="Pursat">Pursat</option>
                            <option value="Prey Veng">Prey Veng</option>
                            <option value="Ratanakiri">Ratanakiri</option>
                            <option value="Siem Reap">Siem Reap</option>
                            <option value="Preah Sihanouk">Preah Sihanouk</option>
                            <option value="Stung Treng">Stung Treng</option>
                            <option value="Svay Rieng">Svay Rieng</option>
                            <option value="Takeo">Takeo</option>
                            <option value="Tbong Khmum">Tbong Khmum</option>
                            <option value="Phnom Penh">Phnom Penh</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="hospital_location" class="form-label">Hospital Location</label>
                        <textarea id="hospital_location" name="hospital_location" class="form-control" rows="3" required>{{ old('hospital_location') }}</textarea>
                    </div>

                    <div class="form-group">
                        <label for="contact_info" class="form-label">Contact Information</label>
                        <textarea id="contact_info" name="contact_info" class="form-control" rows="3" required>{{ old('contact_info') }}</textarea>
                    </div>

                    <div class="form-group">
                        <label for="information" class="form-label">Hospital Information (About)</label>
                        <textarea id="information" name="information" class="form-control" rows="4" required>{{ old('information') }}</textarea>
                    </div>

                    <div class="form-group">
                        <label for="subscription_plan" class="form-label">Subscription Plan</label>
                        <select id="subscription_plan" name="subscription_plan" class="form-select" required>
                            <option value="" >Select a plan</option>
                            <option value="basic" {{ old('subscription_plan') == 'basic' ? 'selected' : '' }}>Basic - $99/month</option>
                            <option value="premium" {{ old('subscription_plan') == 'premium' ? 'selected' : '' }}>Premium - $199/month</option>
                            <option value="enterprise" {{ old('subscription_plan') == 'enterprise' ? 'selected' : '' }}>Enterprise - $399/month</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary w-full">Register Hospital</button>
                </div>

            </form>

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

            <div class="auth-footer">
                <p>Already have an account? <a href="{{ route('login') }}" class="auth-link">Sign in</a></p>
                <p>Want to register as a user? <a href="{{ route('register') }}" class="auth-link">User Registration</a></p>
            </div>
        </div>
    </div>
</div>
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
    max-width: 600px;
    width: 100%;
}

.hospital-register {
    max-width: 700px;
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

.form-section {
    margin-bottom: 2rem;
    padding: 1.5rem;
    background: var(--light-color);
    border-radius: 0.5rem;
}

.form-section h4 {
    color: var(--primary-color);
    margin-bottom: 1rem;
    font-size: 1.125rem;
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

.form-control {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid var(--border-color);
    border-radius: 0.375rem;
    font-size: 0.875rem;
    transition: border-color 0.3s ease;
}

.form-control:focus {
    outline: none;
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
}

.form-select {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid var(--border-color);
    border-radius: 0.375rem;
    font-size: 0.875rem;
    background-color: var(--white-color);
    cursor: pointer;
}

textarea.form-control {
    resize: vertical;
    min-height: 80px;
}

@media (max-width: 768px) {
    .auth-card {
        margin: 1rem;
        padding: 2rem;
    }
    
    .form-row {
        grid-template-columns: 1fr;
    }
    
    .form-section {
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
        document.getElementById('hospitalRegisterForm').submit();
    }, 1200);
}
</script>
@endsection 