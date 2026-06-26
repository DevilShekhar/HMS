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

    <div class="container-fluid py-4">

        <div class="row align-items-center mb-4">
            <div class="col-12 d-flex justify-content-between align-items-center">
                <div>
                    <span class="badge badge-secondary text-uppercase tracking-wider px-2 py-1 mb-2">Business Intelligence</span>
                    <h2 class="font-weight-bold text-dark mb-1">Top Selling Menu Items</h2>
                    <p class="text-muted mb-0">Cross-reference menu item volumes...</p>
                </div>

                <a href="{{ route('restaurant.reports.top-selling-item.pdf', ['restaurant' => request()->route('restaurant')]) }}"
                class="btn btn-danger" id="downloadPdf" target="_blank">
                    <i class="fas fa-file-pdf"></i> Download PDF
                </a>
            </div>
        </div>
        <div class="card shadow-sm border-light mb-4">
            <div class="card-body p-3 p-sm-4 bg-white">
                <form method="GET" action="{{ URL::current() }}">
                    <div class="row align-items-end">

                        <div class="col-xl-3 col-md-6 col-12 mb-3 mb-xl-0">
                            <label class="text-muted small font-weight-bold text-uppercase mb-2 d-block">Target
                                Branch</label>
                            <select name="branch_id" id="branchSelect" class="form-control shadow-sm">
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
                                        {{ auth()->user()->branch?->name ?? 'Your Assigned Branch' }}</option>
                                @endif
                            </select>
                        </div>

                        <div class="col-xl-3 col-md-6 col-12 mb-3 mb-xl-0">
                            <label class="text-muted small font-weight-bold text-uppercase mb-2 d-block">Menu
                                Selection</label>
                            <select name="menu_item_id" id="menuItemSelect" class="form-control shadow-sm">
                                <option value="">All Active Menu Items</option>
                                @foreach ($menuItems as $menuItem)
                                    <option value="{{ $menuItem->id }}" data-branch="{{ $menuItem->branch_id ?? '' }}"
                                        {{ request('menu_item_id') == $menuItem->id ? 'selected' : '' }}>
                                        {{ $menuItem->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-xl-2 col-md-6 col-6 mb-3 mb-md-0">
                            <label class="text-muted small font-weight-bold text-uppercase mb-2 d-block">From Date</label>
                            <input type="date" name="from_date" class="form-control shadow-sm"
                                value="{{ request('from_date') }}">
                        </div>

                        <div class="col-xl-2 col-md-6 col-6 mb-3 mb-md-0">
                            <label class="text-muted small font-weight-bold text-uppercase mb-2 d-block">To Date</label>
                            <input type="date" name="to_date" class="form-control shadow-sm"
                                value="{{ request('to_date') }}">
                        </div>

                        <div class="col-xl-2 col-md-12 col-12 d-flex mt-3 mt-xl-0">
                            <button type="submit" class="btn btn-primary btn-block shadow-sm font-weight-bold mr-2 py-2">
                                Apply
                            </button>
                            <a href="{{ URL::current() }}" class="btn btn-outline-secondary font-weight-bold py-2 px-3">
                                Reset
                            </a>
                        </div>

                    </div>
                </form>
            </div>
        </div>

        <div class="row mb-2">

            <div class="col-lg-6 col-12 mb-4">
                <div class="card shadow-sm bg-dark text-white h-100 border-0">
                    <div class="card-body p-4 d-flex align-items-center justify-content-between">
                        <div class="text-truncate">
                            <span class="badge badge-warning text-uppercase mb-2 font-weight-bold">
                                🏆 Top Performer
                            </span>
                            <h3 class="mt-1 mb-1 font-weight-bold text-truncate text-white">
                                {{ $bestSellerName }}
                            </h3>
                            <p class="text-light mb-0 small">
                                Contributed <span
                                    class="font-weight-bold text-warning">{{ number_format($bestSellerQty) }}</span> units
                                to total volume.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6 col-12 mb-4">
                <div class="card shadow-sm h-100 bg-white">
                    <div class="card-body p-4 d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted text-uppercase small font-weight-bold d-block mb-1">
                                Aggregated Portions Handled
                            </span>
                            <h2 class="mb-1 font-weight-bold text-dark display-4" style="font-size: 2.2rem;">
                                {{ number_format($totalQuantitySold) }}
                            </h2>
                            <p class="text-muted mb-0 small">Total item conversions under current parameters.</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        @foreach ($reports as $branch)
            <div class="card shadow-sm mb-4">

                <div class="card-header bg-white py-3 px-4 d-flex justify-content-between align-items-center flex-wrap">
                    <div>
                        <h5 class="mb-1 font-weight-bold text-dark">
                            {{ $branch['branch'] }}
                        </h5>
                        <p class="text-muted small mb-0">
                            Top menu item performance data
                        </p>
                    </div>
                    <span class="badge badge-light text-dark font-weight-bold px-3 py-2 border mt-2 mt-sm-0">
                        {{ count($branch['items']) }} Records Listed
                    </span>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover mb-0 align-middle">

                            <thead>
                                <tr class="bg-light text-muted small text-uppercase font-weight-bold">
                                    <th class="pl-4 border-0" style="width: 15%;">Rank</th>
                                    <th class="border-0">Menu Item Name</th>
                                    <th class="text-right pr-4 border-0" style="width: 25%;">Quantity Sold</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse ($branch['items'] as $item)
                                    <tr>
                                        <td class="pl-4 font-weight-bold align-middle">
                                            @if ($loop->iteration == 1)
                                                <span
                                                    class="badge badge-warning text-white font-weight-bold px-2 py-1">1</span>
                                            @elseif($loop->iteration == 2)
                                                <span
                                                    class="badge badge-secondary text-white font-weight-bold px-2 py-1">2</span>
                                            @elseif($loop->iteration == 3)
                                                <span
                                                    class="badge badge-info text-white font-weight-bold px-2 py-1">3</span>
                                            @else
                                                <span class="text-muted pl-1">{{ $loop->iteration }}</span>
                                            @endif
                                        </td>

                                        <td class="align-middle text-dark font-weight-bold">
                                            {{ $item->menuItem->name ?? 'Unmapped Item' }}
                                        </td>

                                        <td class="text-right pr-4 align-middle">
                                            <span class="badge badge-light font-weight-bold px-3 py-2 text-dark border">
                                                {{ number_format($item->total_quantity) }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center py-5 text-muted bg-white">
                                            <p class="font-weight-bold text-dark mb-1">
                                                No data available
                                            </p>
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

    {{-- <script>
        document.addEventListener('DOMContentLoaded', function() {
            const branchSelect = document.getElementById('branchSelect');
            const menuSelect = document.getElementById('menuItemSelect');
            const allMenuOptions = Array.from(menuSelect.options);

            function filterMenuOptions() {
                const selectedBranchId = branchSelect.value;

                menuSelect.innerHTML = '';

                allMenuOptions.forEach(option => {
                    const optionBranchId = option.getAttribute('data-branch');

                    if (!selectedBranchId || !optionBranchId || optionBranchId === selectedBranchId) {
                        menuSelect.appendChild(option);
                    }
                });
            }

            branchSelect.addEventListener('change', function() {
                filterMenuOptions();
                menuSelect.value = "";
            });

            filterMenuOptions();
        });
    </script> --}}

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
