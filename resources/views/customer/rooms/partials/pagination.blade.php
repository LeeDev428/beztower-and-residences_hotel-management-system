@if ($rooms->hasPages())
    <nav>
        <ul>
            {{-- Previous Page Link --}}
            @if ($rooms->onFirstPage())
                <li class="disabled">
                    <span>&laquo; Previous</span>
                </li>
            @else
                <li>
                    <a href="{{ $rooms->previousPageUrl() }}" data-page="{{ $rooms->currentPage() - 1 }}">&laquo; Previous</a>
                </li>
            @endif

            {{-- Pagination Elements --}}
            @php
                $start = max(1, $rooms->currentPage() - 2);
                $end = min($rooms->lastPage(), $rooms->currentPage() + 2);
            @endphp

            @if ($start > 1)
                <li>
                    <a href="{{ $rooms->url(1) }}" data-page="1">1</a>
                </li>
                @if ($start > 2)
                    <li class="disabled"><span>...</span></li>
                @endif
            @endif

            @for ($page = $start; $page <= $end; $page++)
                @if ($page == $rooms->currentPage())
                    <li class="active">
                        <span>{{ $page }}</span>
                    </li>
                @else
                    <li>
                        <a href="{{ $rooms->url($page) }}" data-page="{{ $page }}">{{ $page }}</a>
                    </li>
                @endif
            @endfor

            @if ($end < $rooms->lastPage())
                @if ($end < $rooms->lastPage() - 1)
                    <li class="disabled"><span>...</span></li>
                @endif
                <li>
                    <a href="{{ $rooms->url($rooms->lastPage()) }}" data-page="{{ $rooms->lastPage() }}">{{ $rooms->lastPage() }}</a>
                </li>
            @endif

            {{-- Next Page Link --}}
            @if ($rooms->hasMorePages())
                <li>
                    <a href="{{ $rooms->nextPageUrl() }}" data-page="{{ $rooms->currentPage() + 1 }}">Next &raquo;</a>
                </li>
            @else
                <li class="disabled">
                    <span>Next &raquo;</span>
                </li>
            @endif
        </ul>
    </nav>
@endif
