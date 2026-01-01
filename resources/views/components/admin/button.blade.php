@props(['type' => 'primary', 'href' => null, 'size' => 'md'])

@php
$colors = [
    'primary' => 'background: linear-gradient(135deg, var(--primary-gold) 0%, var(--dark-gold) 100%); color: white;',
    'success' => 'background: var(--success); color: white;',
    'danger' => 'background: var(--danger); color: white;',
    'warning' => 'background: var(--warning); color: var(--text-dark);',
    'secondary' => 'background: var(--text-muted); color: white;',
    'outline' => 'background: white; border: 2px solid var(--primary-gold); color: var(--primary-gold);',
];

$sizes = [
    'sm' => 'padding: 0.5rem 1rem; font-size: 0.875rem;',
    'md' => 'padding: 0.75rem 1.5rem; font-size: 1rem;',
    'lg' => 'padding: 1rem 2rem; font-size: 1.125rem;',
];

$style = ($colors[$type] ?? $colors['primary']) . ' ' . ($sizes[$size] ?? $sizes['md']);
@endphp

@if($href)
<a href="{{ $href }}" style="{{ $style }} border-radius: 8px; border: none; cursor: pointer; font-weight: 600; text-decoration: none; display: inline-block; transition: all 0.3s ease; text-align: center;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(0, 0, 0, 0.15)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none';">
    {{ $slot }}
</a>
@else
<button {{ $attributes->merge(['type' => 'submit']) }} style="{{ $style }} border-radius: 8px; border: none; cursor: pointer; font-weight: 600; transition: all 0.3s ease;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(0, 0, 0, 0.15)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none';">
    {{ $slot }}
</button>
@endif
