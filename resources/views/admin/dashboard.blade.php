@extends('layouts.app')

@section('content')

    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-body py-3">
                    <div
                        class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center gap-3">

                        {{-- Left: Country & Timezone --}}
                        @if(isset($currentBranch) && $currentBranch)
                            @php $country = $currentBranch->country; @endphp
                            <div class="d-flex align-items-center gap-3">
                                <div class="bg-warning rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                                    style="width:44px;height:44px;">
                                    <i class="fas fa-store text-white" style="font-size:1.1rem;"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-semibold" style="font-size:0.9rem;">
                                        {{ $country?->name ?? 'Country Not Assigned' }}
                                        @if($country)
                                            <span class="badge bg-light text-dark border ms-1"
                                                style="font-size:0.65rem; font-weight:400; padding:2px 8px;">
                                                {{ $country->iso_code }}
                                            </span>
                                        @endif
                                    </h6>
                                    <div class="d-flex align-items-center gap-3 mt-1 flex-wrap">
                                        <p class="text-dark" style="font-size:0.75rem;">
                                            <i class="far fa-globe me-1"></i>
                                            {{ $country?->timezone ?? 'N/A' }}
                                        </p>

                                        <p class="fw-semibold text-dark" style="font-size:0.8rem;">
                                            <i class="far fa-clock me-1"></i>
                                            <span id="branchClock">--:--:--</span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- Right: Filters + Quick Order --}}
                        <div class="d-flex flex-column flex-sm-row align-items-stretch align-items-sm-center gap-2 ms-lg-auto">

                            {{-- Restaurant Filter --}}
                            @if(auth()->user()->role == 'super_admin')
                                <select id="restaurantFilter" class="form-select form-select-sm"
                                    style="min-width:130px; width:100%; font-size:0.8rem; padding:4px 28px 4px 10px; height:32px; border-radius:6px; background-color:#f8f9fa; border-color:#dee2e6;">
                                    <option value="">All Restaurants</option>
                                    @foreach($restaurants as $restaurant)
                                        <option value="{{ $restaurant->id }}">{{ $restaurant->name }}</option>
                                    @endforeach
                                </select>
                            @else
                                <select id="restaurantFilter" class="form-select form-select-sm"
                                    style="min-width:130px; width:100%; font-size:0.8rem; padding:4px 28px 4px 10px; height:32px; border-radius:6px; background-color:#f8f9fa; border-color:#dee2e6;"
                                    disabled>
                                    <option value="{{ app('restaurant')->id }}">{{ app('restaurant')->name }}</option>
                                </select>
                            @endif
                            {{-- Branch Filter --}}
                            @if(in_array(auth()->user()->role, ['super_admin', 'owner']))
                                <select id="branchFilter" class="form-select form-select-sm"
                                    style="min-width:130px; width:100%; font-size:0.8rem; padding:4px 28px 4px 10px; height:32px; border-radius:6px; background-color:#f8f9fa; border-color:#dee2e6;">
                                    <option value="">All Branches</option>
                                </select>
                            @endif

                            {{-- Quick Order Button --}}
                            @can('create-order')
                                                <a href="{{ auth()->user()->branch_id
                                ? route('branch.orders.create', ['restaurant' => currentRestaurantSlug(), 'branch' => currentBranchSlug()])
                                : route('restaurant.orders.create', ['restaurant' => currentRestaurantSlug()]) }}"
                                                    class="quick-order-btn"
                                                    style="display:inline-flex; align-items:center; gap:6px; padding:4px 18px; height:32px; background: linear-gradient(135deg, #ff8a00, #ff5f00); color:#ffffff; font-weight:600; font-size:0.8rem; border-radius:6px; text-decoration:none; white-space:nowrap; border:none; box-shadow: 0 2px 8px rgba(255, 138, 0, 0.3); transition:all 0.3s cubic-bezier(0.4, 0, 0.2, 1); cursor:pointer; position:relative; overflow:hidden;">
                                                    <i class="fas fa-bolt" style="font-size:0.75rem; transition:transform 0.3s ease;"></i>
                                                    Quick Order
                                                    <span
                                                        style="position:absolute; top:50%; left:50%; width:0; height:0; border-radius:50%; background:rgba(255,255,255,0.2); transform:translate(-50%, -50%); transition:width 0.6s ease, height 0.6s ease;"></span>
                                                </a>
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <section class="section">
        <div class="row mb-3">
            {{-- SuperAdmin Cards --}}
            @if(isset($revenue) && auth()->user()->role == 'super_admin')
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="dashboard-card restaurant-card">
                        <div class="header-icon">
                            <i class="fas fa-store"></i>
                        </div>
                        <div class="card-content">
                            <h6>Total Restaurants</h6>
                            <h2>{{ $totalRestaurants }}</h2>
                            <span>Registered Restaurants</span>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="dashboard-card branch-card">
                        <div class="header-icon">
                            <i class="fas fa-code-branch"></i>
                        </div>
                        <div class="card-content">
                            <h6>Total Branches</h6>
                            <h2>{{ $totalBranches }}</h2>
                            <span>Active Branches</span>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="dashboard-card warning-card">
                        <div class="header-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="card-content">
                            <h6>Near Expiring</h6>
                            <h2>{{ $nearExpirySubscriptions }}</h2>
                            <span>Within 7 Days</span>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="dashboard-card danger-card">
                        <div class="header-icon">
                            <i class="fas fa-times-circle"></i>
                        </div>
                        <div class="card-content">
                            <h6>Expired Plans</h6>
                            <h2>{{ $expiredSubscriptionCount }}</h2>
                            <span>Need Renewal</span>
                        </div>
                    </div>
                </div>
            @endif
            @can('today-revenue')
                @if(auth()->user()->role != 'branch_manager')
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="dashboard-card branch-card">
                            <div class="header-icon">
                                <i class="fas fa-code-branch"></i>
                            </div>
                            <div class="card-content">
                                <h6>Total Branches</h6>
                                <h2>{{ $totalBranches }}</h2>
                                <span>Active Branches</span>
                            </div>
                        </div>
                    </div>
                @endif
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="dashboard-card restaurant-card">
                        <div class="header-icon"><i class="fas fa-coins"></i></div>
                        <div class="card-content">
                            <h6>Today Revenue</h6>
                            <h3 class="mb-1" id="todayRevenue">
                                {{ $currencySymbol ?? '₹' }}{{ number_format($revenue['today']['amount']) }}</h3>
                            <p class="mb-0">{{ $revenue['today']['orders'] }} Orders</p>
                        </div>
                    </div>
                </div>
            @endcan

            @can('yesterday-revenue')
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="dashboard-card restaurant-card">
                        <div class="header-icon"><i class="fas fa-coins"></i></div>
                        <div class="card-content">
                            <h6>Yesterday Revenue</h6>
                            <h3 class="mb-1" id="yesterdayRevenue">
                                {{ $currencySymbol ?? '₹' }}{{ number_format($revenue['yesterday']['amount']) }}</h3>
                            <p class="mb-0">{{ $revenue['yesterday']['orders'] }} Orders</p>
                        </div>
                    </div>
                </div>
            @endcan
            @can('weekly-revenue')
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="dashboard-card restaurant-card">
                        <div class="header-icon">
                            <i class="fas fa-coins"></i>
                        </div>
                        <div class="card-content">
                            <h6>Weekly Revenue</h6>
                            <h3 class="mb-1" id="weeklyRevenueDisplay">
                                {{ $currencySymbol ?? '₹' }}{{ number_format($revenue['weekly']['amount']) }}</h3>
                            <p class="mb-0">{{ $revenue['weekly']['orders'] }} Orders</p>
                        </div>
                    </div>
                </div>
            @endcan

            @can('monthly-revenue')
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="dashboard-card restaurant-card">
                        <div class="header-icon">
                            <i class="fas fa-coins"></i>
                        </div>
                        <div class="card-content">
                            <h6 class="mb-2">Monthly Revenue</h6>
                            @if (isset($revenue))
                                <h3 class="mb-1" id="monthlyRevenueDisplay">
                                    {{ $currencySymbol ?? '₹' }}{{ number_format($revenue['monthly']['amount']) }}</h3>
                                <p class="mb-0">{{ $revenue['monthly']['orders'] }} Orders</p>
                            @endif
                        </div>
                    </div>
                </div>
            @endcan

            @can('yearly-revenue')
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="dashboard-card restaurant-card">
                        <div class="header-icon">
                            <i class="fas fa-coins"></i>
                        </div>
                        <div class="card-content">
                            <h6 class="mb-2">Yearly Revenue</h6>
                            @if (isset($revenue))
                                <h3 class="mb-1" id="yearlyRevenueDisplay">
                                    {{ $currencySymbol ?? '₹' }}{{ number_format($revenue['yearly']['amount']) }}</h3>
                                <p class="mb-0">{{ $revenue['yearly']['orders'] }} Orders</p>
                            @endif
                        </div>
                    </div>
                </div>
            @endcan
        </div>

        @can('superadmin-view')
            {{-- ===== EXPIRED SUBSCRIPTIONS ===== --}}
            <div class="row mb-4">
                <div class="col-12">
                    <div class="rms-dashboard-card">
                        <div class="expired-header">
                            <div class="expired-header-left">
                                <div class="expired-icon">
                                    <i class="fas fa-times"></i>
                                </div>
                                <div>
                                    <h3>Expired Subscriptions</h3>
                                    <p>
                                        Subscriptions that have expired and require immediate attention.
                                    </p>
                                </div>
                            </div>
                            <div class="expired-header-right">
                                <span class="urgent-badge">
                                    <i class="fas fa-bolt"></i>
                                    URGENT
                                </span>
                                <div class="expired-count-card">
                                    <h2>{{ count($expiredSubscriptions) }}</h2>
                                    <span>Expired Subscription</span>
                                </div>
                            </div>
                        </div>
                        <div class="rms-table-wrap">
                            <table class="rms-table">
                                <thead>
                                    <tr>
                                        <th>Restaurant</th>
                                        <th>Branch</th>
                                        <th>Plan</th>
                                        <th>End Date</th>
                                        <th>Expired Since</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($expiredSubscriptions as $subscription)
                                        @php
                                            $endDate = \Carbon\Carbon::parse($subscription->end_date);
                                            $expiredDays = $endDate->diffInDays(now());
                                        @endphp
                                        <tr>
                                            <td>{{ $subscription->branch?->restaurant?->name ?? 'N/A' }}</td>
                                            <td>{{ $subscription->branch?->name ?? 'N/A' }}</td>
                                            <td><span class="plan-pill"><i class="fas fa-crown"></i>
                                                    {{ $subscription->plan?->name ?? $subscription->billing_cycle }}</span></td>
                                            <td>
                                                <div class="date-box">
                                                    <div class="date-icon">
                                                        <i class="far fa-calendar-alt"></i>
                                                    </div>
                                                    <div>
                                                        <strong>
                                                            {{ $endDate->format('d M Y') }}
                                                        </strong>
                                                        <small>
                                                            End Date
                                                        </small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="expired-box">
                                                    <div class="expired-icon-small">
                                                        <i class="far fa-clock"></i>
                                                    </div>
                                                    <div>
                                                        <strong>
                                                            {{ (int) $expiredDays }} day{{ (int) $expiredDays > 1 ? 's' : '' }} ago
                                                        </strong>
                                                        <small>
                                                            Expired Since
                                                        </small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="status-expired"><i class="fas fa-exclamation-circle"></i>Expired</span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="rms-empty-state">
                                                <i class="fas fa-check-circle" style="color: #27ae60;"></i>
                                                <p>No expired subscriptions found.</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ===== SUBSCRIPTION OVERVIEW ===== --}}
            <div class="row mb-4">
                <div class="col-12">
                    <div class="rms-dashboard-card">
                        <div class="subscription-header">

                            <div class="subscription-title">

                                <div class="header-icon">
                                    <i class="fas fa-calendar-check"></i>
                                </div>

                                <div>

                                    <h3>Subscription Overview</h3>

                                    <p>
                                        Monitor all restaurant subscriptions and renewal status.
                                    </p>

                                </div>

                            </div>

                            <div class="subscription-summary">

                                <div class="summary-pill active">

                                    <i class="fas fa-check-circle"></i>

                                    <span>Active</span>

                                    <strong>{{ $subscriptions->filter(fn($s) => now()->diffInDays(\Carbon\Carbon::parse($s->end_date), false) > 30)->count() }}</strong>

                                </div>

                                <div class="summary-pill warning">

                                    <i class="fas fa-clock"></i>

                                    <span>Expiring Soon</span>

                                    <strong>{{ $subscriptions->filter(function ($s) {
                $d = now()->diffInDays(\Carbon\Carbon::parse($s->end_date), false);
                return $d > 7 && $d <= 30;
            })->count() }}</strong>

                                </div>

                                <div class="summary-pill danger">

                                    <i class="fas fa-exclamation-triangle"></i>

                                    <span>Critical</span>

                                    <strong>{{ $subscriptions->filter(fn($s) => now()->diffInDays(\Carbon\Carbon::parse($s->end_date), false) <= 7)->count() }}</strong>

                                </div>

                            </div>

                        </div>
                        <div class="rms-table-wrap">
                            <table class="rms-table">
                                <thead>
                                    <tr>
                                        <th>Restaurant</th>
                                        <th>Branch</th>
                                        <th>Plan</th>
                                        <th>End Date</th>
                                        <th>Days Left</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($subscriptions as $subscription)
                                        @php
                                            $endDate = \Carbon\Carbon::parse($subscription->end_date);
                                            $days = now()->diffInDays($endDate, false);
                                        @endphp
                                        <tr>
                                            <td>{{ $subscription->branch?->restaurant?->name ?? 'N/A' }}</td>
                                            <td>{{ $subscription->branch?->name ?? 'N/A' }}</td>
                                            <td>{{ $subscription->billing_cycle ?? 'N/A' }}</td>
                                            <td>{{ $endDate->format('d M Y') }}</td>
                                            <td>
                                                <span
                                                    class="fw-bold {{ $days <= 7 ? 'text-danger' : ($days <= 30 ? 'text-warning' : 'text-success') }}">
                                                    {{ (int) $days }} day{{ (int) $days > 1 ? 's' : '' }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($days <= 7)
                                                    <span class="rms-badge rms-badge--critical">
                                                        <i class="fas fa-exclamation-triangle"></i> Critical
                                                    </span>
                                                @elseif($days <= 30)
                                                    <span class="rms-badge rms-badge--warning">
                                                        <i class="fas fa-clock"></i> Expiring Soon
                                                    </span>
                                                @else
                                                    <span class="rms-badge rms-badge--success">
                                                        <i class="fas fa-check"></i> Active
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="rms-empty-state">
                                                <i class="fas fa-calendar-check"></i>
                                                <p>No subscriptions nearing expiry.</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ===== INVENTORY ALERTS ===== --}}
            <div class="row mb-4">
                <div class="col-12">
                    <div class="rms-dashboard-card">
                        <div class="subscription-header">
                            <!-- Left Side -->
                            <div class="subscription-title">

                                <div class="header-icon ">
                                    <i class="fas fa-bell"></i>
                                </div>

                                <div>

                                    <h3>Inventory Alerts</h3>

                                    <p>
                                        Monitor stock availability and manage inventory efficiently.
                                    </p>

                                </div>

                            </div>
                            <!-- Right Side -->
                            <div class="subscription-summary">

                                <!-- Out of Stock -->
                                <div class="summary-pill danger">

                                    <i class="fas fa-times-circle"></i>

                                    <span>Out of Stock</span>

                                    <strong>{{ $outOfStockItems ?? 0 }}</strong>

                                </div>

                                <!-- Low Stock -->
                                <div class="summary-pill warning">

                                    <i class="fas fa-exclamation-circle"></i>

                                    <span>Low Stock</span>

                                    <strong>{{ $lowStockItems ?? 0 }}</strong>

                                </div>

                                <!-- In Stock -->
                                <div class="summary-pill active">

                                    <i class="fas fa-check-circle"></i>

                                    <span>In Stock</span>

                                    <strong>{{ $inStockItems ?? 0 }}</strong>

                                </div>

                            </div>
                        </div>
                        <div class="rms-table-wrap">
                            <table class="rms-table">
                                <thead>
                                    <tr>
                                        <th>Restaurant</th>
                                        <th>Branch</th>
                                        <th>Item</th>
                                        <th>Remaining</th>
                                        <th>Minimum</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($inventoryAlerts as $item)
                                        <tr>
                                            <td>{{ $item->restaurant?->name ?? 'N/A' }}</td>
                                            <td>{{ $item->branch?->name ?? 'N/A' }}</td>
                                            <td><strong>{{ $item->name }}</strong></td>
                                            <td>
                                                <span
                                                    class="fw-bold {{ $item->remaining_stock <= 0 ? 'text-danger' : ($item->remaining_stock <= $item->minimum_stock ? 'text-warning' : 'text-success') }}">
                                                    {{ $item->remaining_stock }} {{ $item->unit }}
                                                </span>
                                            </td>
                                            <td>{{ $item->minimum_stock }} {{ $item->unit }}</td>
                                            <td>
                                                @if($item->remaining_stock <= 0)
                                                    <span class="rms-badge rms-badge--danger">
                                                        <i class="fas fa-times-circle"></i> Out of Stock
                                                    </span>
                                                @elseif($item->remaining_stock <= $item->minimum_stock)
                                                    <span class="rms-badge rms-badge--warning">
                                                        <i class="fas fa-exclamation-circle"></i> Low Stock
                                                    </span>
                                                @else
                                                    <span class="rms-badge rms-badge--success">
                                                        <i class="fas fa-check-circle"></i> In Stock
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="rms-empty-state">
                                                <i class="fas fa-check-circle" style="color: #27ae60;"></i>
                                                <p>No inventory alerts.</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ===== TOP RESTAURANTS BY REVENUE ===== --}}
            <div class="row mb-4">
                <div class="col-12">
                    <div class="rms-dashboard-card">
                        <div class="subscription-header">
                            <!-- Left -->
                            <div class="subscription-title">

                                <div class="header-icon">
                                    <i class="fas fa-trophy"></i>
                                </div>
                                <div>
                                    <h3>Top Restaurants by Revenue</h3>
                                    <p>
                                        Track the highest-performing restaurants based on revenue this month.
                                    </p>
                                </div>
                            </div>
                            <!-- Right -->
                            <div class="subscription-summary">
                                <div class="summary-pill revenue">
                                    <i class="fas fa-calendar-alt"></i>
                                    <span>Current Month</span>
                                    <strong>{{ now()->format('M Y') }}</strong>
                                </div>
                            </div>
                        </div>
                        <div class="rms-table-wrap">
                            <table class="rms-table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Restaurant</th>
                                        <th>Outlets</th>
                                        <th>Orders</th>
                                        <th class="text-end">Revenue</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($topRestaurants as $restaurant)
                                        <tr>
                                            <td>
                                                <span
                                                    class="badge bg-{{ $loop->iteration <= 3 ? 'warning' : 'light' }} text-dark rounded-circle"
                                                    style="width: 30px; height: 30px; display: inline-flex; align-items: center; justify-content: center; font-weight: 700;">
                                                    {{ $loop->iteration }}
                                                </span>
                                            </td>
                                            <td><strong>{{ $restaurant->name ?? 'N/A' }}</strong></td>
                                            <td>
                                                <span class="status-expired"><i
                                                        class="fas fa-code-branch"></i>{{ $restaurant->total_branches }}</span>
                                            </td>
                                            <td>
                                                <span class="plan-pill"> <i class="fas fa-shopping-cart"></i>
                                                    {{ number_format($restaurant->total_orders) }}</span>
                                            </td>
                                            <td class="text-end">
                                                <button type="button"
                                                    class="btn btn-sm btn-outline-warning rounded-circle viewRevenue"
                                                    data-slug="{{ $restaurant->slug }}" title="View Revenue Details"
                                                    style="width:32px; height:32px; padding:0;">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="rms-empty-state">
                                                <i class="fas fa-chart-line"></i>
                                                <p>No data available.</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>


            {{-- ===== REVENUE CHART ===== --}}
            <div class="row mb-4">
                <div class="col-12 col-sm-12 col-lg-12">
                    <div class="rms-dashboard-card">

                        <div class="rms-card-body">
                            <div class="row">
                                <div class="col-lg-9">
                                    <!-- Chart with color-coded areas -->
                                    <div id="chart1" class="rms-chart-container"></div>

                                    <!-- Color-coded revenue stats -->
                                    <div class="rms-revenue-stats">
                                        <div class="rms-revenue-item" style="border-left: 4px solid #4e73df;">
                                            <div class="rms-revenue-icon" style="color: #4e73df;">
                                                <i class="fas fa-calendar-day"></i>
                                            </div>
                                            <div class="rms-revenue-value" id="todayRevenue" style="color: #4e73df;">
                                                ₹{{ number_format($revenue['today']['amount'] ?? 0) }}
                                            </div>
                                            <div class="rms-revenue-label">Today's Earnings</div>
                                        </div>

                                        <div class="rms-revenue-item" style="border-left: 4px solid #1cc88a;">
                                            <div class="rms-revenue-icon" style="color: #1cc88a;">
                                                <i class="fas fa-calendar-week"></i>
                                            </div>
                                            <div class="rms-revenue-value" id="weeklyRevenueDisplay" style="color: #1cc88a;">
                                                ₹{{ number_format($revenue['weekly']['amount'] ?? 0) }}
                                            </div>
                                            <div class="rms-revenue-label">Weekly Earnings</div>
                                        </div>

                                        <div class="rms-revenue-item" style="border-left: 4px solid #36b9cc;">
                                            <div class="rms-revenue-icon" style="color: #36b9cc;">
                                                <i class="fas fa-calendar-month"></i>
                                            </div>
                                            <div class="rms-revenue-value" id="monthlyRevenueDisplay" style="color: #36b9cc;">
                                                ₹{{ number_format($revenue['monthly']['amount'] ?? 0) }}
                                            </div>
                                            <div class="rms-revenue-label">Monthly Earnings</div>
                                        </div>

                                        <div class="rms-revenue-item" style="border-left: 4px solid #f6c23e;">
                                            <div class="rms-revenue-icon" style="color: #f6c23e;">
                                                <i class="fas fa-calendar-year"></i>
                                            </div>
                                            <div class="rms-revenue-value" id="yearlyRevenueDisplay" style="color: #f6c23e;">
                                                ₹{{ number_format($revenue['yearly']['amount'] ?? 0) }}
                                            </div>
                                            <div class="rms-revenue-label">Yearly Earnings</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-3">
                                    <div class="rms-stats-panel">
                                        <div class="rms-stat-row">
                                            <span class="rms-stat-label"><i class="fas fa-shopping-cart"></i> Total
                                                Orders</span>
                                            <span class="rms-stat-value" id="totalOrders" style="color: #4e73df;">
                                                {{ number_format($revenue['total']['orders'] ?? 0) }}
                                            </span>
                                        </div>
                                        <div class="rms-stat-row">
                                            <span class="rms-stat-label"><i class="fas fa-money-bill"></i> Total Revenue</span>
                                            <span class="rms-stat-value text-success" id="totalRevenue" style="color: #1cc88a;">
                                                ₹{{ number_format($revenue['total']['amount'] ?? 0) }}
                                            </span>
                                        </div>
                                        <div class="rms-stat-row">
                                            <span class="rms-stat-label"><i class="fas fa-store"></i> Restaurants</span>
                                            <span class="rms-stat-value"
                                                style="color: #36b9cc;">{{ $restaurants->count() }}</span>
                                        </div>
                                        <div class="rms-stat-row">
                                            <span class="rms-stat-label"><i class="fas fa-users"></i> Total Users</span>
                                            <span class="rms-stat-value"
                                                style="color: #f6c23e;">{{ $totalUsers ?? 'N/A' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ===== SMALL CHARTS ===== --}}
            <div class="row">
                <div class="col-12 col-sm-12 col-lg-6">
                    <div class="rms-dashboard-card">
                        <div class="rms-card-header">
                            <h5>
                                <i class="fas fa-chart-pie"></i>
                                Order Status
                            </h5>
                        </div>
                        <div class="rms-card-body">
                            <div id="chart4" class="rms-chart-small"></div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-sm-12 col-lg-6">
                    <div class="rms-dashboard-card">
                        <div class="rms-card-header">
                            <h5>
                                <i class="fas fa-chart-line"></i>
                                Revenue Trend
                            </h5>
                        </div>
                        <div class="rms-card-body">
                            <div id="chart3" class="rms-chart-small"></div>
                        </div>
                    </div>
                </div>
            </div>
        @endcan
        @can('view-payment')
            <div class="row g-4">
                {{-- Pending Verification --}}
                <div class="col-lg-4 col-md-6 mb-3">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <div>
                                    <p class="text-muted text-uppercase small fw-semibold mb-1">
                                        Pending Verification
                                    </p>
                                    <h2 class="fw-bold text-warning mb-1">
                                        {{ $pendingVerification }}
                                    </h2>
                                    <small class="text-muted">
                                        Payments Awaiting Approval
                                    </small>
                                </div>

                                <div class="bg-warning bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center"
                                    style="width:65px;height:65px;">
                                    <i class="fas fa-clock fa-lg text-white"></i>
                                </div>
                            </div>

                            <div class="progress" style="height:6px;">
                                <div class="progress-bar bg-warning w-100"></div>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- Verified Today --}}
                <div class="col-lg-4 col-md-6 mb-3">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <div>
                                    <p class="text-muted text-uppercase small fw-semibold mb-1">
                                        Verified Today
                                    </p>
                                    <h2 class="fw-bold text-success mb-1">
                                        {{ $verifiedToday }}
                                    </h2>
                                    <small class="text-muted">
                                        Payments Approved Today
                                    </small>
                                </div>

                                <div class="bg-success bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center"
                                    style="width:65px;height:65px;">
                                    <i class="fas fa-check-circle fa-lg text-white"></i>
                                </div>
                            </div>

                            <div class="progress" style="height:6px;">
                                <div class="progress-bar bg-success w-100"></div>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- Today's Collection --}}
                <div class="col-lg-4 col-md-6 mb-3">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <div>
                                    <p class="text-muted text-uppercase small fw-semibold mb-1">
                                        Today's Collection
                                    </p>
                                    <h2 class="fw-bold text-primary mb-1">
                                        ₹{{ number_format($todayCollection) }}
                                    </h2>
                                    <small class="text-muted">
                                        Verified Payment Amount
                                    </small>
                                </div>

                                <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center"
                                    style="width:65px;height:65px;">
                                    <i class="fas fa-wallet fa-lg text-white"></i>
                                </div>
                            </div>

                            <div class="progress" style="height:6px;">
                                <div class="progress-bar bg-primary w-100"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endcan

        @can('order-status')
            <div class="row g-4 mt-4 order-status">

                {{-- Pending Orders --}}
                <div class="col-xl-3 col-md-6 mb-3">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <span class="text-muted text-uppercase small fw-semibold">
                                        Pending Orders
                                    </span>

                                    <h2 class="fw-bold text-warning mt-2 mb-1">
                                        {{ $orderStatus['pending'] }}
                                    </h2>

                                    <small class="text-muted">
                                        Waiting for confirmation
                                    </small>
                                </div>

                                <div class="rounded-circle bg-warning bg-opacity-10 p-3">
                                    <i class="fas fa-clock text-white fa-lg"></i>
                                </div>
                            </div>

                            <hr class="my-3">

                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">Current Status</small>
                                <span class="badge bg-warning-subtle text-warning px-3 py-2">
                                    Pending
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Preparing Orders --}}
                <div class="col-xl-3 col-md-6 mb-3">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <span class="text-muted text-uppercase small fw-semibold">
                                        Preparing Orders
                                    </span>

                                    <h2 class="fw-bold text-info mt-2 mb-1">
                                        {{ $orderStatus['preparing'] }}
                                    </h2>

                                    <small class="text-muted">
                                        Kitchen in progress
                                    </small>
                                </div>

                                <div class="rounded-circle bg-info bg-opacity-10 p-3">
                                    <i class="fas fa-utensils text-white fa-lg"></i>
                                </div>
                            </div>

                            <hr class="my-3">

                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">Current Status</small>
                                <span class="badge bg-info-subtle text-info px-3 py-2">
                                    Preparing
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Completed Orders --}}
                <div class="col-xl-3 col-md-6 mb-3">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <span class="text-muted text-uppercase small fw-semibold">
                                        Completed Orders
                                    </span>

                                    <h2 class="fw-bold text-success mt-2 mb-1">
                                        {{ $orderStatus['completed'] }}
                                    </h2>

                                    <small class="text-muted">
                                        Ready for delivery
                                    </small>
                                </div>

                                <div class="rounded-circle bg-success bg-opacity-10 p-3">
                                    <i class="fas fa-check-circle text-white fa-lg"></i>
                                </div>
                            </div>

                            <hr class="my-3">

                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">Current Status</small>
                                <span class="badge bg-success-subtle text-success px-3 py-2">
                                    Completed
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Delivered Orders --}}
                <div class="col-xl-3 col-md-6 mb-3">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <span class="text-muted text-uppercase small fw-semibold">
                                        Delivered Orders
                                    </span>

                                    <h2 class="fw-bold text-primary mt-2 mb-1">
                                        {{ $orderStatus['delivered'] }}
                                    </h2>

                                    <small class="text-muted">
                                        Successfully delivered
                                    </small>
                                </div>

                                <div class="rounded-circle bg-primary bg-opacity-10 p-3">
                                    <i class="fas fa-truck text-primary fa-lg"></i>
                                </div>
                            </div>

                            <hr class="my-3">

                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">Current Status</small>
                                <span class="badge bg-primary-subtle text-info px-3 py-2">
                                    Delivered
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        @endcan

        @php
            $restaurantSlug = request()->route('restaurant');
            $branchSlug = request()->route('branch');
        @endphp
        @can('prepared-index-dashboard')
            <div class="card shadow mt-4" id="preparedOrdersSection">
                <div class="subscription-header">
                    <div class="subscription-title">
                        <div class="header-icon">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <div>
                            <h3>Prepared and Pending Orders</h3>
                            <p>Monitor recent prepared and pending orders.</p>
                        </div>
                    </div>
                    <div class="subscription-summary" id="preparedOrdersSummary">
                        @php
                            $restaurantSlug = request()->route('restaurant');
                            $branchSlug = request()->route('branch');
                        @endphp

                        @if (!empty($restaurantSlug) && !empty($branchSlug))
                            <a href="{{ route('branch.orders.index', ['restaurant' => $restaurantSlug, 'branch' => $branchSlug]) }}"
                                class="btn btn-primary" id="viewAllBtn">
                                View All
                            </a>
                        @elseif (!empty($restaurantSlug))
                            <a href="{{ route('restaurant.orders.index', ['restaurant' => $restaurantSlug]) }}"
                                class="btn btn-primary" id="viewAllBtn">
                                View All
                            </a>
                        @endif
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Token</th>
                                    <th>Customer</th>
                                    <th>Table</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="preparedOrdersTableBody">
                                @forelse($preparedOrders as $order)
                                    <tr>
                                        <td>{{ $order->token_no }}</td>
                                        <td>{{ $order->customer_name }}</td>
                                        <td>{{ $order->table_no ?? '-' }}</td>
                                        <td>{{ $currencySymbol ?? '₹' }}{{ number_format($order->total, 2) }}</td>
                                        <td>{{ $order->status }}</td>
                                        <td>
                                            <div class="d-flex gap-2">
                                                @if (!empty($restaurantSlug) && !empty($branchSlug))
                                                    <a href="{{ route('branch.orders.show', ['restaurant' => $restaurantSlug, 'branch' => $branchSlug, 'order' => $order->id]) }}"
                                                        class="btn btn-md btn-info">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                @elseif(!empty($restaurantSlug))
                                                    <a href="{{ route('restaurant.orders.show', ['restaurant' => $restaurantSlug, 'order' => $order->id]) }}"
                                                        class="btn btn-md btn-info">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">Choose a restaurant and branch-let the numbers do the
                                            talking.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endcan

        @can('inventory-dashboard')
            <div class="row mt-4">
                <div class="col-lg-12">
                    <div class="card shadow">
                        <div class="subscription-header">
                            <div class="subscription-title">
                                <div class="header-icon">
                                    <i class="fas fa-calendar-check"></i>
                                </div>
                                <div>
                                    <h3>Inventory Stock Summary</h3>
                                    <p>
                                        Monitor all restaurant subscriptions and renewal status.
                                    </p>
                                </div>
                            </div>

                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover" id="StockTable">
                                    <thead>
                                        <tr>
                                            @if (auth()->user()->role == 'super_admin')
                                                <th>Restaurant</th>
                                            @endif
                                            @if (auth()->user()->role != 'branch_manager')
                                                <th>Branch</th>
                                            @endif
                                            <th>Item</th>
                                            <th>Remaining</th>
                                            <th>Minimum</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($inventoryStocks as $item)
                                            <tr>
                                                @if(auth()->user()->role == 'super_admin')
                                                    <td>{{ optional($item->restaurant)->name }}</td>
                                                @endif

                                                @if(auth()->user()->role != 'branch_manager')
                                                    <td>{{ optional($item->branch)->name }}</td>
                                                @endif
                                                <td>{{ $item->name }}</td>
                                                <td>
                                                    {{ $item->remaining_stock }}
                                                    {{ $item->unit }}
                                                </td>
                                                <td>
                                                    {{ $item->minimum_stock }}
                                                    {{ $item->unit }}
                                                </td>
                                                <td>
                                                    @if($item->remaining_stock <= $item->minimum_stock)
                                                        <span class="badge badge-danger">
                                                            Low Stock
                                                        </span>
                                                    @else
                                                        <span class="badge badge-success">
                                                            Available
                                                        </span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="{{ auth()->user()->role == 'super_admin' ? 6 : (auth()->user()->role == 'branch_manager' ? 4 : 5) }}"
                                                    class="text-center">
                                                    No Inventory Found
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
        @endcan



    </section>
    <div class="modal fade" id="revenueModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">
                        Restaurant Revenue Details
                    </h5>

                    <button class="btn-close" data-bs-dismiss="modal">
                    </button>
                </div>

                <div class="modal-body">

                    <table class="table table-bordered">

                        <thead>
                            <tr>
                                <th>Country</th>
                                <th>Branch</th>
                                <th>Orders</th>
                                <th class="text-end">
                                    Revenue
                                </th>
                            </tr>
                        </thead>

                        <tbody id="revenueBody">

                        </tbody>

                    </table>

                </div>

            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts@3.45.0/dist/apexcharts.min.js"></script>
