@extends('layouts.app')

@section('content')
<style>
    .sub-page { padding: 30px; }
    .sub-summary, .sub-card { background: #fff; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,.05); padding: 20px; margin-bottom: 24px; }
    .sub-summary { display: flex; align-items: center; justify-content: space-between; gap: 16px; flex-wrap: wrap; }
    .sub-summary h1 { font-size: 2rem; }
    .sub-filter { display: flex; gap: 8px; flex-wrap: wrap; }
    .sub-filter a { padding: 6px 14px; border-radius: 999px; border: 1px solid #e5e7eb; color: #374151; text-decoration: none; font-size: 14px; }
    .sub-filter a.active { background: #2563eb; border-color: #2563eb; color: #fff; }
    .sub-table { width: 100%; border-collapse: collapse; }
    .sub-table th, .sub-table td { padding: 12px 10px; text-align: left; border-bottom: 1px solid #f1f5f9; font-size: 14px; }
    .sub-table th { color: #6b7280; font-weight: 600; text-transform: uppercase; font-size: 12px; }
    .badge { display: inline-block; padding: 3px 10px; border-radius: 999px; font-size: 12px; font-weight: 600; text-transform: capitalize; }
    .badge-active { background: #dcfce7; color: #166534; }
    .badge-expired { background: #fef3c7; color: #92400e; }
    .badge-cancelled { background: #fee2e2; color: #991b1b; }
    .row-actions { display: flex; gap: 8px; }
    .row-actions a, .row-actions button { padding: 6px 12px; border-radius: 8px; font-size: 13px; border: 0; cursor: pointer; text-decoration: none; }
    .btn-edit { background: #eff6ff; color: #1d4ed8; }
    .btn-delete { background: #fef2f2; color: #b91c1c; }
    .pagination { display: flex; gap: 6px; justify-content: center; margin-top: 20px; flex-wrap: wrap; }
    .pagination button { min-width: 36px; height: 36px; border-radius: 8px; border: 1px solid #e5e7eb; background: #fff; cursor: pointer; }
    .pagination button.active { background: #2563eb; color: #fff; border-color: #2563eb; }

    /* Phones and tablets (the sidebar takes space): each row becomes a card */
    @media (max-width: 900px) {
        .sub-page { padding: 16px; }
        .sub-table thead { display: none; }
        .sub-table, .sub-table tbody, .sub-table tr, .sub-table td { display: block; width: 100%; }
        .sub-table tr { border: 1px solid #f1f5f9; border-radius: 12px; padding: 8px 12px; margin-bottom: 12px; }
        .sub-table td { border: 0; padding: 6px 0; display: flex; justify-content: space-between; gap: 12px; }
        .sub-table td::before { content: attr(data-label); color: #6b7280; font-weight: 600; }
    }
</style>

<div class="sub-page">
    <div class="sub-summary">
        <div>
            <h3><strong>Total Subscriptions</strong></h3>
            <h1>{{ number_format($totalSubscriptions) }}</h1>
        </div>
        <div class="sub-filter">
            @foreach(['' => 'All', 'active' => 'Active', 'expired' => 'Expired', 'cancelled' => 'Cancelled'] as $value => $label)
                <a href="{{ route('subscription.index', array_filter(['status' => $value])) }}"
                   class="{{ request('status', '') === $value ? 'active' : '' }}">{{ $label }}</a>
            @endforeach
        </div>
    </div>

    <div class="sub-card">
        @if($subscriptions->count())
            <table class="sub-table">
                <thead>
                    <tr><th>#</th><th>Hospital</th><th>Plan</th><th>Price</th><th>Period</th><th>Status</th><th></th></tr>
                </thead>
                <tbody>
                @foreach($subscriptions as $subscription)
                    <tr id="subscription-{{ $subscription->subscription_id }}">
                        <td data-label="#">{{ $subscription->subscription_id }}</td>
                        <td data-label="Hospital">{{ $subscription->hospital?->name ?? '—' }}</td>
                        <td data-label="Plan">{{ $subscription->plan_name }}</td>
                        <td data-label="Price">{{ $subscription->price !== null ? '$'.number_format((float) $subscription->price, 2) : '—' }}</td>
                        <td data-label="Period">{{ $subscription->start_date?->format('M j, Y') }} – {{ $subscription->end_date?->format('M j, Y') }}</td>
                        <td data-label="Status"><span class="badge badge-{{ $subscription->status }}">{{ $subscription->status }}</span></td>
                        <td data-label="">
                            <div class="row-actions">
                                <a class="btn-edit" href="{{ route('subscription.edit', $subscription->subscription_id) }}">Edit</a>
                                <button class="btn-delete" type="button" data-delete-url="{{ route('subscription.destroy', $subscription->subscription_id) }}">Delete</button>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>

            @include('partials.pagination', ['paginator' => $subscriptions])
        @else
            <p>No subscriptions found.</p>
        @endif
    </div>
</div>

<script>
document.querySelectorAll('[data-delete-url]').forEach(button => {
    button.addEventListener('click', async () => {
        if (!confirm('Delete this subscription? This cannot be undone.')) return;
        const response = await fetch(button.dataset.deleteUrl, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' },
        });
        if (response.ok) button.closest('tr').remove(); else alert('Could not delete the subscription.');
    });
});
</script>
@endsection
