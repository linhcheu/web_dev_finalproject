@extends('layouts.app')

@section('content')
<style>
    .sub-edit { padding: 30px; max-width: 760px; }
    .sub-edit .card { background: #fff; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,.05); padding: 24px; }
    .sub-edit h2 { margin-bottom: 4px; }
    .sub-edit .muted { color: #6b7280; margin-bottom: 20px; }
    .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
    .form-group label { display: block; font-weight: 600; margin-bottom: 6px; font-size: 14px; }
    .form-group input, .form-group select { width: 100%; padding: 10px 12px; border: 1.5px solid #e5e7eb; border-radius: 10px; font-size: 15px; }
    .form-group .error { color: #b91c1c; font-size: 13px; margin-top: 4px; }
    .checkbox { display: flex; align-items: center; gap: 8px; margin-top: 16px; }
    .actions { display: flex; gap: 12px; margin-top: 24px; flex-wrap: wrap; }
    .actions a, .actions button { padding: 10px 20px; border-radius: 10px; font-weight: 600; border: 0; cursor: pointer; text-decoration: none; }
    .btn-primary { background: #2563eb; color: #fff; }
    .btn-secondary { background: #f3f4f6; color: #111827; }
    @media (max-width: 640px) {
        .sub-edit { padding: 16px; }
        .form-grid { grid-template-columns: 1fr; }
    }
</style>

<div class="sub-edit">
    <div class="card">
        <h2>Subscription #{{ $subscription->subscription_id }}</h2>
        <p class="muted">{{ $subscription->hospital?->name ?? 'Unknown hospital' }}</p>

        <form method="POST" action="{{ route('subscription.update', $subscription->subscription_id) }}">
            @csrf
            @method('PUT')

            <div class="form-grid">
                <div class="form-group">
                    <label for="plan_type">Plan</label>
                    <select id="plan_type" name="plan_type" required>
                        @foreach(['basic', 'premium', 'enterprise'] as $plan)
                            <option value="{{ $plan }}" @selected(old('plan_type', $subscription->plan_type) === $plan)>{{ ucfirst($plan) }}</option>
                        @endforeach
                    </select>
                    @error('plan_type')<div class="error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label for="price">Price (USD)</label>
                    <input id="price" name="price" type="number" step="0.01" min="0" value="{{ old('price', $subscription->price) }}">
                    @error('price')<div class="error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label for="start_date">Start date</label>
                    <input id="start_date" name="start_date" type="date" required value="{{ old('start_date', $subscription->start_date?->toDateString()) }}">
                    @error('start_date')<div class="error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label for="end_date">End date</label>
                    <input id="end_date" name="end_date" type="date" required value="{{ old('end_date', $subscription->end_date?->toDateString()) }}">
                    @error('end_date')<div class="error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label for="status">Status</label>
                    <select id="status" name="status" required>
                        @foreach(['active', 'expired', 'cancelled'] as $status)
                            <option value="{{ $status }}" @selected(old('status', $subscription->status) === $status)>{{ ucfirst($status) }}</option>
                        @endforeach
                    </select>
                    @error('status')<div class="error">{{ $message }}</div>@enderror
                </div>
            </div>

            <label class="checkbox">
                <input type="hidden" name="auto_renew" value="0">
                <input type="checkbox" name="auto_renew" value="1" @checked(old('auto_renew', $subscription->auto_renew))>
                Renew automatically
            </label>

            <div class="actions">
                <button type="submit" class="btn-primary">Save changes</button>
                <a href="{{ route('subscription.index') }}" class="btn-secondary">Back to list</a>
            </div>
        </form>
    </div>
</div>
@endsection
