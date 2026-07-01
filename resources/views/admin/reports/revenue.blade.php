@can('view-reports')

    @extends('layouts.app')

    @section('content')

        @php
            $reportsCollection = collect($reports);
            $cardToday = $reportsCollection->sum('today');
            $cardYesterday = $reportsCollection->sum('yesterday') ?? 0;
            $cardWeekly = $reportsCollection->sum('weekly') ?? 0;
            $cardMonthly = $reportsCollection->sum('monthly');
            $cardYearly = $reportsCollection->sum('yearly');
            $cardTotal = $reportsCollection->sum('total');
        @endphp

        <section class="section premium-dashboard">
            <div class="premium-page-head d-flex justify-content-between align-items-center flex-wrap">
                <div class="premium-page-title">
                    <span class="mini-badge">Reports Management</span>
                    <h2>Revenue Report</h2>
                    <p>Branch wise revenue overview</p>
                </div>

                @if(auth()->user()->role == 'owner')
                    <div class="premium-page-actions mb-3" style="min-width: 250px;">
                        <form method="GET"
                            action="{{ route('restaurant.reports.revenue', ['restaurant' => request()->route('restaurant')]) }}">
                            <label class="form-label text-muted small font-weight-bold mb-1">Filter By Branch</label>
                            <select name="branch_id" id="branchFilter" {{-- ← Add this --}} class="form-control shadow-sm"
                                onchange="this.form.submit()" style="border-radius: 8px;">
                                <option value="">All Branches</option>
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                                        {{ $branch->name }}
                                    </option>
                                @endforeach
                            </select>
                        </form>
                    </div>
                @endif
                {{-- Download Button --}}
                <a href="{{ route('restaurant.reports.revenue.pdf', ['restaurant' => request()->route('restaurant')]) }}"
                    class="btn btn-danger" id="downloadPdf">
                    <i class="fas fa-file-pdf"></i> Download PDF
                </a>

            </div>
        </section>

        <section class="section premium-dashboard pt-0">
            <div class="section-body">

                <div class="row mb-4">
                    <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
                        <div class="card border-0 shadow-sm text-center p-3"
                            style="border-left: 4px solid #007bff; border-radius: 10px;">
                            <small class="text-uppercase font-weight-bold text-muted">Today</small>
                            <h4 class="mt-2 mb-0 text-secondary">₹{{ number_format($cardToday) }}</h4>
                        </div>
                    </div>

                    <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
                        <div class="card border-0 shadow-sm text-center p-3"
                            style="border-left: 4px solid #6c757d; border-radius: 10px;">
                            <small class="text-uppercase font-weight-bold text-muted">Yesterday</small>
                            <h4 class="mt-2 mb-0 text-dark">₹{{ number_format($cardYesterday) }}</h4>
                        </div>
                    </div>

                    <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
                        <div class="card border-0 shadow-sm text-center p-3"
                            style="border-left: 4px solid #17a2b8; border-radius: 10px;">
                            <small class="text-uppercase font-weight-bold text-muted">Weekly</small>
                            <h4 class="mt-2 mb-0 text-info">₹{{ number_format($cardWeekly) }}</h4>
                        </div>
                    </div>

                    <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
                        <div class="card border-0 shadow-sm text-center p-3"
                            style="border-left: 4px solid #ffc107; border-radius: 10px;">
                            <small class="text-uppercase font-weight-bold text-muted">Monthly</small>
                            <h4 class="mt-2 mb-0 text-warning">₹{{ number_format($cardMonthly) }}</h4>
                        </div>
                    </div>

                    <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
                        <div class="card border-0 shadow-sm text-center p-3"
                            style="border-left: 4px solid #28a745; border-radius: 10px;">
                            <small class="text-uppercase font-weight-bold text-muted">Yearly</small>
                            <h4 class="mt-2 mb-0 text-success">₹{{ number_format($cardYearly) }}</h4>
                        </div>
                    </div>

                    <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
                        <div class="card border-0 shadow-sm text-center p-3 bg-dark text-white" style="border-radius: 10px;">
                            <small class="text-uppercase font-weight-bold text-white-50">Total</small>
                            <h4 class="mt-2 mb-0 text-white">₹{{ number_format($cardTotal) }}</h4>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="card premium-block shadow-sm">
                            <div class="card-header premium-card-header d-flex justify-content-between align-items-center">
                                <div>
                                    <h4 class="mb-1">Revenue Records</h4>
                                    <p class="header-subtext mb-0">Branch wise sales performance breakdown</p>
                                </div>
                            </div>

                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover vertical-align-middle">
                                        <thead>
                                            <tr>
                                                <th>SrNo</th>
                                                <th>Branch</th>
                                                <th>Today</th>
                                                <th>Yesterday</th>
                                                <th>Weekly</th>
                                                <th>This Month</th>
                                                <th>This Year</th>
                                                <th>Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($reports as $report)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>
                                                        <strong>{{ $report['branch_name'] }}</strong>
                                                    </td>
                                                    <td>₹{{ number_format($report['today']) }}</td>
                                                    <td>₹{{ number_format($report['yesterday']) }}</td>
                                                    <td>₹{{ number_format($report['weekly']) }}</td>
                                                    <td>₹{{ number_format($report['monthly']) }}</td>
                                                    <td>₹{{ number_format($report['yearly']) }}</td>
                                                    <td>
                                                        <strong class="text-dark">₹{{ number_format($report['total']) }}</strong>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="6" class="text-center py-4 text-muted">
                                                        No Revenue Records Found
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </section>
        <a href="{{ route('restaurant.reports.revenue.pdf', ['restaurant' => request()->route('restaurant')]) }}?branch_id=3"
            class="btn btn-danger">
            <i class="fas fa-file-pdf"></i> Test Single Branch (ID 5)
        </a>
        <script>
            document.addEventListener('DOMContentLoaded', function () {

                const branchSelect = document.getElementById('branchFilter');
                const downloadBtn = document.getElementById('downloadPdf');
                const baseUrl = "{{ route('restaurant.reports.revenue.pdf', ['restaurant' => request()->route('restaurant')]) }}";

                function updatePdfLink() {
                    if (!branchSelect || !downloadBtn) return;

                    const branchId = branchSelect.value;
                    let newUrl = baseUrl;

                    if (branchId) {
                        newUrl += '?branch_id=' + branchId;
                    }

                    downloadBtn.href = newUrl;
                    console.log('PDF Link Updated:', newUrl);
                }

                // Update when selection changes
                if (branchSelect) {
                    branchSelect.addEventListener('change', updatePdfLink);
                }

                // Initial update
                updatePdfLink();
            });
        </script>
    @endsection

@else
    @php abort(403); @endphp
@endcan
