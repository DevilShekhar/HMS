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
            <div class="premium-floating-header">
                <div class="header-content">
                    <div class="header-left">
                        <div class="header-icon">
                            <i class="fas fa-chart-bar"></i>
                        </div>
                        <div>
                            <span class="header-badge">
                                Reports Management
                            </span>
                            <h1>Reports Management</h1>
                            <p>Branch wise revenue overview</p>
                        </div>
                    </div>
                    <div class="header-right">
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
                            class="premium-back-btn" id="downloadPdf">
                            <i class="fas fa-file-pdf"></i> Download PDF
                        </a>            
                    </div>
                </div>    
            </div>    
        </section>
        <section class="section premium-dashboard pt-0">
            <div class="section-body">
                <div class="row g-4 mb-4">
                <!-- Today -->
                <div class="col-xl-2 col-lg-4 col-md-4 col-sm-6">
                    <div class="report-card">
                        <div class="report-card-header">
                            <div class="report-icon bg-primary">
                                <i class="fas fa-calendar-day"></i>
                            </div>
                            <span>Today</span>
                        </div>
                        <h3>₹{{ number_format($cardToday) }}</h3>
                        <div class="report-line bg-primary"></div>
                    </div>
                </div>
                <!-- Yesterday -->
                <div class="col-xl-2 col-lg-4 col-md-4 col-sm-6">
                    <div class="report-card">
                        <div class="report-card-header">
                            <div class="report-icon bg-secondary">
                                <i class="fas fa-clock"></i>
                            </div>
                            <span>Yesterday</span>
                        </div>
                        <h3>₹{{ number_format($cardYesterday) }}</h3>
                        <div class="report-line bg-secondary"></div>
                    </div>
                </div>
                <!-- Weekly -->
                <div class="col-xl-2 col-lg-4 col-md-4 col-sm-6">
                    <div class="report-card">
                        <div class="report-card-header">
                            <div class="report-icon bg-info">
                                <i class="fas fa-chart-bar"></i>
                            </div>
                            <span>Weekly</span>
                        </div>
                        <h3>₹{{ number_format($cardWeekly) }}</h3>
                        <div class="report-line bg-info"></div>
                    </div>
                </div>
                <!-- Monthly -->
                <div class="col-xl-2 col-lg-4 col-md-4 col-sm-6">
                    <div class="report-card">
                        <div class="report-card-header">
                            <div class="report-icon bg-warning">
                                <i class="fas fa-calendar-alt"></i>
                            </div>
                            <span>Monthly</span>
                        </div>
                        <h3>₹{{ number_format($cardMonthly) }}</h3>
                        <div class="report-line bg-warning"></div>
                    </div>
                </div>
                <!-- Yearly -->
                <div class="col-xl-2 col-lg-4 col-md-4 col-sm-6">
                    <div class="report-card">
                        <div class="report-card-header">
                            <div class="report-icon bg-success">
                                <i class="fas fa-trophy"></i>
                            </div>
                            <span>Yearly</span>
                        </div>

                        <h3>₹{{ number_format($cardYearly) }}</h3>

                        <div class="report-line bg-success"></div>
                    </div>
                </div>

                <!-- Total -->
                <div class="col-xl-2 col-lg-4 col-md-4 col-sm-6">
                    <div class="report-card total-card">
                        <div class="report-card-header">
                            <div class="report-icon total-icon">
                                <i class="fas fa-wallet"></i>
                            </div>
                            <span>Total Revenue</span>
                        </div>

                        <h3>₹{{ number_format($cardTotal) }}</h3>

                        <div class="report-line bg-white"></div>
                    </div>
                </div>

            </div>

                <div class="row">
                    <div class="col-12">
                        <div class="card premium-block shadow-sm">
                            <div class="card-header premium-card-header d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    <div class="section-icon me-3">
                                        <i class="fas fa-chart-line"></i>
                                    </div>
                                    <div>                                       
                                        <h4 class="mb-1 mt-2 fw-bold">
                                            Revenue Records
                                        </h4>
                                        <p class="header-subtext mb-0">
                                            Branch-wise Sales Performance Breakdown
                                        </p>
                                    </div>
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
                                                        <strong class="qty-badge  text-success">₹{{ number_format($report['total']) }}</strong>
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
