@extends('layouts.app')

@section('content')
    <style>
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
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

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #374151;
            font-size: 14px;
        }

        .form-group input, .form-group select, .form-group textarea {
            width: 100%;
            padding: 10px 15px;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .form-group textarea {
            min-height: 100px;
            resize: vertical;
        }

        .form-group input:focus, .form-group select:focus, .form-group textarea:focus {
            border-color: #667eea;
            outline: none;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-secondary {
            background: #6b7280;
            color: white;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
        }

        .page-title {
            text-align: center;
            margin-bottom: 30px;
            font-size: 24px;
            font-weight: 700;
            color: #374151;
        }

        .error {
            color: #ef4444;
            font-size: 12px;
            margin-top: 5px;
        }

        .form-section {
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .form-section-title {
            font-weight: 600;
            color: #374151;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #e5e7eb;
        }

        .checkbox-group {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }

        .checkbox-group input[type="checkbox"] {
            width: auto;
            margin-right: 10px;
        }
    </style>

    <div class="main">
        <div class="form-container">
            <h1 class="page-title">Create New Hospital</h1>

            @if(session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4">
                    {{ session('success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('hospital.store') }}" enctype="multipart/form-data">
                @csrf

                <div class="form-section">
                    <h2 class="form-section-title">Hospital Information</h2>
                    
                    <div class="form-group">
                        <label for="name">Hospital Name</label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" required>
                        @error('name')
                            <div class="error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="province">Province</label>
                        <select id="province" name="province" required>
                            <option value="">Select Province</option>
                            <option value="Phnom Penh" {{ old('province') == 'Phnom Penh' ? 'selected' : '' }}>Phnom Penh</option>
                            <option value="Banteay Meanchey" {{ old('province') == 'Banteay Meanchey' ? 'selected' : '' }}>Banteay Meanchey</option>
                            <option value="Battambang" {{ old('province') == 'Battambang' ? 'selected' : '' }}>Battambang</option>
                            <option value="Kampong Cham" {{ old('province') == 'Kampong Cham' ? 'selected' : '' }}>Kampong Cham</option>
                            <option value="Kampong Chhnang" {{ old('province') == 'Kampong Chhnang' ? 'selected' : '' }}>Kampong Chhnang</option>
                            <option value="Kampong Speu" {{ old('province') == 'Kampong Speu' ? 'selected' : '' }}>Kampong Speu</option>
                            <option value="Kampong Thom" {{ old('province') == 'Kampong Thom' ? 'selected' : '' }}>Kampong Thom</option>
                            <option value="Kampot" {{ old('province') == 'Kampot' ? 'selected' : '' }}>Kampot</option>
                            <option value="Kandal" {{ old('province') == 'Kandal' ? 'selected' : '' }}>Kandal</option>
                            <option value="Kep" {{ old('province') == 'Kep' ? 'selected' : '' }}>Kep</option>
                            <option value="Koh Kong" {{ old('province') == 'Koh Kong' ? 'selected' : '' }}>Koh Kong</option>
                            <option value="Kratie" {{ old('province') == 'Kratie' ? 'selected' : '' }}>Kratie</option>
                            <option value="Mondulkiri" {{ old('province') == 'Mondulkiri' ? 'selected' : '' }}>Mondulkiri</option>
                            <option value="Oddar Meanchey" {{ old('province') == 'Oddar Meanchey' ? 'selected' : '' }}>Oddar Meanchey</option>
                            <option value="Pailin" {{ old('province') == 'Pailin' ? 'selected' : '' }}>Pailin</option>
                            <option value="Preah Vihear" {{ old('province') == 'Preah Vihear' ? 'selected' : '' }}>Preah Vihear</option>
                            <option value="Prey Veng" {{ old('province') == 'Prey Veng' ? 'selected' : '' }}>Prey Veng</option>
                            <option value="Pursat" {{ old('province') == 'Pursat' ? 'selected' : '' }}>Pursat</option>
                            <option value="Ratanakiri" {{ old('province') == 'Ratanakiri' ? 'selected' : '' }}>Ratanakiri</option>
                            <option value="Siem Reap" {{ old('province') == 'Siem Reap' ? 'selected' : '' }}>Siem Reap</option>
                            <option value="Sihanoukville" {{ old('province') == 'Sihanoukville' ? 'selected' : '' }}>Sihanoukville</option>
                            <option value="Stung Treng" {{ old('province') == 'Stung Treng' ? 'selected' : '' }}>Stung Treng</option>
                            <option value="Svay Rieng" {{ old('province') == 'Svay Rieng' ? 'selected' : '' }}>Svay Rieng</option>
                            <option value="Takeo" {{ old('province') == 'Takeo' ? 'selected' : '' }}>Takeo</option>
                            <option value="Tbong Khmum" {{ old('province') == 'Tbong Khmum' ? 'selected' : '' }}>Tbong Khmum</option>
                            <option value="new">Add New Province</option>
                        </select>
                        @error('province')
                            <div class="error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group" id="new-province-group" style="display: none;">
                        <label for="new_province">New Province Name</label>
                        <input type="text" id="new_province" name="new_province" value="{{ old('new_province') }}">
                    </div>

                    <div class="form-group">
                        <label for="location">Location</label>
                        <input type="text" id="location" name="location" value="{{ old('location') }}" required>
                        @error('location')
                            <div class="error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="contact_info">Contact Information</label>
                        <textarea id="contact_info" name="contact_info" required>{{ old('contact_info') }}</textarea>
                        @error('contact_info')
                            <div class="error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="information">Additional Information</label>
                        <textarea id="information" name="information">{{ old('information') }}</textarea>
                        @error('information')
                            <div class="error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="profile_picture">Hospital Profile Picture</label>
                        <input type="file" id="profile_picture" name="profile_picture">
                        @error('profile_picture')
                            <div class="error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="status">Status</label>
                        <select id="status" name="status" required>
                            <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                        </select>
                        @error('status')
                            <div class="error">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-section">
                    <h2 class="form-section-title">Administrator Information</h2>
                    
                    <div class="form-group">
                        <label for="owner_id">Select Existing Hospital Admin</label>
                        <select id="owner_id" name="owner_id">
                            <option value="">None (Create without admin)</option>
                            @foreach($availableAdmins as $admin)
                                <option value="{{ $admin->user_id }}" {{ old('owner_id') == $admin->user_id ? 'selected' : '' }}>
                                    {{ $admin->first_name }} {{ $admin->last_name }} ({{ $admin->email }})
                                </option>
                            @endforeach
                        </select>
                        <p class="text-sm text-gray-600 mt-1">Note: Only hospital admins who don't already manage a hospital are listed.</p>
                        @error('owner_id')
                            <div class="error">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-section">
                    <h2 class="form-section-title">Subscription Information</h2>
                    
                    <div class="checkbox-group">
                        <input type="checkbox" id="create_subscription" name="create_subscription" value="1" {{ old('create_subscription') ? 'checked' : '' }}>
                        <label for="create_subscription">Create subscription for this hospital</label>
                    </div>
                    
                    <div id="subscription-details" style="{{ old('create_subscription') ? 'display: block;' : 'display: none;' }}">
                        <div class="form-group">
                            <label for="plan_type">Subscription Plan</label>
                            <select id="plan_type" name="plan_type">
                                <option value="basic" {{ old('plan_type') == 'basic' ? 'selected' : '' }}>Basic</option>
                                <option value="premium" {{ old('plan_type') == 'premium' ? 'selected' : '' }}>Premium</option>
                                <option value="enterprise" {{ old('plan_type') == 'enterprise' ? 'selected' : '' }}>Enterprise</option>
                            </select>
                            @error('plan_type')
                                <div class="error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="flex justify-end space-x-4 mt-6">
                    <a href="{{ route('hospital.index') }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Create Hospital</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Province dropdown logic
            const provinceSelect = document.getElementById('province');
            const newProvinceGroup = document.getElementById('new-province-group');
            
            provinceSelect.addEventListener('change', function() {
                if (this.value === 'new') {
                    newProvinceGroup.style.display = 'block';
                } else {
                    newProvinceGroup.style.display = 'none';
                }
            });
            
            // Subscription checkbox logic
            const createSubscriptionCheckbox = document.getElementById('create_subscription');
            const subscriptionDetails = document.getElementById('subscription-details');
            
            createSubscriptionCheckbox.addEventListener('change', function() {
                if (this.checked) {
                    subscriptionDetails.style.display = 'block';
                } else {
                    subscriptionDetails.style.display = 'none';
                }
            });
        });
    </script>
@endsection
