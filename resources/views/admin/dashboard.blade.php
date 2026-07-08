@extends('layouts.app')

@section('content')

{{-- Country & Timezone Info Card --}}
@if (isset($currentBranch) && $currentBranch)
    <div class="row mb-4">
        <div class="col-12">
            <div class="card premium-block shadow-sm border-0">
                <div class="card-body d-flex align-items-center gap-3">
                    <i class="fas fa-globe-asia fa-3x text-primary"></i>
                    <div>
                        @php
                            $country = $currentBranch->country()->first();
                        @endphp

                        <h5 class="mb-1 text-dark">
                            {{ $country?->name ?? 'Country Not Assigned' }}
                            <small class="text-muted">({{ $country?->iso_code ?? '' }})</small>
                        </h5>
                        <p class="mb-0 text-muted">
                            <i class="fas fa-clock"></i>
                            Timezone: <strong>{{ $country?->timezone ?? 'N/A' }}</strong>
                        </p>
                    </div> 
                </div>
            </div>
        </div>
    </div>
@endif
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
            {{-- Revenue Cards --}}
            @can('today-revenue')
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="dashboard-card restaurant-card">
                        <div class="header-icon">
                            <i class="fas fa-coins"></i>
                        </div>
                        <div class="card-content">
                            <h6>Today Revenue</h6>
                            <h3 class="mb-1">₹{{ number_format($revenue['today']['amount']) }}</h3>
                            <p class=" mb-0">{{ $revenue['today']['orders'] }} Orders</p>
                        </div>
                    </div>
                </div>
            @endcan

            @can('yesterday-revenue')
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="dashboard-card restaurant-card">
                        <div class="header-icon">
                            <i class="fas fa-coins"></i>
                        </div>
                        <div class="card-content">
                            <h6>Yesterday Revenue</h6>
                            @if (isset($revenue))
                                <h3 class=" mb-1">₹{{ number_format($revenue['yesterday']['amount']) }}</h3>
                                <p class=" mb-0">{{ $revenue['yesterday']['orders'] }} Orders</p>
                            @endif
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
                             <h3 class=" mb-1">₹{{ number_format($revenue['weekly']['amount']) }}</h3>
                            <p class=" mb-0">{{ $revenue['weekly']['orders'] }} Orders</p>
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
                            <h6 class=" mb-2">Monthly Revenue</h6>
                            @if (isset($revenue))
                                <h3 class=" mb-1">₹{{ number_format($revenue['monthly']['amount']) }}</h3>
                                <p class=" mb-0">{{ $revenue['monthly']['orders'] }} Orders</p>
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
                            <h6 class=" mb-2">Yearly Revenue</h6>
                            @if (isset($revenue))
                                <h3 class="mb-1">₹{{ number_format($revenue['yearly']['amount']) }}</h3>
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
                                        <td><span class="plan-pill"><i class="fas fa-crown"></i> {{ $subscription->plan?->name ?? $subscription->billing_cycle }}</span></td>
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

                        <strong>{{ $subscriptions->filter(fn($s)=> now()->diffInDays(\Carbon\Carbon::parse($s->end_date),false) > 30)->count() }}</strong>

                    </div>

                    <div class="summary-pill warning">

                        <i class="fas fa-clock"></i>

                        <span>Expiring Soon</span>

                        <strong>{{ $subscriptions->filter(function($s){
                            $d = now()->diffInDays(\Carbon\Carbon::parse($s->end_date),false);
                            return $d > 7 && $d <=30;
                        })->count() }}</strong>

                    </div>

                    <div class="summary-pill danger">

                        <i class="fas fa-exclamation-triangle"></i>

                        <span>Critical</span>

                        <strong>{{ $subscriptions->filter(fn($s)=> now()->diffInDays(\Carbon\Carbon::parse($s->end_date),false)<=7)->count() }}</strong>

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
                                            <span class="fw-bold {{ $days <= 7 ? 'text-danger' : ($days <= 30 ? 'text-warning' : 'text-success') }}">
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
                                            <span class="fw-bold {{ $item->remaining_stock <= 0 ? 'text-danger' : ($item->remaining_stock <= $item->minimum_stock ? 'text-warning' : 'text-success') }}">
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
                                            <span class="badge bg-{{ $loop->iteration <= 3 ? 'warning' : 'light' }} text-dark rounded-circle"
                                                  style="width: 30px; height: 30px; display: inline-flex; align-items: center; justify-content: center; font-weight: 700;">
                                                {{ $loop->iteration }}
                                            </span>
                                        </td>
                                        <td><strong>{{ $restaurant->name ?? 'N/A' }}</strong></td>
                                        <td>
                                            <span class="status-expired"><i class="fas fa-code-branch"></i>{{ $restaurant->total_branches }}</span></td>
                                        <td>
                                            <span class="plan-pill"> <i class="fas fa-shopping-cart"></i> {{ number_format($restaurant->total_orders) }}</span>
                                        </td>
                                        <td class="text-end fw-bold">
                                            <span class="rms-badge rms-badge--sucess" >
                                            <i class="fas fa-rupee-sign"></i> {{ number_format($restaurant->total_revenue, 2) }} </span>
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
                    <div class="rms-filter-group">

                        @if(!app()->bound('restaurant'))
                            <select id="restaurantFilter" class="form-select">
                                <option value="">All Restaurants</option>
                                @foreach($restaurants as $r)
                                    <option value="{{ $r->id }}">{{ $r->name }}</option>
                                @endforeach
                            </select>
                        @endif

                        <select id="branchFilter" class="form-select">
                            <option value="">All Branches</option>
                        </select>

                    </div>
                    <div class="rms-card-body">
                        <div class="row">
                            <div class="col-lg-9">
                                <div id="chart1" class="rms-chart-container"></div>

                                <div class="rms-revenue-stats">
                                    <div class="rms-revenue-item">
                                        <div class="rms-revenue-icon"><i class="fas fa-calendar-week"></i></div>
                                        <div class="rms-revenue-value" id="weeklyRevenue">
                                            ₹{{ number_format($revenue['weekly']['amount']) }}
                                        </div>
                                        <div class="rms-revenue-label">Weekly Earnings</div>
                                    </div>
                                    <div class="rms-revenue-item">
                                        <div class="rms-revenue-icon"><i class="fas fa-calendar-month"></i></div>
                                        <div class="rms-revenue-value" id="monthlyRevenue">
                                            ₹{{ number_format($revenue['monthly']['amount']) }}
                                        </div>
                                        <div class="rms-revenue-label">Monthly Earnings</div>
                                    </div>
                                    <div class="rms-revenue-item">
                                        <div class="rms-revenue-icon"><i class="fas fa-calendar-year"></i></div>
                                        <div class="rms-revenue-value" id="yearlyRevenue">
                                            ₹{{ number_format($revenue['yearly']['amount']) }}
                                        </div>
                                        <div class="rms-revenue-label">Yearly Earnings</div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-3">
                                <div class="rms-stats-panel">
                                    <div class="rms-stat-row">
                                        <span class="rms-stat-label"><i class="fas fa-shopping-cart"></i> Total Orders</span>
                                        <span class="rms-stat-value" id="totalOrders">
                                            {{ number_format($revenue['total']['orders']) }}
                                        </span>
                                    </div>
                                    <div class="rms-stat-row">
                                        <span class="rms-stat-label"><i class="fas fa-money-bill"></i> Total Revenue</span>
                                        <span class="rms-stat-value text-success" id="totalRevenue">
                                            ₹{{ number_format($revenue['total']['amount']) }}
                                        </span>
                                    </div>
                                    <div class="rms-stat-row">
                                        <span class="rms-stat-label"><i class="fas fa-store"></i> Restaurants</span>
                                        <span class="rms-stat-value">{{ $restaurants->count() }}</span>
                                    </div>
                                    <div class="rms-stat-row">
                                        <span class="rms-stat-label"><i class="fas fa-users"></i> Total Users</span>
                                        <span class="rms-stat-value">{{ $totalUsers ?? 'N/A' }}</span>
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
                <div class="col-lg-4 col-md-6">
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
                <div class="col-lg-4 col-md-6">
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
                <div class="col-lg-4 col-md-6">
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
                <div class="col-xl-3 col-md-6">
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
                <div class="col-xl-3 col-md-6">
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
                <div class="col-xl-3 col-md-6">
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
                <div class="col-xl-3 col-md-6">
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
                <div class="card shadow mt-4">
                    <div class="subscription-header">
                        <div class="subscription-title">
                            <div class="header-icon">
                                <i class="fas fa-calendar-check"></i>
                            </div>
                            <div>
                                <h3>Prepared and Pendings Orders</h3>
                                <p>
                                    Monitor all restaurant subscriptions and renewal status.
                                </p>
                            </div>
                        </div>
                        <div class="subscription-summary">
                            @if (!empty($restaurantSlug) && !empty($branchSlug))
                                            <a href="{{ route('branch.orders.index', [
                                        'restaurant' => $restaurantSlug,
                                        'branch' => $branchSlug,
                                    ]) }}" class="btn btn-primary">
                                                View All
                                            </a>
                                @elseif (!empty($restaurantSlug))
                                            <a href="{{ route('restaurant.orders.index', [
                                        'restaurant' => $restaurantSlug,
                                    ]) }}" class="btn btn-primary">
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

                                <tbody>
                                    @forelse($preparedOrders as $order)
                                        <tr>
                                            <td>{{ $order->token_no }}</td>
                                            <td>{{ $order->customer_name }}</td>
                                            <td>{{ $order->table_no ?? '-' }}</td>
                                            <td>₹{{ number_format($order->total, 2) }}</td>
                                            <td>
                                                {{ $order->status }}
                                            </td>
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
                                                    @else

                                                    @endif

                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center">No prepared orders found.</td>
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
    <script src="https://cdn.jsdelivr.net/npm/apexcharts@3.45.0/dist/apexcharts.min.js"></script>

    {{-- Your dashboard script --}}

    <script>

        document.addEventListener('DOMContentLoaded', function () {

            if (currentRestaurant) {
                loadBranches();
                refreshAllData();
            }

        });
        let revenueChart;
        let donutChart;

        document.addEventListener('DOMContentLoaded', function () {

            @php
                $revAmount = $revenueData['amount'] ?? [0, 0, 0, 0, 0];
                $revOrders = $revenueData['orders'] ?? [0, 0, 0, 0, 0];
                $invNames = $inventoryAlerts->pluck('name')->toArray();
                $invStock = $inventoryAlerts->pluck('remaining_stock')->toArray();
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
                    }
                },
                colors: ['#4e73df'],
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    curve: 'smooth'
                },
             xaxis: {
                    categories: ['Today', 'Yesterday', 'This Week', 'This Month', 'This Year']
                },
              tooltip: {
                  y: {
                      formatter: function (val) {
                            return "₹" + Number(val).toLocaleString();
                        }
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
                labels: ['Pending', 'Preparing', 'Completed','Delivered'],
                colors: ['#f6c23e', '#36b9cc', '#1cc88a','#FA8AFA'],
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
                        name: 'Revenue (₹)',
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
                            text: 'Revenue (₹)'
                        },
                        labels: {
                            formatter: function(val) {
                                return '₹' + Number(val).toLocaleString();
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
                                return '₹' + Number(val).toLocaleString();
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
           var  options2 = {
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
                 bar:    {
                        horizontal: true,
                        borderRadius: 4
                    }
                },
              xaxis: {
                    categories: @json($invNames ?: ['No Alerts'])
                }
        };

            new ApexCharts(document.querySelector("#chart2"), options2).render();

    });


    </script>

    <script>

        let currentRestaurant = '';
        let currentBranch = '';

        document.getElementById('restaurantFilter').addEventListener('change', function () {

                currentRestaurant = this.value;
            currentBranch = '';

                    loadBranches();
            refreshAllData();

        });

        document.getElementById('branchFilter').addEventListener('change', function () {

                currentBranch = this.value;
            refreshAllData();

        });

        function loadBranches() {

          const branchSelect = document.getElementById('branchFilter');

             branchSelect.innerHTML = '<option value="">All Branches</option>';

            if ( !currentRestaurant) {
                return;
             }

            fetch('/dashboard/branches/' + currentRestaurant)
                .then(response => response.json())
            .then(data => {

                 data.forEach(function (branch) {

                        branchSelect.innerHTML +=
                        `<option value="${branch.id}">${branch.name}</option>`;

                    });

                });

    }

    function refreshAllData() {

    fetch(`/dashboard/data?restaurant_id=${currentRestaurant}&branch_id=${currentBranch}`)
        .then(response => response.json())
        .then(data => {

            console.log("Dashboard Data:", data);

            // Revenue Chart
            revenueChart.updateSeries([{
                data: [
                    Number(data.revenue.today.amount),
                    Number(data.revenue.yesterday.amount),
                    Number(data.revenue.weekly.amount),
                    Number(data.revenue.monthly.amount),
                    Number(data.revenue.yearly.amount)
                ]
            }]);

            // Donut Chart
            donutChart.updateSeries([
                data.orderStatus.pending,
                data.orderStatus.preparing,
                data.orderStatus.completed,
                data.orderStatus.delivered ?? 0
            ]);

            // Revenue Cards
            document.getElementById('totalOrders').innerHTML =
                data.revenue.total.orders;

            document.getElementById('totalRevenue').innerHTML =
                '₹' + Number(data.revenue.total.amount).toLocaleString();

            document.getElementById('weeklyRevenue').innerHTML =
                '₹' + Number(data.revenue.weekly.amount).toLocaleString();

            document.getElementById('monthlyRevenue').innerHTML =
                '₹' + Number(data.revenue.monthly.amount).toLocaleString();

            document.getElementById('yearlyRevenue').innerHTML =
                '₹' + Number(data.revenue.yearly.amount).toLocaleString();

        })
        .catch(function(error) {
            console.error(error);
        });
}

    </script>
@endsection
