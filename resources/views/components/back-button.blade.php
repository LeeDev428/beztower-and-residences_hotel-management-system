@props(['url' => 'javascript:history.back()', 'text' => 'Back'])

<a href="{{ $url }}" class="back-button">
    <i class="fas fa-arrow-left"></i> {{ $text }}
</a>

<style>
    .back-button {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        color: #666;
        text-decoration: none;
        font-size: 1rem;
        font-weight: 500;
        padding: 0.5rem 0;
        transition: all 0.3s ease;
    }

    .back-button:hover {
        color: #d4af37;
        transform: translateX(-5px);
    }

    .back-button i {
        font-size: 0.9rem;
        transition: transform 0.3s ease;
    }

    .back-button:hover i {
        transform: translateX(-3px);
    }
</style>
