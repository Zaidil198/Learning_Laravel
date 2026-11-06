@if ($paginator->hasPages())
<nav aria-label="Navigasi halaman">
    <ul class="pagination">
        {{-- Tombol Sebelumnya --}}
        @if ($paginator->onFirstPage())
        <li class="disabled"><span>&lsaquo;</span></li>
        @else
        <li><a href="{{ $paginator->previousPageUrl() }}" rel="prev">&lsaquo;</a></li>
        @endif

        {{-- Nomor Halaman --}}
        @foreach ($elements as $element)
        {{-- Pemisah titik tiga --}}
        @if (is_string($element))
        <li class="disabled"><span>{{ $element }}</span></li>
        @endif

        {{-- Daftar tautan halaman --}}
        @if (is_array($element))
        @foreach ($element as $page => $url)
        @if ($page == $paginator->currentPage())
        <li class="active"><span>{{ $page }}</span></li>
        @else
        <li><a href="{{ $url }}">{{ $page }}</a></li>
        @endif
        @endforeach
        @endif
        @endforeach

        {{-- Tombol Berikutnya --}}
        @if ($paginator->hasMorePages())
        <li><a href="{{ $paginator->nextPageUrl() }}" rel="next">&rsaquo;</a></li>
        @else
        <li class="disabled"><span>&rsaquo;</span></li>
        @endif
    </ul>
</nav>
@endif