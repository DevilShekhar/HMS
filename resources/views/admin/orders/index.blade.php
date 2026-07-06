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
                                @if ($branchSlug)
                                    <a href="{{ route('branch.orders.create', ['restaurant' => $restaurantSlug, 'branch' => $branchSlug,]) }}"
                                        class="btn btn-primary">
                                        <i class="fas fa-plus"></i>
                                        Create Order
                                    </a>
                                @else
                                    <a href="{{ route('restaurant.orders.create', ['restaurant' => $restaurantSlug,]) }}"
                                        class="btn btn-primary">
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
                            <a href="{{ request()->url() }}"
                                class="btn {{ !request('filter') ? 'btn-primary' : 'btn-light border' }}">
                                <i class="fas fa-list-ul me-2"></i>
                                All Orders
                                <span class="badge bg-white text-dark ms-2">{{ $counts['all'] }}</span>
                            </a>
                            <a href="{{ request()->fullUrlWithQuery(['filter' => 'today']) }}"
                                class="btn {{ request('filter') == 'today' ? 'btn-primary' : 'btn-light border' }}">
                                <i class="fas fa-calendar-day me-2"></i>
                                Today's
                                <span class="badge bg-white text-dark ms-2">{{ $counts['today'] }}</span>
                            </a>
                            <a href="{{ request()->fullUrlWithQuery(['filter' => 'customer']) }}"
                                class="btn {{ request('filter') == 'customer' ? 'btn-success text-white' : 'btn-light border' }}">
                                <i class="fas fa-user me-2"></i>
                                Customer
                                <span class="badge bg-white text-dark ms-2">{{ $counts['customer'] }}</span>
                            </a>
                            <a href="{{ request()->fullUrlWithQuery(['filter' => 'waiter']) }}"
                                class="btn {{ request('filter') == 'waiter' ? 'btn-info text-white' : 'btn-light border' }}">
                                <i class="fas fa-concierge-bell me-2"></i>
                                Waiter
                                <span class="badge bg-white text-dark ms-2">{{ $counts['waiter'] }}</span>
                            </a>
                            <a href="{{ request()->fullUrlWithQuery(['filter' => 'waiter_head']) }}"
                                class="btn {{ request('filter') == 'waiter_head' ? 'btn-warning text-dark' : 'btn-light border' }}">
                                <i class="fas fa-user-tie me-2"></i>
                                Waiter Head
                                <span class="badge bg-white text-dark ms-2">{{ $counts['waiter_head'] }}</span>
                            </a>
                            <a href="{{ request()->fullUrlWithQuery(['filter' => 'vip']) }}"
                                class="btn {{ request('filter') == 'vip' ? 'text-dark' : 'btn-light border' }}"
                                style="{{ request('filter') == 'vip' ? 'background-color:#ffcc00; border-color:#ffcc00;' : '' }}">
                                <i class="fas fa-user-tie me-2"></i>
                                VIP Orders
                                <span class="badge bg-white text-dark ms-2">{{ $counts['vip'] }}</span>
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
                            <thead>
                                <tr>
                                    <th>SrNo.</th>
                                    <th>Token No</th>
                                    <th>Customer</th>
                                    <th>Mobile</th>
                                    <th>Table No</th>
                                    <th>Table Category</th>
                                    <th>Order Type</th>
                                    <th>Status</th>
                                    <th>Payment Method</th>
                                    <th>Total</th>
                                    <th width="180">
                                        Action
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($orders as $key => $order)
                                    <tr>
                                        <td>
                                            {{ $key + 1 }}
                                        </td>
                                        <td>
                                            <span class="badge bg-info">
                                                {{ $order->token_no }}
                                            </span>
                                        </td>
                                        <td>
                                            <strong>
                                                {{ $order->customer_name }}
                                            </strong>
                                        </td>
                                        <td>
                                            {{ $order->mobile_number }}
                                        </td>
                                        <td>
                                            {{ $order->table_no ?? '-' }}
                                        </td>
                                        <td>
                                            {{ $order->table?->category?->name ?? '-' }}
                                        </td>
                                        <td class="text-white">
                                            @if ($order->order_type == 'vip')
                                                <span class="badge bg-warning text-dark">
                                                    VIP
                                                </span>
                                            @else
                                                <span class="badge bg-primary">
                                                    Normal
                                                </span>
                                            @endif
                                        </td>
                                        <td class="text-white">
                                            @if ($order->status == 'pending')
                                                <span class="badge bg-warning text-dark">
                                                    Pending
                                                </span>
                                            @elseif($order->status == 'prepared')
                                                <span class="badge bg-info">
                                                    Prepared
                                                </span>
                                            @elseif($order->status == 'completed_waiting_cashier')
                                                <span class="badge bg-warning text-dark">
                                                    Waiting Cashier Approval
                                                </span>
                                            @elseif($order->status == 'completed')
                                                <span class="badge bg-success">
                                                    Completed
                                                </span>
                                            @elseif($order->status == 'delivered')
                                                <span class="badge bg-primary">
                                                    Delivered
                                                </span>
                                            @elseif($order->status == 'cancelled')
                                                <span class="badge bg-danger">
                                                    Cancelled
                                                </span>
                                            @else
                                                <span class="badge bg-secondary">
                                                    {{ ucfirst($order->status) }}
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($order->payment_method)
                                                {{ strtoupper($order->payment_method) }}
                                                @if($order->payment_method == 'upi')
                                                    @if($order->payment_status == 'pending')
                                                        <span class="badge bg-warning">
                                                            Pending Verification
                                                        </span>
                                                    @else
                                                        <span class="badge bg-success">
                                                            Verified
                                                        </span>
                                                    @endif
                                                @elseif(in_array($order->payment_method, ['cash', 'card']))
                                                    <span class="badge bg-success">
                                                        Paid
                                                    </span>
                                                @endif
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td> ₹{{ number_format($order->total, 2) }} </td>

                                        <td>
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
                                            <div class="dropdown">

                                                <button class="btn btn-sm btn-light border" type="button" data-bs-toggle="dropdown"
                                                    aria-expanded="false">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end">
                                                    {{-- View --}}
                                                    <li>
                                                        @if (!empty($restaurantSlug) && !empty($branchSlug))
                                                                                            <a class="dropdown-item" href="{{ route('branch.orders.show', [
                                                                'restaurant' => $restaurantSlug,
                                                                'branch' => $branchSlug,
                                                                'order' => $order->id,
                                                            ]) }}">
                                                                                                <i class="fas fa-eye me-2 text-info"></i>
                                                                                                View
                                                                                            </a>
                                                        @elseif(!empty($restaurantSlug))
                                                                                            <a class="dropdown-item" href="{{ route('restaurant.orders.show', [
                                                                'restaurant' => $restaurantSlug,
                                                                'order' => $order->id,
                                                            ]) }}">
                                                                                                <i class="fas fa-eye me-2 text-info"></i>
                                                                                                View
                                                                                            </a>
                                                        @else
                                                            <a class="dropdown-item" href="{{ route('orders.show', $order->id) }}">
                                                                <i class="fas fa-eye me-2 text-info"></i>
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
                                                                ]) }}">

                                                                                        <i class="fas fa-edit me-2 text-warning"></i>
                                                                                        Edit
                                                                                    </a>
                                                                                </li>

                                                            @else
                                                                <li>
                                                                    <button class="dropdown-item text-muted" disabled>
                                                                        <i class="fas fa-lock me-2"></i>
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
                                                                <a class="dropdown-item" href="{{ $billRoute }}">
                                                                    @if(!$order->bill_generated_at)
                                                                        <i class="fas fa-file-invoice me-2 text-warning"></i>
                                                                        Generate Bill
                                                                    @else
                                                                        <i class="fas fa-receipt me-2 text-warning"></i>
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
                                                                    data-qr="{{ asset($order->branch?->qrcode ?? '') }}">

                                                                    <i class="fas fa-money-bill-wave me-2 text-success"></i>

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
                                                                    class="verify-payment-form">

                                                                    @csrf

                                                                    <button type="submit" class="dropdown-item">

                                                                        <i class="fas fa-check me-2 text-success"></i>

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
                                        <td colspan="9" class="text-center">
                                            No Orders Found
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
