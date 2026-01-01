<div class="stat-card" style="background: {{ $color ?? 'linear-gradient(135deg, var(--primary-gold) 0%, var(--dark-gold) 100%)' }}; border-radius: 12px; padding: 1.5rem; color: white; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
    <div style="display: flex; justify-content: space-between; align-items: flex-start;">
        <div>
            <div style="opacity: 0.9; font-size: 0.875rem; margin-bottom: 0.5rem;">{{ $title }}</div>
            <div style="font-size: 2rem; font-weight: 700;">{{ $value }}</div>
            @isset($subtitle)
            <div style="opacity: 0.8; font-size: 0.875rem; margin-top: 0.5rem;">{{ $subtitle }}</div>
            @endisset
        </div>
        @isset($icon)
        <div style="opacity: 0.3; font-size: 3rem;">
            {!! $icon !!}
        </div>
        @endisset
    </div>
    @isset($trend)
    <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid rgba(255, 255, 255, 0.2); display: flex; align-items: center; gap: 0.5rem; font-size: 0.875rem;">
        {!! $trend !!}
    </div>
    @endisset
</div>
