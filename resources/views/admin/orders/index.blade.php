@can('view-order')
    @extends('layouts.app')
    @section('content')
        <section class="section premium-dashboard">
            <div class="premium-floating-header">
                <div class="header-content">
                    <div class="header-left">
                        <div class="header-icon">
                            <i class="fas fa-clipboard-list"></i>
                        </div>
                        <div>
                            <span class="header-badge">Order Management</span>
                            <h2>Order</h2>
                            <p>Manage restaurant orders and track status.</p>
                        </div>
                    </div>
                    <div class="header-right">
                        @php
                            $restaurantSlug = request()->route('restaurant');
                            $branchSlug = request()->route('branch');
                        @endphp


                        <div class="header-right">

                            @can('create-order')
                                @if (auth()->user()->role === 'super_admin')


                                @elseif (!empty($restaurantSlug) && !empty($branchSlug))
                                    <a href="{{ route('branch.orders.create', ['restaurant' => $restaurantSlug, 'branch' => $branchSlug,]) }}"
                                        class="premium-back-btn">
                                        <i class="fas fa-plus"></i>
                                        Create Order
                                    </a>

                                @elseif (!empty($restaurantSlug))
                                    <a href="{{ route('restaurant.orders.create', ['restaurant' => $restaurantSlug,]) }}"
                                        class="premium-back-btn">
                                        <i class="fas fa-plus"></i>
                                        Create Order
                                    </a>
                                @endif
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="section premium-dashboard pt-0">
            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif
            <div class="card premium-block">
                <div class="card-header premium-card-header">
                    <div>
                        <h4>Orders List</h4>
                        <p class="header-subtext">
                            View and manage all restaurant orders.
                        </p>
                    </div>
                </div>
                <div class="card-body">
                    @if (in_array(optional(auth()->user())->role, ['super_admin', 'owner', 'branch_manager']))
                        <div class="d-flex flex-wrap gap-2 mb-4 order-filters">

                            {{-- All Orders --}}
                            <a href="{{ request()->url() }}"
                                class="btn {{ !request('filter') ? 'btn-primary' : 'btn-light border' }}">
                                <i class="fas fa-list-ul me-2"></i>
                                All Orders
                                <span class="badge bg-white text-dark ms-2">{{ $counts['all'] }}</span>
                            </a>

                            {{-- Today's Orders --}}
                            <a href="{{ request()->fullUrlWithQuery(['filter' => 'today']) }}"
                                class="btn {{ request('filter') == 'today' ? 'btn-primary' : 'btn-light border' }}">
                                <i class="fas fa-calendar-day me-2"></i>
                                Today's Orders
                                <span class="badge bg-white text-dark ms-2">{{ $counts['today'] }}</span>
                            </a>

                            {{-- Registered Customers --}}
                            <a href="{{ request()->fullUrlWithQuery(['filter' => 'customer']) }}"
                                class="btn {{ request('filter') == 'customer' ? 'btn-success text-white' : 'btn-light border' }}">
                                <i class="fas fa-user me-2"></i>
                                Registered Customers
                                <span class="badge bg-white text-dark ms-2">{{ $counts['customer'] }}</span>
                            </a>

                            {{-- Created by Waiter --}}
                            <a href="{{ request()->fullUrlWithQuery(['filter' => 'waiter']) }}"
                                class="btn {{ request('filter') == 'waiter' ? 'btn-info text-white' : 'btn-light border' }}">
                                <i class="fas fa-concierge-bell me-2"></i>
                                Created by Waiter
                                <span class="badge bg-white text-dark ms-2">{{ $counts['waiter'] }}</span>
                            </a>

                            {{-- Created by Waiter Head --}}
                            <a href="{{ request()->fullUrlWithQuery(['filter' => 'waiter_head']) }}"
                                class="btn {{ request('filter') == 'waiter_head' ? 'btn-warning text-dark' : 'btn-light border' }}">
                                <i class="fas fa-user-tie me-2"></i>
                                Created by Waiter Head
                                <span class="badge bg-white text-dark ms-2">{{ $counts['waiter_head'] }}</span>
                            </a>

                            {{-- VIP Orders --}}
                            <a href="{{ request()->fullUrlWithQuery(['filter' => 'vip']) }}"
                                class="btn {{ request('filter') == 'vip' ? 'text-dark' : 'btn-light border' }}"
                                style="{{ request('filter') == 'vip' ? 'background-color:#ffcc00; border-color:#ffcc00;' : '' }}">
                                <i class="fas fa-crown me-2"></i>
                                VIP Orders
                                <span class="badge bg-white text-dark ms-2">{{ $counts['vip'] ?? 0 }}</span>
                            </a>

                        </div>
                    @endif
                    <div class="card mb-4 border-0 shadow-sm">
                        <div class="card-body">
                            <form method="GET" action="{{ url()->current() }}">
                                <div class="row align-items-end">

                                    {{-- Preserve current filter (today, waiter, vip, etc.) --}}
                                    @if(request('filter'))
                                        <input type="hidden" name="filter" value="{{ request('filter') }}">
                                    @endif

                                    <div class="col-md-4">
                                        <label class="form-label fw-bold">From Date</label>
                                        <input type="date" name="from_date" class="form-control"
                                            value="{{ request('from_date') }}">
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label fw-bold">To Date</label>
                                        <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}">
                                    </div>

                                    <div class="col-md-4">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-search me-1"></i> Filter
                                        </button>

                                        <a href="{{ url()->current() }}" class="btn btn-secondary">
                                            <i class="fas fa-redo me-1"></i> Reset
                                        </a>
                                    </div>

                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle" id="tableExport">
                            <thead style="border-bottom: 2px solid var(--de-border);">
                                <tr>
                                    <th
                                        style="padding: 14px 16px; font-weight: 700; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.5px; color: var(--de-text-light); border: none;">
                                        SrNo.</th>
                                    <th
                                        style="padding: 14px 16px; font-weight: 700; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.5px; color: var(--de-text-light); border: none; min-width: 120px;">
                                        Token No</th>
                                    <th
                                        style="padding: 14px 16px; font-weight: 700; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.5px; color: var(--de-text-light); border: none;">
                                        Customer</th>
                                    <th
                                        style="padding: 14px 16px; font-weight: 700; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.5px; color: var(--de-text-light); border: none;">
                                        Mobile</th>
                                    <th
                                        style="padding: 14px 16px; font-weight: 700; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.5px; color: var(--de-text-light); border: none;">
                                        Table No</th>
                                    <th
                                        style="padding: 14px 16px; font-weight: 700; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.5px; color: var(--de-text-light); border: none;">
                                        Table Category</th>
                                    <th
                                        style="padding: 14px 16px; font-weight: 700; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.5px; color: var(--de-text-light); border: none;">
                                        Order Type</th>
                                    <th
                                        style="padding: 14px 16px; font-weight: 700; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.5px; color: var(--de-text-light); border: none;">
                                        Status</th>
                                    <th
                                        style="padding: 14px 16px; font-weight: 700; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.5px; color: var(--de-text-light); border: none;">
                                        Payment Method</th>
                                    <th
                                        style="padding: 14px 16px; font-weight: 700; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.5px; color: var(--de-text-light); border: none;">
                                        Total</th>
                                    <th style="padding: 14px 16px; font-weight: 700; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.5px; color: var(--de-text-light); border: none; text-align: center;"
                                        width="180">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($orders as $key => $order)
                                    <tr style="transition: var(--de-transition); border-bottom: 1px solid var(--de-border);">
                                        <td style="padding: 12px 16px; font-weight: 500; color: var(--de-text);">
                                            {{ $key + 1 }}
                                        </td>
                                        <td style="padding: 12px 16px;">
                                            <span
                                                style="background: var(--de-gradient); color: #fff; padding: 6px 20px; border-radius: 50px; font-size: 0.85rem; font-weight: 700; box-shadow: 0 2px 8px rgba(255, 107, 53, 0.25); display: inline-block; min-width: 80px; text-align: center;">
                                                {{ $order->token_no }}
                                            </span>
                                        </td>
                                        <td style="padding: 12px 16px;">
                                            <strong style="color: var(--de-text);">
                                                {{ $order->customer_name }}
                                            </strong>
                                        </td>
                                        <td style="padding: 12px 16px; color: var(--de-text-light);">
                                            {{ $order->mobile_number }}
                                        </td>
                                        <td style="padding: 12px 16px; color: var(--de-text);">
                                            {{ $order->table_no ?? '-' }}
                                        </td>
                                        <td style="padding: 12px 16px; color: var(--de-text-light);">
                                            {{ $order->table?->category?->name ?? '-' }}
                                        </td>
                                        <td style="padding: 12px 16px;">
                                            @if ($order->order_type == 'vip')
                                                <span
                                                    style="background: var(--de-gradient); color: #fff; padding: 4px 16px; border-radius: 50px; font-size: 0.7rem; font-weight: 600; box-shadow: 0 2px 8px rgba(255, 107, 53, 0.2); display: inline-block;">
                                                    VIP
                                                </span>
                                            @else
                                                <span
                                                    style="background: var(--de-bg); color: var(--de-text); padding: 4px 16px; border-radius: 50px; font-size: 0.7rem; font-weight: 600; border: 1px solid var(--de-border); display: inline-block;">
                                                    Normal
                                                </span>
                                            @endif
                                        </td>
                                        <td style="padding: 12px 16px;">
                                            @if ($order->status == 'pending')
                                                <span
                                                    style="background: #f39c12; color: #fff; padding: 4px 16px; border-radius: 50px; font-size: 0.7rem; font-weight: 600; box-shadow: 0 2px 8px rgba(243, 156, 18, 0.3); display: inline-block;">
                                                    Pending
                                                </span>
                                            @elseif($order->status == 'prepared')
                                                <span
                                                    style="background: #3498db; color: #fff; padding: 4px 16px; border-radius: 50px; font-size: 0.7rem; font-weight: 600; box-shadow: 0 2px 8px rgba(52, 152, 219, 0.3); display: inline-block;">
                                                    Prepared
                                                </span>
                                            @elseif($order->status == 'completed_waiting_cashier')
                                                <span
                                                    style="background: #f39c12; color: #fff; padding: 4px 16px; border-radius: 50px; font-size: 0.7rem; font-weight: 600; box-shadow: 0 2px 8px rgba(243, 156, 18, 0.3); display: inline-block;">
                                                    Waiting Cashier
                                                </span>
                                            @elseif($order->status == 'completed')
                                                <span
                                                    style="background: #2ecc71; color: #fff; padding: 4px 16px; border-radius: 50px; font-size: 0.7rem; font-weight: 600; box-shadow: 0 2px 8px rgba(46, 204, 113, 0.3); display: inline-block;">
                                                    Completed
                                                </span>
                                            @elseif($order->status == 'delivered')
                                                <span
                                                    style="background: var(--de-primary); color: #fff; padding: 4px 16px; border-radius: 50px; font-size: 0.7rem; font-weight: 600; box-shadow: 0 2px 8px rgba(255, 107, 53, 0.3); display: inline-block;">
                                                    Delivered
                                                </span>
                                            @elseif($order->status == 'cancelled')
                                                <span
                                                    style="background: #e74c3c; color: #fff; padding: 4px 16px; border-radius: 50px; font-size: 0.7rem; font-weight: 600; box-shadow: 0 2px 8px rgba(231, 76, 60, 0.3); display: inline-block;">
                                                    Cancelled
                                                </span>
                                            @else
                                                <span
                                                    style="background: var(--de-bg); color: var(--de-text); padding: 4px 16px; border-radius: 50px; font-size: 0.7rem; font-weight: 600; border: 1px solid var(--de-border); display: inline-block;">
                                                    {{ ucfirst($order->status) }}
                                                </span>
                                            @endif
                                        </td>
                                        <td style="padding: 12px 16px;">
                                            @if($order->payment_method)
                                                <div style="display: flex; flex-direction: column; gap: 4px; align-items: flex-start;">
                                                    <span
                                                        style="font-weight: 600; color: var(--de-text); font-size: 0.8rem; text-transform: uppercase;">
                                                        {{ strtoupper($order->payment_method) }}
                                                    </span>
                                                    @if($order->payment_method == 'upi')
                                                        @if($order->payment_status == 'pending')
                                                            <span
                                                                style="background: #f39c12; color: #fff; padding: 2px 12px; border-radius: 50px; font-size: 0.6rem; font-weight: 600; display: inline-block;">
                                                                Pending Verification
                                                            </span>
                                                        @else
                                                            <span
                                                                style="background: #2ecc71; color: #fff; padding: 2px 12px; border-radius: 50px; font-size: 0.6rem; font-weight: 600; display: inline-block;">
                                                                Verified
                                                            </span>
                                                        @endif
                                                    @elseif(in_array($order->payment_method, ['cash', 'card']))
                                                        <span
                                                            style="background: #2ecc71; color: #fff; padding: 2px 12px; border-radius: 50px; font-size: 0.6rem; font-weight: 600; display: inline-block;">
                                                            Paid
                                                        </span>
                                                    @endif
                                                </div>
                                            @else
                                                <span style="color: var(--de-text-light); font-size: 0.8rem;">-</span>
                                            @endif
                                        </td>
                                        <td style="padding: 12px 16px;">
                                            <span
                                                style="font-weight:700;font-size:1rem;background:var(--de-gradient);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;">
                                                {{ $order->branch?->country?->currency_symbol ?? '₹' }}
                                                {{ number_format($order->total, 2) }}
                                            </span>
                                        </td>
                                        <td style="padding: 12px 16px; text-align: center;">
                                            @php
                                                if (!empty($restaurantSlug) && !empty($branchSlug)) {
                                                    $billRoute = route('branch.orders.bill', [
                                                        'restaurant' => $restaurantSlug,
                                                        'branch' => $branchSlug,
                                                        'order' => $order->id,
                                                    ]);
                                                } elseif (!empty($restaurantSlug)) {
                                                    $billRoute = route('restaurant.orders.bill', [
                                                        'restaurant' => $restaurantSlug,
                                                        'order' => $order->id,
                                                    ]);
                                                } else {
                                                    $billRoute = route('orders.bill', [
                                                        'order' => $order->id,
                                                    ]);
                                                }
                                                $canEdit = $order->created_at->gt(now()->subSeconds(10));
                                            @endphp
                                            <div class="dropdown d-inline-block">
                                                <button class="btn btn-sm" type="button" data-bs-toggle="dropdown"
                                                    aria-expanded="false"
                                                    style="background: var(--de-bg); border: 1.5px solid var(--de-border); color: var(--de-text); border-radius: 50px; padding: 6px 14px; transition: var(--de-transition);">
                                                    <i class="fas fa-ellipsis-v" style="font-size: 0.8rem;"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end"
                                                    style="border: 1px solid var(--de-border); border-radius: var(--de-radius-sm); box-shadow: var(--de-shadow); padding: 6px 0; min-width: 180px;">
                                                    {{-- View --}}
                                                    <li>
                                                        @if (!empty($restaurantSlug) && !empty($branchSlug))
                                                                                            <a class="dropdown-item" href="{{ route('branch.orders.show', [
                                                                'restaurant' => $restaurantSlug,
                                                                'branch' => $branchSlug,
                                                                'order' => $order->id,
                                                            ]) }}"
                                                                                                style="padding: 8px 18px; font-size: 0.85rem; color: var(--de-text); transition: var(--de-transition);">
                                                                                                <i class="fas fa-eye me-2" style="color: var(--de-primary);"></i>
                                                                                                View
                                                                                            </a>
                                                        @elseif(!empty($restaurantSlug))
                                                                                            <a class="dropdown-item" href="{{ route('restaurant.orders.show', [
                                                                'restaurant' => $restaurantSlug,
                                                                'order' => $order->id,
                                                            ]) }}"
                                                                                                style="padding: 8px 18px; font-size: 0.85rem; color: var(--de-text); transition: var(--de-transition);">
                                                                                                <i class="fas fa-eye me-2" style="color: var(--de-primary);"></i>
                                                                                                View
                                                                                            </a>
                                                        @else
                                                            <a class="dropdown-item" href="{{ route('orders.show', $order->id) }}"
                                                                style="padding: 8px 18px; font-size: 0.85rem; color: var(--de-text); transition: var(--de-transition);">
                                                                <i class="fas fa-eye me-2" style="color: var(--de-primary);"></i>
                                                                View
                                                            </a>
                                                        @endif
                                                    </li>

                                                    {{-- Edit --}}
                                                    @can('edit-order')
                                                        @if ($order->status == 'pending')
                                                            @php
                                                                $canEdit = $order->created_at->gt(now()->subSeconds(10));
                                                            @endphp
                                                            @if($canEdit)
                                                                                <li>
                                                                                    <a class="dropdown-item" href="{{ !empty($restaurantSlug) && !empty($branchSlug)
                                                                ? route('branch.orders.edit', [
                                                                    'restaurant' => $restaurantSlug,
                                                                    'branch' => $branchSlug,
                                                                    'order' => $order->id,
                                                                ])
                                                                : route('restaurant.orders.edit', [
                                                                    'restaurant' => $restaurantSlug,
                                                                    'order' => $order->id,
                                                                ]) }}"
                                                                                        style="padding: 8px 18px; font-size: 0.85rem; color: var(--de-text); transition: var(--de-transition);">
                                                                                        <i class="fas fa-edit me-2" style="color: #f39c12;"></i>
                                                                                        Edit
                                                                                    </a>
                                                                                </li>
                                                            @else
                                                                <li>
                                                                    <button class="dropdown-item" disabled
                                                                        style="padding: 8px 18px; font-size: 0.85rem; color: var(--de-text-light); opacity: 0.6;">
                                                                        <i class="fas fa-lock me-2" style="color: var(--de-text-light);"></i>
                                                                        Edit Expired
                                                                    </button>
                                                                </li>
                                                            @endif
                                                        @endif
                                                    @endcan

                                                    {{-- Generate/View Bill --}}
                                                    @if($order->status == 'delivered')
                                                        @can('view-bill')
                                                            <li>
                                                                <a class="dropdown-item" href="{{ $billRoute }}"
                                                                    style="padding: 8px 18px; font-size: 0.85rem; color: var(--de-text); transition: var(--de-transition);">
                                                                    @if(!$order->bill_generated_at)
                                                                        <i class="fas fa-file-invoice me-2" style="color: #f39c12;"></i>
                                                                        Generate Bill
                                                                    @else
                                                                        <i class="fas fa-receipt me-2" style="color: var(--de-primary);"></i>
                                                                        View Bill
                                                                    @endif
                                                                </a>
                                                            </li>
                                                        @endcan
                                                    @endif

                                                    {{-- Make Payment --}}
                                                    @can('make-payment')
                                                        @if($order->status == 'delivered')
                                                            <li>
                                                                <button type="button" class="dropdown-item paymentBtn"
                                                                    data-bs-toggle="modal" data-bs-target="#paymentModal"
                                                                    data-order="{{ $order->id }}"
                                                                    data-qr="{{ asset($order->branch?->qrcode ?? '') }}"
                                                                    style="padding: 8px 18px; font-size: 0.85rem; color: var(--de-text); transition: var(--de-transition); width: 100%; text-align: left; border: none; background: transparent;">
                                                                    <i class="fas fa-money-bill-wave me-2" style="color: #2ecc71;"></i>
                                                                    Make Payment
                                                                </button>
                                                            </li>
                                                        @endif
                                                    @endcan

                                                    {{-- Verify Payment --}}
                                                    @can('verify-payment')
                                                        @if($order->status == 'delivered' && $order->payment_method == 'upi' && $order->payment_status == 'pending')
                                                            <li>
                                                                <form method="POST"
                                                                    action="{{ route('orders.verify.payment', $order->id) }}"
                                                                    class="verify-payment-form" style="margin: 0;">
                                                                    @csrf
                                                                    <button type="submit" class="dropdown-item"
                                                                        style="padding: 8px 18px; font-size: 0.85rem; color: var(--de-text); transition: var(--de-transition); width: 100%; text-align: left; border: none; background: transparent;">
                                                                        <i class="fas fa-check me-2" style="color: #2ecc71;"></i>
                                                                        Verify Payment
                                                                    </button>
                                                                </form>
                                                            </li>
                                                        @endif
                                                    @endcan
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="11"
                                            style="padding: 40px 16px; text-align: center; color: var(--de-text-light);">
                                            <div style="font-size: 3rem; opacity: 0.3; display: block; margin-bottom: 10px;">📋
                                            </div>
                                            <p style="font-weight: 600; font-size: 1.1rem; color: var(--de-text);">No Orders Found
                                            </p>
                                            <small style="color: var(--de-text-light);">Start taking orders to see them here</small>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
        <div class="modal fade" id="paymentModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">

                    @if (auth()->user()->branch_id)
                            <form method="POST" action="{{ route('branch.orders.payment', [
                            'restaurant' => $restaurant->slug,
                            'branch' => auth()->user()->branch->slug,
                        ]) }}">
                    @else
                                    <form method="POST" action="{{ route('restaurant.orders.payment', [
                                'restaurant' => $restaurant->slug,
                            ]) }}">
                        @endif

                            @csrf


                            <input type="hidden" name="order_id" id="order_id">

                            <div class="modal-header">
                                <h5 class="modal-title">
                                    Make Payment
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>

                            <div class="modal-body">

                                <label class="form-label">
                                    Payment Method
                                </label>

                                <select name="payment_method" id="payment_method" class="form-control" required>

                                    <option value="">
                                        Select Payment Method
                                    </option>

                                    <option value="cash">
                                        Cash
                                    </option>

                                    <option value="upi">
                                        UPI
                                    </option>

                                    <option value="card">
                                        Card
                                    </option>

                                </select>

                                <div id="upiSection" class="mt-3" style="display:none;">

                                    <h6>
                                        Scan QR Code
                                    </h6>

                                    <img id="branchQr" src="" class="img-fluid border rounded">

                                </div>

                            </div>

                            <div class="modal-footer">

                                <button type="submit" class="btn btn-primary">
                                    Confirm Payment
                                </button>

                            </div>

                        </form>

                </div>
            </div>
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', function () {

                document.querySelectorAll('.paymentBtn').forEach(btn => {

                    btn.addEventListener('click', function () {

                        document.getElementById('order_id').value =
                            this.dataset.order;

                        document.getElementById('branchQr').src =
                            this.dataset.qr;
                    });

                });

                document.getElementById('payment_method')
                    .addEventListener('change', function () {

                        let upiSection =
                            document.getElementById('upiSection');

                        if (this.value === 'upi') {

                            upiSection.style.display = 'block';

                        } else {

                            upiSection.style.display = 'none';

                        }
                    });

            });
        </script>
    @endsection
    @if (session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: "{{ session('success') }}",
                    timer: 2000,
                    showConfirmButton: false
                });
            });
        </script>
    @endif

    <script>
        document.addEventListener('DOMContentLoaded', function () {

            document.querySelectorAll('.delete-form').forEach(form => {

                form.addEventListener('submit', function (e) {

                    e.preventDefault();

                    Swal.fire({
                        title: 'Deactivate Category?',
                        text: 'This action can be reverted later.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Yes',
                        cancelButtonText: 'Cancel'
                    }).then((result) => {

                        if (result.isConfirmed) {
                            form.submit();
                        }

                    });

                });

            });

        });


        document.addEventListener('DOMContentLoaded', function () {

            document.querySelectorAll('.verify-payment-form').forEach(form => {

                form.addEventListener('submit', function (e) {

                    e.preventDefault();

                    Swal.fire({
                        title: 'Verify Payment?',
                        text: 'Are you sure you have received the payment? This action cannot be undone.It will show in Completed',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#198754',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Yes, Verify',
                        cancelButtonText: 'Cancel'
                    }).then((result) => {

                        if (result.isConfirmed) {
                            form.submit();
                        }

                    });

                });

            });

        });

    </script>

@else
    @php
        abort(403);
    @endphp
@endcan
