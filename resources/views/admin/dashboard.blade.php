@extends('layouts.app')

@section('content')
    <section class="section">
        <div class="row mb-3">

            {{-- SuperAdmin Cards --}}
            @if (isset($revenue) && auth()->user()->role == 'super_admin')
                <div class="col-md-3 mb-4">
                    <div class="card gradient-card restaurant-card shadow-sm border-0">
                        <div class="card-body text-center p-4">
                            <h6 class="text-white mb-2">Total Restaurant</h6>
                            <h3 class="text-white mb-0">{{ $totalRestaurants }}</h3>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 mb-4">
                    <div class="card gradient-card branches-card shadow-sm border-0">
                        <div class="card-body text-center p-4">
                            <h6 class="text-white mb-2">Total Branches</h6>
                            <h3 class="text-white mb-0">{{ $totalBranches }}</h3>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 mb-4">
                    <div class="card gradient-card expiry-card shadow-sm border-0">
                        <div class="card-body text-center p-4">
                            <h6 class="text-white mb-2">Near Expiring</h6>
                            <h3 class="text-white mb-0">{{ $nearExpirySubscriptions }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="card gradient-card expiry-card shadow-sm border-0">
                        <div class="card-body text-center p-4">
                            <h6 class="text-white mb-2">Expired Subscription</h6>
                            <h3 class="text-white mb-0">{{ $expiredSubscriptionCount }}</h3>
                        </div>
                    </div>
                </div>
            @endif






            {{-- Revenue Cards --}}
            @can('today-revenue')
                <div class="col-md-3 mb-4">
                    <div class="card gradient-card today-card shadow-sm border-0">
                        <div class="card-body text-center p-4">
                            <h6 class="text-white mb-2">Today Revenue</h6>
                            <h3 class="text-white mb-1">₹{{ number_format($revenue['today']['amount']) }}</h3>
                            <p class="text-white mb-0">{{ $revenue['today']['orders'] }} Orders</p>
                        </div>
                    </div>
                </div>
            @endcan

            @can('yesterday-revenue')
                <div class="col-md-3 mb-4">
                    <div class="card gradient-card yesterday-card shadow-sm border-0">
                        <div class="card-body text-center p-4">
                            <h6 class="text-white mb-2">Yesterday Revenue</h6>
                            @if (isset($revenue))
                                <h3 class="text-white mb-1">₹{{ number_format($revenue['yesterday']['amount']) }}</h3>
                                <p class="text-white mb-0">{{ $revenue['yesterday']['orders'] }} Orders</p>
                            @endif
                        </div>
                    </div>
                </div>
            @endcan

            @can('weekly-revenue')
                <div class="col-md-3 mb-4">
                    <div class="card gradient-card weekly-card shadow-sm border-0">
                        <div class="card-body text-center p-4">
                            <h6 class="text-white mb-2">Weekly Revenue</h6>
                            <h3 class="text-white mb-1">₹{{ number_format($revenue['weekly']['amount']) }}</h3>
                            <p class="text-white mb-0">{{ $revenue['weekly']['orders'] }} Orders</p>
                        </div>
                    </div>
                </div>
            @endcan

            @can('monthly-revenue')
                <div class="col-md-3 mb-4">
                    <div class="card gradient-card monthly-card shadow-sm border-0">
                        <div class="card-body text-center p-4">
                            <h6 class="text-white mb-2">Monthly Revenue</h6>
                            @if (isset($revenue))
                                <h3 class="text-white mb-1">₹{{ number_format($revenue['monthly']['amount']) }}</h3>
                                <p class="text-white mb-0">{{ $revenue['monthly']['orders'] }} Orders</p>
                            @endif
                        </div>
                    </div>
                </div>
            @endcan

            @can('yearly-revenue')
                <div class="col-md-3 mb-4">
                    <div class="card gradient-card yearly-card shadow-sm border-0">
                        <div class="card-body text-center p-4">
                            <h6 class="text-white mb-2">Yearly Revenue</h6>
                            @if (isset($revenue))
                                <h3 class="text-white mb-1">₹{{ number_format($revenue['yearly']['amount']) }}</h3>
                                <p class="text-white mb-0">{{ $revenue['yearly']['orders'] }} Orders</p>
                            @endif
                        </div>
                    </div>
                </div>
            @endcan
        </div>

        @can('superadmin-view')
            <div class="row mb-4">
                <div class="col-12">

                    <div class="card shadow-sm border-0">

                        <div class="card-header bg-white text-dark">
                            <h5 class="mb-0">
                                <i data-feather="x-circle"></i>
                                Expired Subscriptions
                            </h5>
                        </div>

                        <div class="table-responsive">

                            <table class="table table-hover align-middle mb-0">

                                <thead class="table-light">
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

                                            <td>
                                                {{ $subscription->branch?->restaurant?->name ?? 'N/A' }}
                                            </td>

                                            <td>
                                                {{ $subscription->branch?->name ?? 'N/A' }}
                                            </td>

                                            <td>
                                                {{ $subscription->plan?->name ?? $subscription->billing_cycle }}
                                            </td>

                                            <td>
                                                {{ $endDate->format('d M Y') }}
                                            </td>

                                            <td>
                                                {{ (int) $expiredDays }} day{{ (int) $expiredDays > 1 ? 's' : '' }} ago
                                            </td>

                                            <td>
                                                <span class="badge bg-danger">
                                                    Expired
                                                </span>
                                            </td>

                                        </tr>

                                    @empty

                                        <tr>
                                            <td colspan="6" class="text-center py-4">
                                                No expired subscriptions found.
                                            </td>
                                        </tr>

                                    @endforelse

                                </tbody>

                            </table>

                        </div>

                    </div>

                </div>
            </div>
            <div class="row mb-4">
                <div class="col-12">

                    <div class="card shadow-sm border-0">

                        <div class="card-header bg-white text-dark">
                            <h5 class="mb-0">
                                <i data-feather="calendar"></i>
                                Subscription Overview
                            </h5>
                        </div>

                        <div class="table-responsive">

                            <table class="table table-hover align-middle mb-0">

                                <thead class="table-light">

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

                                            <td>
                                                {{ $subscription->branch?->restaurant?->name ?? 'N/A' }}
                                            </td>

                                            <td>
                                                {{ $subscription->branch?->name ?? 'N/A' }}
                                            </td>

                                            <td>
                                                {{ $subscription->billing_cycle ?? 'N/A' }}
                                            </td>

                                            <td>
                                                {{ $endDate->format('d M Y') }}
                                            </td>

                                            <td>
                                                {{ (int) $expiredDays }}
                                            </td>


                                            <td>

                                                @if($days <= 7)

                                                    <span class="badge bg-danger">
                                                        Critical
                                                    </span>

                                                @elseif($days <= 30)

                                                    <span class="badge bg-warning text-dark">
                                                        Expiring Soon
                                                    </span>

                                                @else

                                                    <span class="badge bg-success">
                                                        Active
                                                    </span>

                                                @endif

                                            </td>

                                        </tr>

                                    @empty

                                        <tr>

                                            <td colspan="6" class="text-center py-4">
                                                No subscriptions nearing expiry.
                                            </td>

                                        </tr>

                                    @endforelse

                                </tbody>

                            </table>

                        </div>

                    </div>

                </div>
            </div>


            <div class="row">
                <div class="col-12">

                    <div class="card shadow-sm border-0">

                        <div class="card-header bg-white text-dark">

                            <h5 class="mb-0">
                                <i data-feather="alert-triangle"></i>
                                Inventory Alerts
                            </h5>

                        </div>

                        <div class="table-responsive">

                            <table class="table table-hover align-middle mb-0">

                                <thead class="table-light">

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

                                            <td>
                                                {{ $item->restaurant?->name ?? 'N/A' }}
                                            </td>

                                            <td>
                                                {{ $item->branch?->name ?? 'N/A' }}
                                            </td>

                                            <td>
                                                <strong>{{ $item->name }}</strong>
                                            </td>

                                            <td>
                                                {{ $item->remaining_stock }} {{ $item->unit }}
                                            </td>

                                            <td>
                                                {{ $item->minimum_stock }} {{ $item->unit }}
                                            </td>

                                            <td>

                                                @if($item->remaining_stock <= 0)

                                                    <span class="badge bg-danger">
                                                        Out of Stock
                                                    </span>

                                                @elseif($item->remaining_stock <= $item->minimum_stock)

                                                    <span class="badge bg-warning text-dark">
                                                        Low Stock
                                                    </span>

                                                @else

                                                    <span class="badge bg-success">
                                                        In Stock
                                                    </span>

                                                @endif

                                            </td>

                                        </tr>

                                    @empty

                                        <tr>

                                            <td colspan="6" class="text-center py-4">
                                                No inventory alerts.
                                            </td>

                                        </tr>

                                    @endforelse

                                </tbody>

                            </table>

                        </div>

                    </div>

                </div>
            </div>

            <div class="row mb-4">
                <div class="col-12">

                    <div class="card shadow-sm border-0">

                        <div class="card-header bg-white d-flex justify-content-between align-items-center text-dark">
                            <h5 class="mb-0">
                                <i data-feather="trending-up" class="me-2"></i>
                                Top Restaurants by Revenue
                            </h5>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">

                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Restaurant</th>
                                        <th>Branches</th>
                                        <th>Orders</th>
                                        <th class="text-end">Revenue</th>
                                    </tr>
                                </thead>

                                <tbody>

                                    @forelse($topRestaurants as $restaurant)

                                        <tr>

                                            <td>{{ $loop->iteration }}</td>

                                            <td>
                                                <strong>{{ $restaurant->name ?? 'N/A' }}</strong>
                                            </td>

                                            <td>{{ $restaurant->total_branches }}</td>

                                            <td>{{ number_format($restaurant->total_orders) }}</td>

                                            <td class="text-end fw-bold text-success">
                                                ₹{{ number_format($restaurant->total_revenue, 2) }}
                                            </td>

                                        </tr>

                                    @empty

                                        <tr>
                                            <td colspan="5" class="text-center py-4">
                                                No data available.
                                            </td>
                                        </tr>

                                    @endforelse

                                </tbody>

                            </table>
                        </div>

                    </div>

                </div>
            </div>
            <div class="row">
            <div class="col-12 col-sm-12 col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Revenue Chart</h4>
                        <select id="restaurantFilter" class="form-control w-auto">
                            <option value="">All Restaurants</option>
                            @foreach($restaurants as $r)
                                <option value="{{ $r->id }}">{{ $r->name }}</option>
                            @endforeach
                        </select>

                        <!-- Branch Dropdown (will be populated dynamically) -->
                        <select id="branchFilter" class="form-control w-auto">
                            <option value="">All Branches</option>
                        </select>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-9">
                                <div id="chart1" style="min-height: 380px;"></div>

                                <div class="row mb-0 mt-4">
                                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                                        <div class="list-inline text-center">
                                            <div class="list-inline-item p-r-30">
                                                <i data-feather="arrow-up-circle" class="col-green"></i>
                                                <h5 class="m-b-0" id="weeklyRevenue">
                                                    ₹{{ number_format($revenue['weekly']['amount']) }}
                                                </h5>
                                                <p class="text-muted font-14 m-b-0">Weekly Earnings</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                                        <div class="list-inline text-center">
                                            <div class="list-inline-item p-r-30">
                                                <i data-feather="arrow-up-circle" class="col-green"></i>
                                                <h5 class="m-b-0" id="monthlyRevenue">
                                                    ₹{{ number_format($revenue['monthly']['amount']) }}
                                                </h5>
                                                <p class="text-muted font-14 m-b-0">Monthly Earnings</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                                        <div class="list-inline text-center">
                                            <div class="list-inline-item p-r-30">
                                                <i data-feather="arrow-up-circle" class="col-green"></i>
                                               <h5 class="mb-0 m-b-0" id="yearlyRevenue">
                                                    ₹{{ number_format($revenue['yearly']['amount']) }}
                                                </h5>
                                                <p class="text-muted font-14 m-b-0">Yearly Earnings</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-3">
                                <div class="row mt-5">
                                    <div class="col-7 col-xl-7 mb-3">Total Orders</div>
                                    <div class="col-5 col-xl-5 mb-3">
                                        <span id="totalOrders" class="text-big">
                                            {{ number_format($revenue['total']['orders']) }}
                                        </span><span
                                            class="text-big">{{ number_format($revenue['total']['orders']) }}</span>
                                    </div>
                                    <div class="col-7 col-xl-7 mb-3">Total Revenue</div>
                                    <div class="col-5 col-xl-5 mb-3">
                                        <span id="totalRevenue" class="text-big">
                                            ₹{{ number_format($revenue['total']['amount']) }}
                                        </span>
                                    </div>
                                    <!-- Add more stats as needed -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Small Charts Row -->
        <div class="row">
            <div class="col-12 col-sm-12 col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h4>Order Status</h4>
                    </div>
                    <div class="card-body">
                        <div id="chart4" class="chartsh" style="min-height: 300px;"></div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-12 col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h4>Revenue Trend</h4>
                    </div>
                    <div class="card-body">
                        <div id="chart3" class="chartsh" style="min-height: 300px;"></div>
                    </div>
                </div>
            </div>

        </div>
            {{-- <div class="row mb-4">
                <div class="col-12">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center">
                            <h5 class="mb-0"><i data-feather="bar-chart-2"></i> Revenue Overview</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="revenueBarChart" height="120"></canvas>
                        </div>
                    </div>
                </div>
            </div> --}}
        @endcan
        @can('view-payment')
            <div class="row">
                {{-- Pending Verification --}}
                <div class="col-md-4 mb-4">
                    <div class="card bg-warning text-white shadow border-0 h-100">
                        <div class="card-body text-center p-4">
                            <div class="mb-3">
                                <i class="fas fa-clock fa-2x"></i>
                            </div>
                            <h6 class="mb-2">Pending Verification</h6>
                            <h2 class="fw-bold">{{ $pendingVerification }}</h2>
                            <small>Payments Awaiting Approval</small>
                        </div>
                    </div>
                </div>

                {{-- Verified Today --}}
                <div class="col-md-4 mb-4">
                    <div class="card bg-success text-white shadow border-0 h-100">
                        <div class="card-body text-center p-4">
                            <div class="mb-3">
                                <i class="fas fa-check-circle fa-2x"></i>
                            </div>
                            <h6 class="mb-2">Verified Today</h6>
                            <h2 class="fw-bold">{{ $verifiedToday }}</h2>
                            <small>Payments Approved Today</small>
                        </div>
                    </div>
                </div>

                {{-- Today's Collection --}}
                <div class="col-md-4 mb-4">
                    <div class="card bg-primary text-white shadow border-0 h-100">
                        <div class="card-body text-center p-4">
                            <div class="mb-3">
                                <i class="fas fa-wallet fa-2x"></i>
                            </div>
                            <h6 class="mb-2">Today's Collection</h6>
                            <h2 class="fw-bold">₹{{ number_format($todayCollection) }}</h2>
                            <small>Verified Payment Amount</small>
                        </div>
                    </div>
                </div>

            </div>
        @endcan

        @can('order-status')
            <div class="row g-4 mt-3 mb-4">

                {{-- Pending Orders --}}
                <div class="col-12 col-md-4">
                    <div class="status-card status-card--pending">
                        <div class="status-card__icon-wrapper">
                            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="currentColor"
                                viewBox="0 0 16 16">
                                <path d="M8 3.5a.5.5 0 0 0-1 0V9a.5.5 0 0 0 .252.434l3.5 2a.5.5 0 0 0 .496-.868L8 8.71z" />
                                <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16" />
                            </svg>
                        </div>
                        <div class="status-card__content">
                            <span class="status-card__label">Pending Orders</span>
                            <h2 class="status-card__value">{{ $orderStatus['pending'] }}</h2>
                        </div>
                    </div>
                </div>

                {{-- Preparing Orders --}}
                <div class="col-12 col-md-4">
                    <div class="status-card status-card--preparing">
                        <div class="status-card__icon-wrapper">
                            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="currentColor"
                                viewBox="0 0 16 16">
                                <path
                                    d="M8 11.5a.5.5 0 0 1 .5-.5h4a.5.5 0 0 1 0 1h-4a.5.5 0 0 1-.5-.5m0-4a.5.5 0 0 1 .5-.5h4a.5.5 0 0 1 0 1h-4a.5.5 0 0 1-.5-.5m0-4a.5.5 0 0 1 .5-.5h4a.5.5 0 0 1 0 1h-4a.5.5 0 0 1-.5-.5" />
                                <path
                                    d="M1.5 0A1.5 1.5 0 0 0 0 1.5v13A1.5 1.5 0 0 0 1.5 16h13a1.5 1.5 0 0 0 1.5-1.5v-13A1.5 1.5 0 0 0 14.5 0zM1 1.5a.5.5 0 0 1 .5-.5h13a.5.5 0 0 1 .5.5v13a.5.5 0 0 1-.5.5h-13a.5.5 0 0 1-.5-.5z" />
                            </svg>
                        </div>
                        <div class="status-card__content">
                            <span class="status-card__label">Preparing Orders</span>
                            <h2 class="status-card__value">{{ $orderStatus['preparing'] }}</h2>
                        </div>
                    </div>
                </div>

                {{-- Completed Orders --}}
                <div class="col-12 col-md-4">
                    <div class="status-card status-card--completed">
                        <div class="status-card__icon-wrapper">
                            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="currentColor"
                                viewBox="0 0 16 16">
                                <path
                                    d="M12.736 3.97a.733.733 0 0 1 1.047 0c.286.289.29.756.01 1.05L7.88 12.01a.733.733 0 0 1-1.065.02L3.217 8.384a.757.757 0 0 1 0-1.06.733.733 0 0 1 1.047 0l3.052 3.093 5.4-6.425z" />
                            </svg>
                        </div>
                        <div class="status-card__content">
                            <span class="status-card__label">Completed Orders</span>
                            <h2 class="status-card__value">{{ $orderStatus['completed'] }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="status-card status-card--completed">
                        <div class="status-card__icon-wrapper">
                            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="currentColor"
                                viewBox="0 0 16 16">
                                <path
                                    d="M12.736 3.97a.733.733 0 0 1 1.047 0c.286.289.29.756.01 1.05L7.88 12.01a.733.733 0 0 1-1.065.02L3.217 8.384a.757.757 0 0 1 0-1.06.733.733 0 0 1 1.047 0l3.052 3.093 5.4-6.425z" />
                            </svg>
                        </div>
                        <div class="status-card__content">
                            <span class="status-card__label">Delivered Orders</span>
                            <h2 class="status-card__value">{{ $orderStatus['delivered'] }}</h2>
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
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Prepared and Pendings Orders</h5>
                    <div class="premium-head-actions">


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
                                                        class="btn btn-sm btn-info">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                @elseif(!empty($restaurantSlug))
                                                    <a href="{{ route('restaurant.orders.show', ['restaurant' => $restaurantSlug, 'order' => $order->id]) }}"
                                                        class="btn btn-sm btn-info">
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
                        <div class="card-header">
                            <h5 class="mb-0">
                                Inventory Stock Summary
                            </h5>
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
           var  options3 = {
                series: [{
                    name: 'Orders',
                    data: @json($revOrders)
                }],
            chart: {
                    height: 300,
                    type: 'bar'
                },
                colors: ['#4e73df'],
             plotOptions: {
                 bar:    {
                        borderRadius: 4
                    }
                },
              xaxis: {
                    categories: ['Today', 'Yesterday', 'Week', 'Month', 'Year']
                }
        };

         new ApexCharts(document.querySelector("#chart3"), options3).render();


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

                  console.log(data);

                  revenueChart.updateSeries([{

                        data  : [
                            data.revenue.today.amount,
                            data.revenue.yesterday.amount,
                            data.revenue.weekly.amount,
                            data.revenue.monthly.amount,
                            data.revenue.yearly.amount
                    ]

                 }]);

                 donutChart.updateSeries([

                         data.orderStatus.pending,
                         data.orderStatus.preparing,
                         data.orderStatus.completed
                         data.orderStatus.delivered
                    ]);

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

               .catch(function (error) {

                   console.log(error);

               });

    }

    </script>
@endsection
