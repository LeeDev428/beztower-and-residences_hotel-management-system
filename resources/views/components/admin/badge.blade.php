@props(['status' => 'pending'])

@php
$statusConfig = [
    'pending' => ['color' => 'var(--warning)', 'bg' => 'rgba(255, 193, 7, 0.1)', 'text' => 'Pending'],
    'confirmed' => ['color' => 'var(--success)', 'bg' => 'rgba(40, 167, 69, 0.1)', 'text' => 'Confirmed'],
    'checked_in' => ['color' => 'var(--info)', 'bg' => 'rgba(23, 162, 184, 0.1)', 'text' => 'Checked In'],
    'checked_out' => ['color' => 'var(--text-muted)', 'bg' => 'rgba(108, 117, 125, 0.1)', 'text' => 'Checked Out'],
    'cancelled' => ['color' => 'var(--danger)', 'bg' => 'rgba(220, 53, 69, 0.1)', 'text' => 'Cancelled'],
    'paid' => ['color' => 'var(--success)', 'bg' => 'rgba(40, 167, 69, 0.1)', 'text' => 'Paid'],
    'clean' => ['color' => 'var(--success)', 'bg' => 'rgba(40, 167, 69, 0.1)', 'text' => 'Clean'],
    'dirty' => ['color' => 'var(--danger)', 'bg' => 'rgba(220, 53, 69, 0.1)', 'text' => 'Dirty'],
    'in_progress' => ['color' => 'var(--info)', 'bg' => 'rgba(23, 162, 184, 0.1)', 'text' => 'In Progress'],
];

$config = $statusConfig[$status] ?? ['color' => 'var(--text-muted)', 'bg' => 'rgba(108, 117, 125, 0.1)', 'text' => ucfirst($status)];
@endphp

<span style="display: inline-flex; align-items: center; padding: 0.375rem 0.75rem; border-radius: 20px; font-size: 0.875rem; font-weight: 600; background: {{ $config['bg'] }}; color: {{ $config['color'] }};">
    <span style="width: 6px; height: 6px; border-radius: 50%; background: {{ $config['color'] }}; margin-right: 0.5rem;"></span>
    {{ $config['text'] }}
</span>
