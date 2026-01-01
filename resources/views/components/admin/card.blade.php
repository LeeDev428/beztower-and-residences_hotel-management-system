<div style="background: white; border-radius: 12px; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1); overflow: hidden;">
    <div style="padding: 1.5rem; border-bottom: 1px solid var(--border-gray); display: flex; justify-content: space-between; align-items: center;">
        <h3 style="font-size: 1.125rem; font-weight: 600; color: var(--text-dark);">{{ $title }}</h3>
        @isset($action)
        <div>{{ $action }}</div>
        @endisset
    </div>
    <div style="padding: 1.5rem;">
        {{ $slot }}
    </div>
</div>
