@extends('layouts.app')

@section('content')

    @php
        $totalQuantitySold = 0;
        $allProcessedItems = collect();

        foreach ($reports as $branch) {
            foreach ($branch['items'] as $item) {
                $totalQuantitySold += $item->total_quantity;

                $itemName = $item->menuItem->name ?? 'Unknown';
                if (isset($allProcessedItems[$itemName])) {
                    $allProcessedItems[$itemName] += $item->total_quantity;
                } else {
                    $allProcessedItems[$itemName] = $item->total_quantity;
                }
            }
        }

        $bestSellerName = $allProcessedItems->sortDesc()->keys()->first() ?? 'No Data Available';
        $bestSellerQty = $allProcessedItems->sortDesc()->first() ?? 0;
    @endphp

    <section class="section premium-dashboard">
    <div class="premium-floating-header">
        <div class="header-content">
            <div class="header-left">
                <div class="header-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div>
                    <span class="header-badge">
                        Business Intelligence
                    </span>
                    <h1>Top Selling Menu Items</h1>
                    <p>Cross-reference menu item volumes...</p>
                </div>
            </div>
            <div class="header-right">
                {{-- Download Button --}}
                <a href="{{ route('restaurant.reports.top-selling-item.pdf', ['restaurant' => request()->route('restaurant')]) }}"
                    class="premium-back-btn" id="downloadPdf" target="_blank">
                    <i class="fas fa-file-pdf"></i> Download PDF
                </a>            
            </div>
        </div>    
    </div>    
</section>

