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

            </div>
        @endcan

        @can('prepared-index-dashboard')
            @php
                $restaurantSlug = request()->route('restaurant');
                $branchSlug = request()->route('branch');
            @endphp
            <div class="card shadow mt-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Prepared Orders</h5>

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
                                            <span class="badge bg-info">Prepared</span>
                                        </td>
                                        <td>
                                            <div class="d-flex gap-2">

                                                {{-- View Button - Always visible --}}
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
                                                    {{-- <a href="{{ route('orders.show', ['order' => $order->id]) }}"
                                                        class="btn btn-sm btn-info">
                                                        <i class="fas fa-eye"></i>
                                                    </a> --}}
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




        <div class="row">
            <div class="col-12 col-sm-12 col-lg-12">
                <div class="card ">
                    <div class="card-header">
                        <h4>Revenue chart</h4>
                        <div class="card-header-action">
                            <div class="dropdown">
                                <a href="#" data-toggle="dropdown" class="btn btn-warning dropdown-toggle">Options</a>
                                <div class="dropdown-menu">
                                    <a href="#" class="dropdown-item has-icon"><i class="fas fa-eye"></i> View</a>
                                    <a href="#" class="dropdown-item has-icon"><i class="far fa-edit"></i> Edit</a>
                                    <div class="dropdown-divider"></div>
                                    <a href="#" class="dropdown-item has-icon text-danger"><i class="far fa-trash-alt"></i>
                                        Delete</a>
                                </div>
                            </div>
                            <a href="#" class="btn btn-primary">View All</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-9">
                                <div id="chart1"></div>
                                <div class="row mb-0">
                                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                                        <div class="list-inline text-center">
                                            <div class="list-inline-item p-r-30"><i data-feather="arrow-up-circle"
                                                    class="col-green"></i>
                                                <h5 class="m-b-0">$675</h5>
                                                <p class="text-muted font-14 m-b-0">Weekly Earnings</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                                        <div class="list-inline text-center">
                                            <div class="list-inline-item p-r-30"><i data-feather="arrow-down-circle"
                                                    class="col-orange"></i>
                                                <h5 class="m-b-0">$1,587</h5>
                                                <p class="text-muted font-14 m-b-0">Monthly Earnings</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                                        <div class="list-inline text-center">
                                            <div class="list-inline-item p-r-30"><i data-feather="arrow-up-circle"
                                                    class="col-green"></i>
                                                <h5 class="mb-0 m-b-0">$45,965</h5>
                                                <p class="text-muted font-14 m-b-0">Yearly Earnings</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="row mt-5">
                                    <div class="col-7 col-xl-7 mb-3">Total customers</div>
                                    <div class="col-5 col-xl-5 mb-3">
                                        <span class="text-big">8,257</span>
                                        <sup class="col-green">+09%</sup>
                                    </div>
                                    <div class="col-7 col-xl-7 mb-3">Total Income</div>
                                    <div class="col-5 col-xl-5 mb-3">
                                        <span class="text-big">$9,857</span>
                                        <sup class="text-danger">-18%</sup>
                                    </div>
                                    <div class="col-7 col-xl-7 mb-3">Project completed</div>
                                    <div class="col-5 col-xl-5 mb-3">
                                        <span class="text-big">28</span>
                                        <sup class="col-green">+16%</sup>
                                    </div>
                                    <div class="col-7 col-xl-7 mb-3">Total expense</div>
                                    <div class="col-5 col-xl-5 mb-3">
                                        <span class="text-big">$6,287</span>
                                        <sup class="col-green">+09%</sup>
                                    </div>
                                    <div class="col-7 col-xl-7 mb-3">New Customers</div>
                                    <div class="col-5 col-xl-5 mb-3">
                                        <span class="text-big">684</span>
                                        <sup class="col-green">+22%</sup>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-sm-12 col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h4>Chart</h4>
                    </div>
                    <div class="card-body">
                        <div id="chart4" class="chartsh"></div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-12 col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h4>Chart</h4>
                    </div>
                    <div class="card-body">
                        <div class="summary">
                            <div class="summary-chart active" data-tab-group="summary-tab" id="summary-chart">
                                <div id="chart3" class="chartsh"></div>
                            </div>
                            <div data-tab-group="summary-tab" id="summary-text">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-12 col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h4>Chart</h4>
                    </div>
                    <div class="card-body">
                        <div id="chart2" class="chartsh"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Assign Task Table</h4>
                        <div class="card-header-form">
                            <form>
                                <div class="input-group">
                                    <input type="text" class="form-control" placeholder="Search">
                                    <div class="input-group-btn">
                                        <button class="btn btn-primary"><i class="fas fa-search"></i></button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <tr>
                                    <th class="text-center">
                                        <div class="custom-checkbox custom-checkbox-table custom-control">
                                            <input type="checkbox" data-checkboxes="mygroup" data-checkbox-role="dad"
                                                class="custom-control-input" id="checkbox-all">
                                            <label for="checkbox-all" class="custom-control-label">&nbsp;</label>
                                        </div>
                                    </th>
                                    <th>Task Name</th>
                                    <th>Members</th>
                                    <th>Task Status</th>
                                    <th>Assigh Date</th>
                                    <th>Due Date</th>
                                    <th>Priority</th>
                                    <th>Action</th>
                                </tr>
                                <tr>
                                    <td class="p-0 text-center">
                                        <div class="custom-checkbox custom-control">
                                            <input type="checkbox" data-checkboxes="mygroup" class="custom-control-input"
                                                id="checkbox-1">
                                            <label for="checkbox-1" class="custom-control-label">&nbsp;</label>
                                        </div>
                                    </td>
                                    <td>Create a mobile app</td>
                                    <td class="text-truncate">
                                        <ul class="list-unstyled order-list m-b-0 m-b-0">
                                            <li class="team-member team-member-sm"><img class="rounded-circle"
                                                    src="assets/img/users/user-8.png" alt="user" data-toggle="tooltip"
                                                    title="" data-original-title="Wildan Ahdian"></li>
                                            <li class="team-member team-member-sm"><img class="rounded-circle"
                                                    src="assets/img/users/user-9.png" alt="user" data-toggle="tooltip"
                                                    title="" data-original-title="John Deo">
                                            </li>
                                            <li class="team-member team-member-sm"><img class="rounded-circle"
                                                    src="assets/img/users/user-10.png" alt="user" data-toggle="tooltip"
                                                    title="" data-original-title="Sarah Smith"></li>
                                            <li class="avatar avatar-sm"><span class="badge badge-primary">+4</span></li>
                                        </ul>
                                    </td>
                                    <td class="align-middle">
                                        <div class="progress-text">50%</div>
                                        <div class="progress" data-height="6">
                                            <div class="progress-bar bg-success" data-width="50%"></div>
                                        </div>
                                    </td>
                                    <td>2018-01-20</td>
                                    <td>2019-05-28</td>
                                    <td>
                                        <div class="badge badge-success">Low</div>
                                    </td>
                                    <td><a href="#" class="btn btn-outline-primary">Detail</a></td>
                                </tr>
                                <tr>
                                    <td class="p-0 text-center">
                                        <div class="custom-checkbox custom-control">
                                            <input type="checkbox" data-checkboxes="mygroup" class="custom-control-input"
                                                id="checkbox-2">
                                            <label for="checkbox-2" class="custom-control-label">&nbsp;</label>
                                        </div>
                                    </td>
                                    <td>Redesign homepage</td>
                                    <td class="text-truncate">
                                        <ul class="list-unstyled order-list m-b-0 m-b-0">
                                            <li class="team-member team-member-sm"><img class="rounded-circle"
                                                    src="assets/img/users/user-1.png" alt="user" data-toggle="tooltip"
                                                    title="" data-original-title="Wildan Ahdian"></li>
                                            <li class="team-member team-member-sm"><img class="rounded-circle"
                                                    src="assets/img/users/user-2.png" alt="user" data-toggle="tooltip"
                                                    title="" data-original-title="John Deo">
                                            </li>
                                            <li class="avatar avatar-sm"><span class="badge badge-primary">+2</span></li>
                                        </ul>
                                    </td>
                                    <td class="align-middle">
                                        <div class="progress-text">40%</div>
                                        <div class="progress" data-height="6">
                                            <div class="progress-bar bg-danger" data-width="40%"></div>
                                        </div>
                                    </td>
                                    <td>2017-07-14</td>
                                    <td>2018-07-21</td>
                                    <td>
                                        <div class="badge badge-danger">High</div>
                                    </td>
                                    <td><a href="#" class="btn btn-outline-primary">Detail</a></td>
                                </tr>
                                <tr>
                                    <td class="p-0 text-center">
                                        <div class="custom-checkbox custom-control">
                                            <input type="checkbox" data-checkboxes="mygroup" class="custom-control-input"
                                                id="checkbox-3">
                                            <label for="checkbox-3" class="custom-control-label">&nbsp;</label>
                                        </div>
                                    </td>
                                    <td>Backup database</td>
                                    <td class="text-truncate">
                                        <ul class="list-unstyled order-list m-b-0 m-b-0">
                                            <li class="team-member team-member-sm"><img class="rounded-circle"
                                                    src="assets/img/users/user-3.png" alt="user" data-toggle="tooltip"
                                                    title="" data-original-title="Wildan Ahdian"></li>
                                            <li class="team-member team-member-sm"><img class="rounded-circle"
                                                    src="assets/img/users/user-4.png" alt="user" data-toggle="tooltip"
                                                    title="" data-original-title="John Deo">
                                            </li>
                                            <li class="team-member team-member-sm"><img class="rounded-circle"
                                                    src="assets/img/users/user-5.png" alt="user" data-toggle="tooltip"
                                                    title="" data-original-title="Sarah Smith"></li>
                                            <li class="avatar avatar-sm"><span class="badge badge-primary">+3</span></li>
                                        </ul>
                                    </td>
                                    <td class="align-middle">
                                        <div class="progress-text">55%</div>
                                        <div class="progress" data-height="6">
                                            <div class="progress-bar bg-purple" data-width="55%"></div>
                                        </div>
                                    </td>
                                    <td>2019-07-25</td>
                                    <td>2019-08-17</td>
                                    <td>
                                        <div class="badge badge-info">Average</div>
                                    </td>
                                    <td><a href="#" class="btn btn-outline-primary">Detail</a></td>
                                </tr>
                                <tr>
                                    <td class="p-0 text-center">
                                        <div class="custom-checkbox custom-control">
                                            <input type="checkbox" data-checkboxes="mygroup" class="custom-control-input"
                                                id="checkbox-4">
                                            <label for="checkbox-4" class="custom-control-label">&nbsp;</label>
                                        </div>
                                    </td>
                                    <td>Android App</td>
                                    <td class="text-truncate">
                                        <ul class="list-unstyled order-list m-b-0 m-b-0">
                                            <li class="team-member team-member-sm"><img class="rounded-circle"
                                                    src="assets/img/users/user-7.png" alt="user" data-toggle="tooltip"
                                                    title="" data-original-title="John Deo">
                                            </li>
                                            <li class="team-member team-member-sm"><img class="rounded-circle"
                                                    src="assets/img/users/user-8.png" alt="user" data-toggle="tooltip"
                                                    title="" data-original-title="Sarah Smith"></li>
                                            <li class="avatar avatar-sm"><span class="badge badge-primary">+4</span></li>
                                        </ul>
                                    </td>
                                    <td class="align-middle">
                                        <div class="progress-text">70%</div>
                                        <div class="progress" data-height="6">
                                            <div class="progress-bar" data-width="70%"></div>
                                        </div>
                                    </td>
                                    <td>2018-04-15</td>
                                    <td>2019-07-19</td>
                                    <td>
                                        <div class="badge badge-success">Low</div>
                                    </td>
                                    <td><a href="#" class="btn btn-outline-primary">Detail</a></td>
                                </tr>
                                <tr>
                                    <td class="p-0 text-center">
                                        <div class="custom-checkbox custom-control">
                                            <input type="checkbox" data-checkboxes="mygroup" class="custom-control-input"
                                                id="checkbox-5">
                                            <label for="checkbox-5" class="custom-control-label">&nbsp;</label>
                                        </div>
                                    </td>
                                    <td>Logo Design</td>
                                    <td class="text-truncate">
                                        <ul class="list-unstyled order-list m-b-0 m-b-0">
                                            <li class="team-member team-member-sm"><img class="rounded-circle"
                                                    src="assets/img/users/user-9.png" alt="user" data-toggle="tooltip"
                                                    title="" data-original-title="Wildan Ahdian"></li>
                                            <li class="team-member team-member-sm"><img class="rounded-circle"
                                                    src="assets/img/users/user-10.png" alt="user" data-toggle="tooltip"
                                                    title="" data-original-title="John Deo">
                                            </li>
                                            <li class="team-member team-member-sm"><img class="rounded-circle"
                                                    src="assets/img/users/user-2.png" alt="user" data-toggle="tooltip"
                                                    title="" data-original-title="Sarah Smith"></li>
                                            <li class="avatar avatar-sm"><span class="badge badge-primary">+2</span></li>
                                        </ul>
                                    </td>
                                    <td class="align-middle">
                                        <div class="progress-text">45%</div>
                                        <div class="progress" data-height="6">
                                            <div class="progress-bar bg-cyan" data-width="45%"></div>
                                        </div>
                                    </td>
                                    <td>2017-02-24</td>
                                    <td>2018-09-06</td>
                                    <td>
                                        <div class="badge badge-danger">High</div>
                                    </td>
                                    <td><a href="#" class="btn btn-outline-primary">Detail</a></td>
                                </tr>
                                <tr>
                                    <td class="p-0 text-center">
                                        <div class="custom-checkbox custom-control">
                                            <input type="checkbox" data-checkboxes="mygroup" class="custom-control-input"
                                                id="checkbox-6">
                                            <label for="checkbox-6" class="custom-control-label">&nbsp;</label>
                                        </div>
                                    </td>
                                    <td>Ecommerce website</td>
                                    <td class="text-truncate">
                                        <ul class="list-unstyled order-list m-b-0 m-b-0">
                                            <li class="team-member team-member-sm"><img class="rounded-circle"
                                                    src="assets/img/users/user-8.png" alt="user" data-toggle="tooltip"
                                                    title="" data-original-title="Wildan Ahdian"></li>
                                            <li class="team-member team-member-sm"><img class="rounded-circle"
                                                    src="assets/img/users/user-9.png" alt="user" data-toggle="tooltip"
                                                    title="" data-original-title="John Deo">
                                            </li>
                                            <li class="team-member team-member-sm"><img class="rounded-circle"
                                                    src="assets/img/users/user-10.png" alt="user" data-toggle="tooltip"
                                                    title="" data-original-title="Sarah Smith"></li>
                                            <li class="avatar avatar-sm"><span class="badge badge-primary">+4</span></li>
                                        </ul>
                                    </td>
                                    <td class="align-middle">
                                        <div class="progress-text">30%</div>
                                        <div class="progress" data-height="6">
                                            <div class="progress-bar bg-orange" data-width="30%"></div>
                                        </div>
                                    </td>
                                    <td>2018-01-20</td>
                                    <td>2019-05-28</td>
                                    <td>
                                        <div class="badge badge-info">Average</div>
                                    </td>
                                    <td><a href="#" class="btn btn-outline-primary">Detail</a></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 col-lg-12 col-xl-6">
                <!-- Support tickets -->
                <div class="card">
                    <div class="card-header">
                        <h4>Support Ticket</h4>
                        <form class="card-header-form">
                            <input type="text" name="search" class="form-control" placeholder="Search">
                        </form>
                    </div>
                    <div class="card-body">
                        <div class="support-ticket media pb-1 mb-3">
                            <img src="assets/img/users/user-1.png" class="user-img mr-2" alt="">
                            <div class="media-body ml-3">
                                <div class="badge badge-pill badge-success mb-1 float-right">Feature</div>
                                <span class="font-weight-bold">#89754</span>
                                <a href="javascript:void(0)">Please add advance table</a>
                                <p class="my-1">Hi, can you please add new table for advan...</p>
                                <small class="text-muted">Created by <span class="font-weight-bold font-13">John
                                        Deo</span>
                                    &nbsp;&nbsp; - 1 day ago</small>
                            </div>
                        </div>
                        <div class="support-ticket media pb-1 mb-3">
                            <img src="assets/img/users/user-2.png" class="user-img mr-2" alt="">
                            <div class="media-body ml-3">
                                <div class="badge badge-pill badge-warning mb-1 float-right">Bug</div>
                                <span class="font-weight-bold">#57854</span>
                                <a href="javascript:void(0)">Select item not working</a>
                                <p class="my-1">please check select item in advance form not work...</p>
                                <small class="text-muted">Created by <span class="font-weight-bold font-13">Sarah
                                        Smith</span>
                                    &nbsp;&nbsp; - 2 day ago</small>
                            </div>
                        </div>
                        <div class="support-ticket media pb-1 mb-3">
                            <img src="assets/img/users/user-3.png" class="user-img mr-2" alt="">
                            <div class="media-body ml-3">
                                <div class="badge badge-pill badge-primary mb-1 float-right">Query</div>
                                <span class="font-weight-bold">#85784</span>
                                <a href="javascript:void(0)">Are you provide template in Angular?</a>
                                <p class="my-1">can you provide template in latest angular 8.</p>
                                <small class="text-muted">Created by <span class="font-weight-bold font-13">Ashton
                                        Cox</span>
                                    &nbsp;&nbsp; -2 day ago</small>
                            </div>
                        </div>
                        <div class="support-ticket media pb-1 mb-3">
                            <img src="assets/img/users/user-6.png" class="user-img mr-2" alt="">
                            <div class="media-body ml-3">
                                <div class="badge badge-pill badge-info mb-1 float-right">Enhancement</div>
                                <span class="font-weight-bold">#25874</span>
                                <a href="javascript:void(0)">About template page load speed</a>
                                <p class="my-1">Hi, John, can you work on increase page speed of template...</p>
                                <small class="text-muted">Created by <span class="font-weight-bold font-13">Hasan
                                        Basri</span>
                                    &nbsp;&nbsp; -3 day ago</small>
                            </div>
                        </div>
                    </div>
                    <a href="javascript:void(0)" class="card-footer card-link text-center small ">View
                        All</a>
                </div>
                <!-- Support tickets -->
            </div>
            <div class="col-md-6 col-lg-12 col-xl-6">
                <div class="card">
                    <div class="card-header">
                        <h4>Projects Payments</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Client Name</th>
                                        <th>Date</th>
                                        <th>Payment Method</th>
                                        <th>Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>1</td>
                                        <td>John Doe </td>
                                        <td>11-08-2018</td>
                                        <td>NEFT</td>
                                        <td>$258</td>
                                    </tr>
                                    <tr>
                                        <td>2</td>
                                        <td>Cara Stevens
                                        </td>
                                        <td>15-07-2018</td>
                                        <td>PayPal</td>
                                        <td>$125</td>
                                    </tr>
                                    <tr>
                                        <td>3</td>
                                        <td>
                                            Airi Satou
                                        </td>
                                        <td>25-08-2018</td>
                                        <td>RTGS</td>
                                        <td>$287</td>
                                    </tr>
                                    <tr>
                                        <td>4</td>
                                        <td>
                                            Angelica Ramos
                                        </td>
                                        <td>01-05-2018</td>
                                        <td>CASH</td>
                                        <td>$170</td>
                                    </tr>
                                    <tr>
                                        <td>5</td>
                                        <td>
                                            Ashton Cox
                                        </td>
                                        <td>18-04-2018</td>
                                        <td>NEFT</td>
                                        <td>$970</td>
                                    </tr>
                                    <tr>
                                        <td>6</td>
                                        <td>
                                            John Deo
                                        </td>
                                        <td>22-11-2018</td>
                                        <td>PayPal</td>
                                        <td>$854</td>
                                    </tr>
                                    <tr>
                                        <td>7</td>
                                        <td>
                                            Hasan Basri
                                        </td>
                                        <td>07-09-2018</td>
                                        <td>Cash</td>
                                        <td>$128</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
