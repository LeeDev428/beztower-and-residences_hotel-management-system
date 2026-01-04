@extends('layouts.admin')

@section('title', 'Payments Verification')
@section('page-title', 'Payment Verification')

@section('content')
<div style="margin-bottom: 1.5rem;">
    <form method="GET" style="display: flex; gap: 1rem;">
        <select name="status" style="flex: 1; padding: 0.75rem; border: 1px solid var(--border-gray); border-radius: 8px;" onchange="this.form.submit()">
            <option value="pending" {{ request('status', 'pending') == 'pending' ? 'selected' : '' }}>Pending Payments</option>
            <option value="verified" {{ request('status') == 'verified' ? 'selected' : '' }}>Verified</option>
            <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Rejected</option>
            <option value="" {{ request('status') === '' ? 'selected' : '' }}>All</option>
        </select>
    </form>
</div>

<x-admin.card title="Payment Proofs ({{ $payments->total() }})">
    @if($payments->count() > 0)
    <div style="display: grid; gap: 1.5rem;">
        @foreach($payments as $payment)
        <div style="border: 1px solid var(--border-gray); border-radius: 12px; padding: 1.5rem;">
            <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem;">
                <!-- Payment Details -->
                <div>
                    <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1rem;">
                        <div>
                            <h4 style="font-size: 1.125rem; font-weight: 600; margin-bottom: 0.5rem;">{{ $payment->booking->booking_reference }}</h4>
                            <p style="color: var(--text-muted);">{{ $payment->booking->guest->name }} | {{ $payment->booking->guest->email }}</p>
                        </div>
                        <div style="display: flex; gap: 0.5rem; align-items: center;">
                            @if($payment->payment_type === 'down_payment')
                            <span style="padding: 0.375rem 0.75rem; background: linear-gradient(135deg, #3498db, #2980b9); color: white; border-radius: 6px; font-size: 0.875rem; font-weight: 600;">Downpayment</span>
                            @else
                            <span style="padding: 0.375rem 0.75rem; background: linear-gradient(135deg, #27ae60, #229954); color: white; border-radius: 6px; font-size: 0.875rem; font-weight: 600;">Full Payment</span>
                            @endif
                            <x-admin.badge :status="$payment->payment_status" />
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <div style="font-size: 0.875rem; color: var(--text-muted);">Room</div>
                            <div style="font-weight: 600;">{{ $payment->booking->room->room_number }}</div>
                        </div>
                        <div>
                            <div style="font-size: 0.875rem; color: var(--text-muted);">Check-in</div>
                            <div style="font-weight: 600;">{{ $payment->booking->check_in_date->format('M d, Y') }}</div>
                        </div>
                        <div>
                            <div style="font-size: 0.875rem; color: var(--text-muted);">Payment Type</div>
                            <div style="font-weight: 600;">{{ ucfirst(str_replace('_', ' ', $payment->payment_type)) }}</div>
                        </div>
                        <div>
                            <div style="font-size: 0.875rem; color: var(--text-muted);">Amount</div>
                            <div style="font-weight: 700; color: var(--primary-gold); font-size: 1.25rem;">₱{{ number_format($payment->amount, 2) }}</div>
                        </div>
                        <div>
                            <div style="font-size: 0.875rem; color: var(--text-muted);">Payment Method</div>
                            <div style="font-weight: 600;">{{ strtoupper($payment->payment_method) }}</div>
                        </div>
                        @if($payment->payment_reference)
                        <div>
                            <div style="font-size: 0.875rem; color: var(--text-muted);">Reference Number</div>
                            <div style="font-weight: 600;">{{ $payment->payment_reference }}</div>
                        </div>
                        @endif
                    </div>

                    @if($payment->payment_status === 'pending')
                    <div style="display: flex; gap: 1rem; margin-top: 1.5rem;">
                        <form method="POST" action="{{ route('admin.payments.verify', $payment) }}" style="flex: 1;" onsubmit="return confirm('Verify this payment?');">
                            @csrf
                            <x-admin.button type="success">✓ Verify Payment</x-admin.button>
                        </form>
                        <button type="button" onclick="showRejectForm({{ $payment->id }})" style="flex: 1; padding: 0.75rem; background: var(--danger); color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer;">✗ Reject</button>
                    </div>

                    <form id="reject-form-{{ $payment->id }}" method="POST" action="{{ route('admin.payments.reject', $payment) }}" style="display: none; margin-top: 1rem;">
                        @csrf
                        <textarea name="rejection_reason" placeholder="Reason for rejection..." style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-gray); border-radius: 8px; min-height: 80px;" required></textarea>
                        <div style="display: flex; gap: 0.5rem; margin-top: 0.5rem;">
                            <x-admin.button type="danger">Submit Rejection</x-admin.button>
                            <button type="button" onclick="hideRejectForm({{ $payment->id }})" style="padding: 0.75rem 1.5rem; background: var(--text-muted); color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer;">Cancel</button>
                        </div>
                    </form>
                    @endif

                    @if($payment->payment_status === 'failed' && $payment->payment_notes)
                    <div style="margin-top: 1rem; padding: 1rem; background: rgba(220, 53, 69, 0.1); border-radius: 8px; color: var(--danger);">
                        <strong>Rejection Reason:</strong> {{ $payment->payment_notes }}
                    </div>
                    @endif
                </div>

                <!-- Payment Proof -->
                <div>
                    <div style="font-weight: 600; margin-bottom: 0.5rem;">Payment Proof</div>
                    @if($payment->proof_of_payment)
                    <a href="{{ asset('storage/' . $payment->proof_of_payment) }}" target="_blank">
                        <img src="{{ asset('storage/' . $payment->proof_of_payment) }}" alt="Payment Proof" style="width: 100%; border-radius: 8px; border: 2px solid var(--border-gray); cursor: pointer; transition: transform 0.3s;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
                    </a>
                    <p style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.5rem; text-align: center;">Click to enlarge</p>
                    @else
                    <div style="padding: 2rem; background: var(--light-gray); border-radius: 8px; text-align: center; color: var(--text-muted);">
                        No proof uploaded
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div style="margin-top: 1.5rem;">
        {{ $payments->links() }}
    </div>
    @else
    <p style="text-align: center; padding: 3rem; color: var(--text-muted);">No payment proofs found</p>
    @endif
</x-admin.card>

@push('scripts')
<script>
function showRejectForm(id) {
    document.getElementById('reject-form-' + id).style.display = 'block';
}

function hideRejectForm(id) {
    document.getElementById('reject-form-' + id).style.display = 'none';
}
</script>
@endpush
@endsection
