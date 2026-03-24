@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" style="display:flex;justify-content:center;align-items:center;gap:0.4rem;flex-wrap:wrap;">
        @if ($paginator->onFirstPage())
            <span aria-disabled="true" aria-label="Previous" style="padding:0.42rem 0.8rem;border:1px solid #d7d7d7;border-radius:8px;background:#f3f3f3;color:#9a9a9a;font-size:0.86rem;font-weight:600;cursor:not-allowed;">Previous</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="Previous" style="padding:0.42rem 0.8rem;border:1px solid #d7d7d7;border-radius:8px;background:#fff;color:#444;text-decoration:none;font-size:0.86rem;font-weight:600;">Previous</a>
        @endif

        @foreach ($elements as $element)
            @if (is_string($element))
                <span aria-disabled="true" style="padding:0.42rem 0.65rem;color:#a0a0a0;font-size:0.86rem;">{{ $element }}</span>
            @endif

            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span aria-current="page" style="min-width:2rem;text-align:center;padding:0.42rem 0.55rem;border:1px solid #b68e17;border-radius:8px;background:#d4af37;color:#2c2c2c;font-size:0.86rem;font-weight:700;">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" style="min-width:2rem;text-align:center;padding:0.42rem 0.55rem;border:1px solid #d7d7d7;border-radius:8px;background:#fff;color:#444;text-decoration:none;font-size:0.86rem;font-weight:600;">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach

        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="Next" style="padding:0.42rem 0.8rem;border:1px solid #d7d7d7;border-radius:8px;background:#fff;color:#444;text-decoration:none;font-size:0.86rem;font-weight:600;">Next</a>
        @else
            <span aria-disabled="true" aria-label="Next" style="padding:0.42rem 0.8rem;border:1px solid #d7d7d7;border-radius:8px;background:#f3f3f3;color:#9a9a9a;font-size:0.86rem;font-weight:600;cursor:not-allowed;">Next</span>
        @endif
    </nav>
@endif