<section class="section premium-dashboard pt-0">
    <div class="section-body">
        
        {{-- Filters Form Section --}}
        <div class="card premium-block shadow-sm mb-4">
            <div class="card-body p-4">
                <form method="GET" action="{{ URL::current() }}">
                    <div class="row g-3 align-items-end">

                        <div class="col-xl-3 col-md-6 col-12">
                            <label class="form-label text-muted small font-weight-bold mb-2">Target Branch</label>
                            <select name="branch_id" id="branchSelect" class="form-control shadow-sm" style="border-radius: 8px;">
                                @if (auth()->user()->role == 'owner')
                                    <option value="">All Operational Branches</option>
                                    @foreach ($branches as $branchOpt)
                                        <option value="{{ $branchOpt->id }}"
                                            {{ request('branch_id') == $branchOpt->id ? 'selected' : '' }}>
                                            {{ $branchOpt->name }}
                                        </option>
                                    @endforeach
                                @else
                                    <option value="{{ auth()->user()->branch_id }}" selected>
                                        {{ auth()->user()->branch?->name ?? 'Your Assigned Branch' }}
                                    </option>
                                @endif
                            </select>
                        </div>

                        <div class="col-xl-3 col-md-6 col-12">
                            <label class="form-label text-muted small font-weight-bold mb-2">Menu Selection</label>
                            <select name="menu_item_id" id="menuItemSelect" class="form-control shadow-sm" style="border-radius: 8px;">
                                <option value="">All Active Menu Items</option>
                                @foreach ($menuItems as $menuItem)
                                    <option value="{{ $menuItem->id }}" data-branch="{{ $menuItem->branch_id ?? '' }}"
                                        {{ request('menu_item_id') == $menuItem->id ? 'selected' : '' }}>
                                        {{ $menuItem->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-xl-2 col-md-6 col-6">
                            <label class="form-label text-muted small font-weight-bold mb-2">From Date</label>
                            <input type="date" name="from_date" class="form-control shadow-sm"
                                value="{{ request('from_date') }}" style="border-radius: 8px;">
                        </div>

                        <div class="col-xl-2 col-md-6 col-6">
                            <label class="form-label text-muted small font-weight-bold mb-2">To Date</label>
                            <input type="date" name="to_date" class="form-control shadow-sm"
                                value="{{ request('to_date') }}" style="border-radius: 8px;">
                        </div>

                        <div class="col-xl-2 col-md-12 col-12 d-flex mt-3 mt-xl-0">
                            <button type="submit" class="btn btn-primary shadow-sm font-weight-bold w-100 me-2 py-2" style="border-radius: 8px;">
                                Apply
                            </button>
                            <a href="{{ URL::current() }}" class="btn btn-outline-secondary font-weight-bold py-2 px-3 w-100" style="border-radius: 8px;">
                                Reset
                            </a>
                        </div>

                    </div>
                </form>
            </div>
        </div>

        {{-- KPI Cards Section --}}
        <div class="row g-4 mb-4">
            <div class="col-lg-6 col-12">
                <div class="report-card total-card h-100">
                    <div class="report-card-header">
                        <div class="report-icon total-icon">
                            <i class="fas fa-trophy"></i>
                        </div>
                        <span>Top Performer</span>
                    </div>
                    <h3 class="text-truncate text-white mt-2 mb-1">{{ $bestSellerName }}</h3>
                    <p class="text-white-50 mb-0 small">
                        Contributed <span class="font-weight-bold text-warning fw-bold">{{ number_format($bestSellerQty) }}</span> units to total volume.
                    </p>
                    <div class="report-line bg-white"></div>
                </div>
            </div>

            <div class="col-lg-6 col-12">
                <div class="report-card h-100">
                    <div class="report-card-header">
                        <div class="report-icon bg-success">
                            <i class="fas fa-cubes"></i>
                        </div>
                        <span>Aggregated Portions Handled</span>
                    </div>
                    <h3 class="text-dark mt-2 mb-1">{{ number_format($totalQuantitySold) }}</h3>
                    <p class="text-muted mb-0 small">Total item conversions under current parameters.</p>
                    <div class="report-line bg-success"></div>
                </div>
            </div>
        </div>

        {{-- Branch-wise Tables Loop --}}
        @foreach ($reports as $branch)
            <div class="card premium-block shadow-sm mb-4">
                <div class="card-header premium-card-header d-flex justify-content-between align-items-center flex-wrap py-3">
                    <div>
                        <h4 class="mb-1 fw-bold text-dark">{{ $branch['branch'] }}</h4>
                        <p class="header-subtext mb-0">Top menu item performance data</p>
                    </div>
        <span class="badge bg-dark text-white px-3 py-2 mt-2 mt-sm-0" style="border-radius: 6px; letter-spacing: 0.5px;">                        {{ count($branch['items']) }} Records Listed
                    </span>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover vertical-align-middle mb-0">
                            <thead>
                                <tr>
                                    <th class="ps-4" style="width: 15%;">Rank</th>
                                    <th>Menu Item Name</th>
                                    <th class="text-end pe-4" style="width: 25%;">Quantity Sold</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($branch['items'] as $item)
                                    <tr>
                                        <td class="ps-4 align-middle">
                                            @if ($loop->iteration == 1)
                                                <span class="badge bg-warning text-white font-weight-bold px-2 py-1" style="border-radius: 4px;">1</span>
                                            @elseif($loop->iteration == 2)
                                                <span class="badge bg-secondary text-white font-weight-bold px-2 py-1" style="border-radius: 4px;">2</span>
                                            @elseif($loop->iteration == 3)
                                                <span class="badge bg-info text-white font-weight-bold px-2 py-1" style="border-radius: 4px;">3</span>
                                            @else
                                                <span class="text-muted ps-1">{{ $loop->iteration }}</span>
                                            @endif
                                        </td>
                                        <td class="align-middle text-dark fw-bold">
                                            {{ $item->menuItem->name ?? 'Unmapped Item' }}
                                        </td>
                                        <td class="text-end pe-4 align-middle">
                                            <strong class="qty-badge text-success">
                                                {{ number_format($item->total_quantity) }}
                                            </strong>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center py-5 text-muted bg-white">
                                            <h5 class="font-weight-bold text-dark mb-1">No data available</h5>
                                            <small class="text-muted">
                                                No logged menu transactions match your current search constraints.
                                            </small>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endforeach

    </div>
</section>

    

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const branchSelect = document.getElementById('branchSelect');
        const menuSelect = document.getElementById('menuItemSelect');
        const fromDate = document.querySelector('input[name="from_date"]');
        const toDate = document.querySelector('input[name="to_date"]');
        const downloadBtn = document.getElementById('downloadPdf');

        const baseUrl = "{{ route('restaurant.reports.top-selling-item.pdf', ['restaurant' => request()->route('restaurant')]) }}";

        function updatePdfLink() {
            if (!downloadBtn) return;

            let url = baseUrl;
            let params = [];

            if (branchSelect && branchSelect.value) {
                params.push('branch_id=' + branchSelect.value);
            }
            if (menuSelect && menuSelect.value) {
                params.push('menu_item_id=' + menuSelect.value);
            }
            if (fromDate && fromDate.value) {
                params.push('from_date=' + fromDate.value);
            }
            if (toDate && toDate.value) {
                params.push('to_date=' + toDate.value);
            }

            if (params.length > 0) {
                url += '?' + params.join('&');
            }

            downloadBtn.href = url;
        }

        // Update link when any filter changes
        if (branchSelect) branchSelect.addEventListener('change', updatePdfLink);
        if (menuSelect) menuSelect.addEventListener('change', updatePdfLink);
        if (fromDate) fromDate.addEventListener('change', updatePdfLink);
        if (toDate) toDate.addEventListener('change', updatePdfLink);

        // Initial update
        updatePdfLink();
    });
</script>
@endsection
