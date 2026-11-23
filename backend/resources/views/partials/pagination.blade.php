{{-- Usage: @include('partials.pagination', ['paginator' => $items]) --}}
@if($paginator->hasPages())
    @php
        $current = $paginator->currentPage();
        $last = $paginator->lastPage();
        $from = max(1, $current - 2);
        $to = min($last, $current + 2);
    @endphp
    <nav class="pagination" aria-label="Pagination">
        @if($paginator->onFirstPage())
            <button disabled aria-label="Previous page">&lt;</button>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" aria-label="Previous page"><button>&lt;</button></a>
        @endif

        @if($from > 1)
            <a href="{{ $paginator->url(1) }}"><button>1</button></a>
            @if($from > 2)<span class="pagination-gap" aria-hidden="true">…</span>@endif
        @endif

        @foreach($paginator->getUrlRange($from, $to) as $page => $url)
            @if($page == $current)
                <button class="active" aria-current="page">{{ $page }}</button>
            @else
                <a href="{{ $url }}"><button>{{ $page }}</button></a>
            @endif
        @endforeach

        @if($to < $last)
            @if($to < $last - 1)<span class="pagination-gap" aria-hidden="true">…</span>@endif
            <a href="{{ $paginator->url($last) }}"><button>{{ $last }}</button></a>
        @endif

        @if($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" aria-label="Next page"><button>&gt;</button></a>
        @else
            <button disabled aria-label="Next page">&gt;</button>
        @endif
    </nav>
@endif
