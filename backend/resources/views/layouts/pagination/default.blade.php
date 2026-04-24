{{-- layouts/pagination/default.blade.php --}}
@if ($paginator->hasPages())
    <ul class="pagination justify-content-center">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <li class="page-item disabled">
                <span class="page-link">
                    @if ($language->direction == 'rtl')
                        <i class="fas fa-angle-right"></i>
                    @else
                        <i class="fas fa-angle-left"></i>
                    @endif
                </span>
            </li>
        @else
            <li class="page-item">
                <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev">
                    @if ($language->direction == 'rtl')
                        <i class="fas fa-angle-right"></i>
                    @else
                        <i class="fas fa-angle-left"></i>
                    @endif
                </a>
            </li>
        @endif

        {{-- Pagination Elements --}}
        @foreach ($elements as $element)
            {{-- "Three Dots" Separator --}}
            @if (is_string($element))
                <li class="page-item disabled">
                    <span class="page-link">{{ $element }}</span>
                </li>
            @endif

            {{-- Array Of Links --}}
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <li class="page-item active">
                            <span class="page-link">
                                {{ $page }}
                                <span class="sr-only">(@translate('current'))</span>
                            </span>
                        </li>
                    @else
                        <li class="page-item">
                            <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                        </li>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <li class="page-item">
                <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next">
                    @if ($language->direction == 'rtl')
                        <i class="fas fa-angle-left"></i>
                    @else
                        <i class="fas fa-angle-right"></i>
                    @endif
                </a>
            </li>
        @else
            <li class="page-item disabled">
                <span class="page-link">
                    @if ($language->direction == 'rtl')
                        <i class="fas fa-angle-left"></i>
                    @else
                        <i class="fas fa-angle-right"></i>
                    @endif
                </span>
            </li>
        @endif
    </ul>

    {{-- Mobile Pagination --}}
    <div class="d-flex justify-content-between d-md-none mt-3">
        @if ($paginator->onFirstPage())
            <span class="btn btn-outline-secondary disabled">
                <i class="fas fa-angle-left mr-2"></i>
                @translate('previous')
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" class="btn btn-outline-primary">
                <i class="fas fa-angle-left mr-2"></i>
                @translate('previous')
            </a>
        @endif

        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" class="btn btn-outline-primary">
                @translate('next')
                <i class="fas fa-angle-right ml-2"></i>
            </a>
        @else
            <span class="btn btn-outline-secondary disabled">
                @translate('next')
                <i class="fas fa-angle-right ml-2"></i>
            </span>
        @endif
    </div>

    {{-- Pagination Information --}}
    <div class="d-flex justify-content-center mt-3">
        <small class="text-muted">
            @translate('showing') {{ ($paginator->currentPage() - 1) * $paginator->perPage() + 1 }}
            @translate('to') {{ min($paginator->currentPage() * $paginator->perPage(), $paginator->total()) }}
            @translate('of') {{ $paginator->total() }} @translate('entries')
        </small>
    </div>
@endif
