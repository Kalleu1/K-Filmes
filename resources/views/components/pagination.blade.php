@props(['paginator'])

@if ($paginator->hasPages())
    <nav class="k-pagination">
        {{-- Página anterior --}}
        @if ($paginator->onFirstPage())
            <span class="page-btn disabled">‹</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" class="page-btn">‹</a>
        @endif

        {{-- Números --}}
        @foreach ($paginator->links()->elements[0] ?? [] as $page => $url)
            @if ($page == $paginator->currentPage())
                <span class="page-number active">{{ $page }}</span>
            @else
                <a href="{{ $url }}" class="page-number">{{ $page }}</a>
            @endif
        @endforeach

        {{-- Próxima página --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" class="page-btn">›</a>
        @else
            <span class="page-btn disabled">›</span>
        @endif
    </nav>
@endif
