@if ($paginator->hasPages())
    <nav class="d-flex justify-content-between align-items-center my-4" role="navigation" aria-label="Pagination Navigation">
        <div class="text-secondary small">
            Showing {{ $paginator->firstItem() }} to {{ $paginator->lastItem() }} of {{ $paginator->total() }} results
        </div>

        <ul class="pagination pagination-sm m-0" style="gap: 4px;">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li class="page-item disabled" aria-disabled="true">
                    <span class="page-link border-0 bg-light text-muted" style="border-radius: 6px; width: 34px; height: 34px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-chevron-left small"></i>
                    </span>
                </li>
            @else
                <li class="page-item">
                    <a class="page-link border-0 text-secondary bg-light" href="{{ $paginator->previousPageUrl() }}" rel="prev" style="border-radius: 6px; width: 34px; height: 34px; display: flex; align-items: center; justify-content: center; transition: all 0.2s;">
                        <i class="fas fa-chevron-left small"></i>
                    </a>
                </li>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <li class="page-item disabled" aria-disabled="true">
                        <span class="page-link border-0 bg-transparent text-muted" style="width: 34px; height: 34px; display: flex; align-items: center; justify-content: center;">{{ $element }}</span>
                    </li>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="page-item active" aria-current="page">
                                <span class="page-link border-0 text-white" style="background-color: #FA5603; border-radius: 6px; width: 34px; height: 34px; display: flex; align-items: center; justify-content: center; font-weight: 600;">
                                    {{ $page }}
                                </span>
                            </li>
                        @else
                            <li class="page-item">
                                <a class="page-link border-0 text-dark bg-light pagination-hover-btn" href="{{ $url }}" style="border-radius: 6px; width: 34px; height: 34px; display: flex; align-items: center; justify-content: center; transition: all 0.2s;">
                                    {{ $page }}
                                </a>
                            </li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li class="page-item">
                    <a class="page-link border-0 text-secondary bg-light" href="{{ $paginator->nextPageUrl() }}" rel="next" style="border-radius: 6px; width: 34px; height: 34px; display: flex; align-items: center; justify-content: center; transition: all 0.2s;">
                        <i class="fas fa-chevron-right small"></i>
                    </a>
                </li>
            @else
                <li class="page-item disabled" aria-disabled="true">
                    <span class="page-link border-0 bg-light text-muted" style="border-radius: 6px; width: 34px; height: 34px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-chevron-right small"></i>
                    </span>
                </li>
            @endif
        </ul>
    </nav>

    <style>
        .pagination-hover-btn:hover {
            background-color: #ffebe0 !important;
            color: #FA5603 !important;
        }
    </style>
@endif
