@if ($rooms->hasPages())
    <nav class="pagination-nav">
        <ul class="pagination">
            {{-- Previous Page Link --}}
            @if ($rooms->onFirstPage())
                <li class="page-item disabled">
                    <span class="page-link">&laquo;</span>
                </li>
            @else
                <li class="page-item">
                    <a class="page-link" href="{{ $rooms->previousPageUrl() }}" data-page="{{ $rooms->currentPage() - 1 }}">&laquo;</a>
                </li>
            @endif

            {{-- Pagination Elements --}}
            @php
                $start = max(1, $rooms->currentPage() - 2);
                $end = min($rooms->lastPage(), $rooms->currentPage() + 2);
            @endphp

            @if ($start > 1)
                <li class="page-item">
                    <a class="page-link" href="{{ $rooms->url(1) }}" data-page="1">1</a>
                </li>
                @if ($start > 2)
                    <li class="page-item disabled"><span class="page-link">...</span></li>
                @endif
            @endif

            @for ($page = $start; $page <= $end; $page++)
                @if ($page == $rooms->currentPage())
                    <li class="page-item active">
                        <span class="page-link">{{ $page }}</span>
                    </li>
                @else
                    <li class="page-item">
                        <a class="page-link" href="{{ $rooms->url($page) }}" data-page="{{ $page }}">{{ $page }}</a>
                    </li>
                @endif
            @endfor

            @if ($end < $rooms->lastPage())
                @if ($end < $rooms->lastPage() - 1)
                    <li class="page-item disabled"><span class="page-link">...</span></li>
                @endif
                <li class="page-item">
                    <a class="page-link" href="{{ $rooms->url($rooms->lastPage()) }}" data-page="{{ $rooms->lastPage() }}">{{ $rooms->lastPage() }}</a>
                </li>
            @endif

            {{-- Next Page Link --}}
            @if ($rooms->hasMorePages())
                <li class="page-item">
                    <a class="page-link" href="{{ $rooms->nextPageUrl() }}" data-page="{{ $rooms->currentPage() + 1 }}">&raquo;</a>
                </li>
            @else
                <li class="page-item disabled">
                    <span class="page-link">&raquo;</span>
                </li>
            @endif
        </ul>
    </nav>
@endif
