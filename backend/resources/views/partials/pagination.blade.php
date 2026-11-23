{{-- Usage: @include('partials.pagination', ['paginator' => $items]) --}}
@if($paginator->hasPages())
    <nav class="pagination" aria-label="Pagination">
        @if($paginator->onFirstPage())
            <button disabled aria-label="Previous page">&lt;</button>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" aria-label="Previous page"><button>&lt;</button></a>
        @endif

        @foreach($paginator->getUrlRange(max(1, $paginator->currentPage() - 2), min($paginator->lastPage(), $paginator->currentPage() + 2)) as $page => $url)
            @if($page == $paginator->currentPage())
                <button class="active" aria-current="page">{{ $page }}</button>
            @else
                <a href="{{ $url }}"><button>{{ $page }}</button></a>
            @endif
        @endforeach

        @if($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" aria-label="Next page"><button>&gt;</button></a>
        @else
            <button disabled aria-label="Next page">&gt;</button>
        @endif
    </nav>
@endif