<script>
    // Initialize variables
    let currentRestaurant = '';
    let currentBranch = '';
    let currencySymbol = '{{ $currencySymbol ?? "₹" }}';
    let revenueChart;
    let donutChart;
    let revenueTrendChart;

    // Get branch currency from PHP
    let branchCurrency = '{{ isset($currentBranch) && $currentBranch->country ? $currentBranch->country->currency_symbol : ($currencySymbol ?? "₹") }}';

    @if(auth()->user()->role == 'super_admin')
        branchCurrency = '{{ $currencySymbol ?? "₹" }}';
    @endif

    currencySymbol = branchCurrency;

    @if(auth()->user()->role != 'super_admin' && auth()->user()->role != 'owner' && isset($currentBranch))
        currentBranch = '{{ $currentBranch->id }}';
    @endif

    // ============ FUNCTIONS ============

    function loadBranches(restaurantId) {
        const branchSelect = document.getElementById('branchFilter');
        if (!branchSelect || !restaurantId) {
            if (branchSelect) {
                branchSelect.innerHTML = '<option value="">All Branches</option>';
            }
            return;
        }

        branchSelect.innerHTML = '<option value="">Loading branches...</option>';
        branchSelect.disabled = true;

        fetch('/dashboard/branches/' + restaurantId)
            .then(response => response.json())
            .then(data => {
                branchSelect.innerHTML = '<option value="">All Branches</option>';
                branchSelect.disabled = false;

                let branches = [];
                if (Array.isArray(data)) {
                    branches = data;
                } else if (data.branches && Array.isArray(data.branches)) {
                    branches = data.branches;
                } else if (data.data && Array.isArray(data.data)) {
                    branches = data.data;
                }

                if (branches.length > 0) {
                    branches.forEach(branch => {
                        const option = document.createElement('option');
                        option.value = branch.id;
                        option.textContent = branch.name;
                        branchSelect.appendChild(option);

                        @if(auth()->user()->role != 'super_admin' && auth()->user()->role != 'owner' && isset($currentBranch))
                            if (branch.id == {{ $currentBranch->id }}) {
                                option.selected = true;
                            }
                        @endif
                    });
                } else {
                    const option = document.createElement('option');
                    option.value = '';
                    option.textContent = 'No branches available';
                    branchSelect.appendChild(option);
                }
            })
            .catch(() => {
                branchSelect.innerHTML = '<option value="">Error loading branches</option>';
                branchSelect.disabled = false;
            });
    }

    // Function to reset all revenue elements to just "0" without currency
    function resetRevenueToZero() {
    // Reset all revenue display elements to just "0" without currency
        const revenueIds = [
            'todayRevenue',
            'yesterdayRevenue',
            'weeklyRevenueDisplay',
            'monthlyRevenueDisplay',
            'yearlyRevenueDisplay',
            'totalRevenue',
            'weeklyRevenue',
            'monthlyRevenue',
            'yearlyRevenue'
        ];

        revenueIds.forEach(id => {
            const el = document.getElementById(id);
            if (el) {
                el.innerHTML = '0';
                // Keep color styling but remove currency
                if (id === 'todayRevenue') el.style.color = '#4e73df';
                else if (id === 'yesterdayRevenue') el.style.color = '#e74a3b';
                else if (id === 'weeklyRevenueDisplay' || id === 'weeklyRevenue') el.style.color = '#1cc88a';
                else if (id === 'monthlyRevenueDisplay' || id === 'monthlyRevenue') el.style.color = '#36b9cc';
                else if (id === 'yearlyRevenueDisplay' || id === 'yearlyRevenue') el.style.color = '#f6c23e';
                else if (id === 'totalRevenue') el.style.color = '#1cc88a';
            }
        });

        // Reset total orders
        const totalOrders = document.getElementById('totalOrders');
        if (totalOrders) {
            totalOrders.innerHTML = '0';
            totalOrders.style.color = '#4e73df';
        }
    }

    function refreshAllData() {
        // If no restaurant selected, reset everything to "0" without currency
        if (!currentRestaurant && !currentBranch) {
            resetRevenueToZero();

            if (typeof revenueChart !== 'undefined' && revenueChart) {
                revenueChart.updateSeries([{
                    data: [0, 0, 0, 0, 0]
                }]);
            }

            if (typeof donutChart !== 'undefined' && donutChart) {
                donutChart.updateSeries([0, 0, 0, 0]);
            }

            updatePreparedOrdersTable([]);
            return;
        }

        let url = `/dashboard/data?restaurant_id=${currentRestaurant}`;
        if (currentBranch) {
            url += `&branch_id=${currentBranch}`;
        }

        fetch(url)
            .then(response => response.json())
            .then(data => {
                currencySymbol = data.currencySymbol || branchCurrency || '₹';

                if (data.preparedOrders !== undefined) {
                    updatePreparedOrdersTable(data.preparedOrders);
                }

                // Update revenue cards - show currency only if amount > 0
                const revenueData = [
                    { id: 'todayRevenue', amount: Number(data.revenue?.today?.amount || 0), color: '#4e73df' },
                    { id: 'yesterdayRevenue', amount: Number(data.revenue?.yesterday?.amount || 0), color: '#e74a3b' },
                    { id: 'weeklyRevenueDisplay', amount: Number(data.revenue?.weekly?.amount || 0), color: '#1cc88a' },
                    { id: 'monthlyRevenueDisplay', amount: Number(data.revenue?.monthly?.amount || 0), color: '#36b9cc' },
                    { id: 'yearlyRevenueDisplay', amount: Number(data.revenue?.yearly?.amount || 0), color: '#f6c23e' }
                ];

                revenueData.forEach(item => {
                    const el = document.getElementById(item.id);
                    if (el) {
                        if (item.amount > 0) {
                            el.innerHTML = currencySymbol + item.amount.toLocaleString();
                        } else {
                            el.innerHTML = '0';
                        }
                        el.style.color = item.color;
                    }
                });

                // Total Revenue
                const totalAmount = Number(data.revenue?.total?.amount || 0);
                const totalRevenueEl = document.getElementById('totalRevenue');
                if (totalRevenueEl) {
                    if (totalAmount > 0) {
                        totalRevenueEl.innerHTML = currencySymbol + totalAmount.toLocaleString();
                    } else {
                        totalRevenueEl.innerHTML = '0';
                    }
                    totalRevenueEl.style.color = '#1cc88a';
                }

                // Total Orders
                const totalOrdersEl = document.getElementById('totalOrders');
                if (totalOrdersEl) {
                    totalOrdersEl.innerHTML = Number(data.revenue?.total?.orders || 0).toLocaleString();
                    totalOrdersEl.style.color = '#4e73df';
                }

                // Update charts
                if (typeof revenueChart !== 'undefined' && revenueChart) {
                    revenueChart.updateSeries([{
                        data: [
                            Number(data.revenue?.today?.amount || 0),
                            Number(data.revenue?.yesterday?.amount || 0),
                            Number(data.revenue?.weekly?.amount || 0),
                            Number(data.revenue?.monthly?.amount || 0),
                            Number(data.revenue?.yearly?.amount || 0)
                        ]
                    }]);
                }

                if (typeof donutChart !== 'undefined' && donutChart) {
                    donutChart.updateSeries([
                        data.orderStatus?.pending ?? 0,
                        data.orderStatus?.preparing ?? 0,
                        data.orderStatus?.completed ?? 0,
                        data.orderStatus?.delivered ?? 0
                    ]);
                }

                if (typeof revenueTrendChart !== 'undefined' && revenueTrendChart) {
                    revenueTrendChart.updateSeries([
                        {
                            name: 'Orders',
                            data: [
                                Number(data.revenue?.today?.orders || 0),
                                Number(data.revenue?.yesterday?.orders || 0),
                                Number(data.revenue?.weekly?.orders || 0),
                                Number(data.revenue?.monthly?.orders || 0),
                                Number(data.revenue?.yearly?.orders || 0)
                            ]
                        },
                        {
                            name: 'Revenue (' + currencySymbol + ')',
                            data: [
                                Number(data.revenue?.today?.amount || 0),
                                Number(data.revenue?.yesterday?.amount || 0),
                                Number(data.revenue?.weekly?.amount || 0),
                                Number(data.revenue?.monthly?.amount || 0),
                                Number(data.revenue?.yearly?.amount || 0)
                            ]
                        }
                    ]);
                }
            })
            .catch(() => { });
    }

    function resetAllData() {
    resetRevenueToZero();

    if (typeof revenueChart !== 'undefined' && revenueChart) {
        revenueChart.updateSeries([{ data: [0, 0, 0, 0, 0] }]);
    }

    if (typeof donutChart !== 'undefined' && donutChart) {
        donutChart.updateSeries([0, 0, 0, 0]);
    }

    if (typeof revenueTrendChart !== 'undefined' && revenueTrendChart) {
        revenueTrendChart.updateSeries([
            {
                name: 'Orders',
                data: [0, 0, 0, 0, 0]
            },
            {
                name: 'Revenue (' + currencySymbol + ')',
                data: [0, 0, 0, 0, 0]
            }
        ]);
    }

    updatePreparedOrdersTable([]);
}

    function updatePreparedOrdersTable(orders) {
        const tbody = document.getElementById('preparedOrdersTableBody');
        if (!tbody) return;

        let html = '';

        if (!orders || orders.length === 0) {
            html = `<tr><td colspan="6" class="text-center">No orders found for the selected filters.</td></tr>`;
        } else {
            const restaurantSlug = '{{ $restaurantSlug ?? "" }}';
            const branchSlug = '{{ $branchSlug ?? "" }}';

            orders.forEach(order => {
                let actionHtml = '';

                if (restaurantSlug && branchSlug) {
                    actionHtml = `
                        <a href="/${restaurantSlug}/${branchSlug}/orders/${order.id}"
                        class="btn btn-md btn-info">
                            <i class="fas fa-eye"></i>
                        </a>`;
                } else if (restaurantSlug) {
                    actionHtml = `
                        <a href="/${restaurantSlug}/orders/${order.id}"
                        class="btn btn-md btn-info">
                            <i class="fas fa-eye"></i>
                        </a>`;
                }

                html += `
                    <tr>
                        <td>${order.token_no || ''}</td>
                        <td>${order.customer_name || ''}</td>
                        <td>${order.table_no ?? '-'}</td>
                        <td>${currencySymbol}${Number(order.total || 0).toLocaleString('en-IN', { minimumFractionDigits: 2 })}</td>
                        <td>${order.status || ''}</td>
                        <td>
                            <div class="d-flex gap-2">${actionHtml}</div>
                        </td>
                    </tr>`;
            });
        }

        tbody.innerHTML = html;
    }

    function initializeCharts() {
        @php
            $revAmount = $revenueData['amount'] ?? [0, 0, 0, 0, 0];
            $revOrders = $revenueData['orders'] ?? [0, 0, 0, 0, 0];
            $invNames = $inventoryAlerts->pluck('name')->toArray();
            $invStock = $inventoryAlerts->pluck('remaining_stock')->toArray();

            $chartCurrency = '₹';
            if (auth()->user()->role != 'super_admin' && isset($currentBranch) && $currentBranch->country) {
                $chartCurrency = $currentBranch->country->currency_symbol;
            } elseif (auth()->user()->role == 'super_admin') {
                $chartCurrency = $currencySymbol ?? '₹';
            }
        @endphp

        // Revenue Chart
        var options1 = {
            series: [{
                name: 'Revenue',
                data: @json($revAmount)
            }],
            chart: {
                height: 380,
                type: 'area',
                toolbar: {
                    show: false
                },
                animations: {
                    enabled: true,
                    easing: 'easeinout',
                    speed: 800
                }
            },
            colors: ['#4e73df'],
            dataLabels: {
                enabled: false
            },
            stroke: {
                curve: 'smooth',
                width: 3
            },
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.7,
                    opacityTo: 0.3,
                    stops: [0, 90, 100]
                }
            },
            xaxis: {
                categories: ['Today', 'Yesterday', 'This Week', 'This Month', 'This Year'],
                labels: {
                    style: {
                        colors: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b'],
                        fontSize: '12px',
                        fontWeight: 'bold'
                    }
                }
            },
            yaxis: {
                labels: {
                    formatter: function (val) {
                        return "{{ $chartCurrency }}" + Number(val).toLocaleString();
                    }
                }
            },
            tooltip: {
                y: {
                    formatter: function (val) {
                        return "{{ $chartCurrency }}" + Number(val).toLocaleString();
                    }
                }
            },
            markers: {
                size: 5,
                colors: ['#4e73df'],
                strokeColors: '#fff',
                strokeWidth: 2,
                hover: {
                    size: 7
                }
            },
            grid: {
                borderColor: '#e0e0e0',
                row: {
                    colors: ['#f8f9fa', 'transparent'],
                    opacity: 0.5
                }
            }
        };

        revenueChart = new ApexCharts(document.querySelector("#chart1"), options1);
        revenueChart.render();

        // Donut Chart
        var options4 = {
            series: [
                {{ $orderStatus['pending'] ?? 0 }},
                {{ $orderStatus['preparing'] ?? 0 }},
                {{ $orderStatus['completed'] ?? 0 }},
                {{ $orderStatus['delivered'] ?? 0 }}
            ],
            chart: {
                height: 300,
                type: 'donut'
            },
            labels: ['Pending', 'Preparing', 'Completed', 'Delivered'],
            colors: ['#f6c23e', '#36b9cc', '#1cc88a', '#FA8AFA'],
            legend: {
                position: 'bottom'
            }
        };

        donutChart = new ApexCharts(document.querySelector("#chart4"), options4);
        donutChart.render();

        // Orders Bar Chart
        var options3 = {
            series: [
                {
                    name: 'Orders',
                    type: 'column',
                    data: @json($revOrders)
                },
                {
                    name: 'Revenue ({{ $chartCurrency }})',
                    type: 'line',
                    data: @json($revAmount)
                }
            ],
            chart: {
                height: 300,
                type: 'line'
            },
            colors: ['#4e73df', '#28a745'],
            stroke: {
                width: [0, 3]
            },
            plotOptions: {
                bar: {
                    borderRadius: 4
                }
            },
            xaxis: {
                categories: ['Today', 'Yesterday', 'Week', 'Month', 'Year']
            },
            yaxis: [
                {
                    title: {
                        text: 'Orders'
                    }
                },
                {
                    opposite: true,
                    title: {
                        text: 'Revenue ({{ $chartCurrency }})'
                    },
                    labels: {
                        formatter: function(val) {
                            return '{{ $chartCurrency }}' + Number(val).toLocaleString();
                        }
                    }
                }
            ],
            tooltip: {
                y: [
                    {
                        formatter: function(val) {
                            return val + ' Orders';
                        }
                    },
                    {
                        formatter: function(val) {
                            return '{{ $chartCurrency }}' + Number(val).toLocaleString();
                        }
                    }
                ]
            }
        };

        revenueTrendChart = new ApexCharts(
            document.querySelector("#chart3"),
            options3
        );
        revenueTrendChart.render();

        // Inventory Chart
        var options2 = {
            series: [{
                name: 'Stock',
                data: @json($invStock)
            }],
            chart: {
                height: 300,
                type: 'bar'
            },
            colors: ['#e74a3b'],
            plotOptions: {
                bar: {
                    horizontal: true,
                    borderRadius: 4
                }
            },
            xaxis: {
                categories: @json($invNames ?: ['No Alerts'])
            }
        };

        new ApexCharts(document.querySelector("#chart2"), options2).render();
    }

    // ============ INITIALIZE ============

    document.addEventListener('DOMContentLoaded', function () {
        const restaurantFilter = document.getElementById('restaurantFilter');
        const branchFilter = document.getElementById('branchFilter');

        initializeCharts();

        // Reset all revenue to "0" on initial load if nothing selected
        if (!currentRestaurant && !currentBranch) {
            resetRevenueToZero();
        }

        if (restaurantFilter) {
            currentRestaurant = restaurantFilter.value || '';

            restaurantFilter.addEventListener('change', function() {
                currentRestaurant = this.value;
                currentBranch = '';

                if (branchFilter) {
                    branchFilter.innerHTML = '<option value="">All Branches</option>';
                }

                if (currentRestaurant) {
                    loadBranches(currentRestaurant);
                    setTimeout(refreshAllData, 500);
                } else {
                    // Reset everything to zero when "All Restaurants" is selected
                    resetAllData();
                    updatePreparedOrdersTable([]);
                }
            });

            if (currentRestaurant) {
                loadBranches(currentRestaurant);
                setTimeout(refreshAllData, 500);
            } else {
                if (branchFilter) {
                    branchFilter.innerHTML = '<option value="">All Branches</option>';
                }
                resetAllData();
                updatePreparedOrdersTable([]);
            }
        }

        if (branchFilter) {
            branchFilter.addEventListener('change', function() {
                currentBranch = this.value;
                refreshAllData();
            });
        }

        @if(auth()->user()->role != 'super_admin' && auth()->user()->role != 'owner' && isset($currentBranch))
            setTimeout(function() {
                currentRestaurant = '{{ $currentBranch->restaurant_id }}';
                currentBranch = '{{ $currentBranch->id }}';
                refreshAllData();
            }, 1000);
        @endif
    });

    // ============ VIEW REVENUE MODAL ============
    document.querySelectorAll('.viewRevenue').forEach(button => {
        button.addEventListener('click', function () {
            const slug = this.dataset.slug;

            fetch(`/restaurants/${slug}/revenue-details`)
                .then(response => response.json())
                .then(res => {
                    let html = '';
                    res.forEach(item => {
                        html += `
                            <tr>
                                <td>${item.country ?? '-'}</td>
                                <td>${item.branch}</td>
                                <td>${item.orders}</td>
                                <td class="text-end">
                                    ${item.currency} ${Number(item.revenue).toLocaleString()}
                                </td>
                            </tr>
                        `;
                    });
                    document.getElementById('revenueBody').innerHTML = html;
                    new bootstrap.Modal(document.getElementById('revenueModal')).show();
                })
                .catch(() => {});
        });
    });

    @if(isset($currentBranch) && $currentBranch && $currentBranch->country)
        const branchTimezone = @json($currentBranch->country->timezone);
        function updateBranchClock() {
            const now = new Date();
            const time = now.toLocaleTimeString('en-US', {
                timeZone: branchTimezone,
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
                hour12: true
            });
            document.getElementById('branchClock').textContent = time;
        }
        updateBranchClock();
        setInterval(updateBranchClock, 1000);
    @endif
</script>
@endsection
