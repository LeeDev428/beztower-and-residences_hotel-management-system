@extends('layouts.admin')

@section('title', 'Payment Details')
@section('page-title', 'Payment Details')

@section('content')
<div style="margin-bottom: 1.5rem;">
    <x-admin.button type="outline" href="{{ route('admin.payments.index') }}">← Back to Payments</x-admin.button>
</div>

<x-admin.card title="Payment {{ $payment->payment_reference ?: '#' . $payment->id }}">
    <div style="display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 1rem;">
        <div>
            <div style="font-size: 0.85rem; color: var(--text-muted);">Booking Reference</div>
            <div style="font-weight: 700;">{{ $payment->booking->booking_reference ?? 'N/A' }}</div>
        </div>
        <div>
            <div style="font-size: 0.85rem; color: var(--text-muted);">Guest</div>
            <div style="font-weight: 700;">{{ $payment->booking->guest->name ?? 'N/A' }}</div>
        </div>
        <div>
            <div style="font-size: 0.85rem; color: var(--text-muted);">Amount</div>
            <div style="font-weight: 700; color: var(--primary-gold);">₱{{ number_format($payment->amount, 2) }}</div>
        </div>
        <div>
            <div style="font-size: 0.85rem; color: var(--text-muted);">Status</div>
            <x-admin.badge :status="$payment->payment_status" />
        </div>
    </div>

    @if($payment->proof_of_payment)
        <div style="margin-top: 1.5rem;">
            <div style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 0.5rem;">Proof of Payment</div>
            <a href="{{ asset('storage/' . $payment->proof_of_payment) }}" target="_blank">
                <img src="{{ asset('storage/' . $payment->proof_of_payment) }}" alt="Payment proof" style="max-width: 420px; width: 100%; border-radius: 8px; border: 1px solid var(--border-gray);">
            </a>
        </div>
    @endif
</x-admin.card>
@endsection
