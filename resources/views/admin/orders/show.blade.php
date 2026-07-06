@extends('layouts.app')

@section('content')
    <div class="section">
        <div class="page-card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h4>Order Details</h4>
                    <div>
                        @if ($order->status == 'pending')
                            <span class="badge bg-warning">Pending</span>
                        @elseif($order->status == 'prepared')
                            <span class="badge bg-info">Prepared</span>
                        @elseif($order->status == 'completed')
                            <span class="badge bg-success">Completed</span>
                        @elseif($order->status == 'delivered')
                            <span class="badge bg-primary">Delivered</span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="card-body ">
                <div class="card shadow-md border-0 rounded-4 mb-4">
                    <h5 class="p-3">Order Status</h5>

                    @php
                        $statuses = [
                            'pending' => 1,
                            'prepared' => 2,
                            'delivered' => 3,
                            'completed' => 4,
                        ];

                        $currentStep = $statuses[$order->status] ?? 1;
                    @endphp

                    <div class="order-stepper mt-4">
                        <div class="d-flex justify-content-between align-items-center position-relative">

                            <!-- Step 1 -->
                            <div class="step text-center flex-fill
                        {{ $currentStep > 1 ? 'completed' : '' }}
                        {{ $currentStep == 1 ? 'active' : '' }}">
                                <div class="step-icon mx-auto mb-2">
                                    @if($currentStep > 1)
                                        <i class="fas fa-check"></i>
                                    @else
                                        <i class="fas fa-shopping-cart"></i>
                                    @endif
                                </div>
                                <div class="step-number">1</div>
                                <div class="step-label">Order Placed</div>
                            </div>

                            <div class="step-connector {{ $currentStep >= 2 ? 'active' : '' }}"></div>

                            <!-- Step 2 -->
                            <div class="step text-center flex-fill
                        {{ $currentStep > 2 ? 'completed' : '' }}
                        {{ $currentStep == 2 ? 'active' : '' }}">
                                <div class="step-icon mx-auto mb-2">
                                    @if($currentStep > 2)
                                        <i class="fas fa-check"></i>
                                    @else
                                        <i class="fas fa-utensils"></i>
                                    @endif
                                </div>
                                <div class="step-number">2</div>
                                <div class="step-label">Prepared</div>
                            </div>

                            <div class="step-connector {{ $currentStep >= 3 ? 'active' : '' }}"></div>

                            <!-- Step 3 -->
                            <div class="step text-center flex-fill
                        {{ $currentStep > 3 ? 'completed' : '' }}
                        {{ $currentStep == 3 ? 'active' : '' }}">
                                <div class="step-icon mx-auto mb-2">
                                    @if($currentStep > 3)
                                        <i class="fas fa-check"></i>
                                    @else
                                        <i class="fas fa-motorcycle"></i>
                                    @endif
                                </div>
                                <div class="step-number">3</div>
                                <div class="step-label">Delivered</div>
                            </div>

                            <div class="step-connector {{ $currentStep >= 4 ? 'active' : '' }}"></div>

                            <!-- Step 4 -->
                            <div class="step text-center flex-fill
                        {{ $currentStep >= 4 ? 'completed' : '' }}
                        {{ $currentStep == 4 ? 'active' : '' }}">
                                <div class="step-icon mx-auto mb-2">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                                <div class="step-number">4</div>
                                <div class="step-label">Completed</div>
                            </div>

                        </div>
                    </div>
                </div>
                <!-- Order Info -->
               <div class="card border-0 shadow-md rounded-4 mb-4 order-view" >
                    <div class="card-body p-4">
                        <div class="row">
                            <!-- Left -->
                            <div class="col-lg-4 border-end">
                                <div class="info-item mb-4">
                                    <div class="icon bg-primary-over ">
                                        <i class="fas fa-receipt text-primary-over"></i>
                                    </div>
                                    <div>
                                        <span class="label">Order ID</span>
                                        <h5>{{ $order->id }}</h5>
                                    </div>
                                </div>
                                <div class="info-item">
                                    <div class="icon bg-purple-over">
                                        <i class="fas fa-user text-purple"></i>
                                    </div>
                                    <div>
                                        <span class="label">Customer Name</span>
                                        <h6>{{ $order->customer->name ?? ($order->customer_name ?? 'Walk-in Customer') }}</h6>
                                    </div>
                                </div>
                            </div>
                            <!-- Center -->
                            <div class="col-lg-4 border-end">
                                <div class="info-item mb-4">
                                    <div class="icon bg-primary-over">
                                        <i class="fas fa-tag text-primary-over"></i>
                                    </div>
                                    <div>
                                        <span class="label">Token No</span>
                                        @if($order->order_type=='vip')
                                            <span class="badge bg-warning text-dark px-3 py-2">
                                                {{ $order->token_no }}
                                            </span>
                                        @else
                                            <span class="badge bg-primary px-3 py-2">
                                                {{ $order->token_no }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="info-item">
                                    <div class="icon bg-success-over">
                                        <i class="fas fa-mobile-alt text-success"></i>
                                    </div>
                                    <div>
                                        <span class="label">Mobile</span>
                                        <h6>{{ $order->mobile_number ?? 'N/A' }}</h6>
                                    </div>
                                </div>
                            </div>
                            <!-- Right -->
                            <div class="col-lg-4">
                                <div class="info-item mb-4">
                                    <div class="icon bg-warning-over">
                                        <i class="fas fa-chair text-warning"></i>
                                    </div>
                                    <div>
                                        <span class="label">Table No</span>
                                        <h5>{{ $order->table_no ?? ($order->table?->name ?? 'Take Away') }}</h5>
                                    </div>
                                </div>
                                <div class="info-item">
                                    <div class="icon bg-danger-over">
                                        <i class="far fa-calendar-alt text-danger"></i>
                                    </div>

                                    <div>
                                        <span class="label">Order Date</span>
                                        <h6>{{ $order->created_at->format('d M Y, h:i A') }}</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <hr>
               
                <div class="table-responsive card p-2">
                     <!-- Order Items -->
                 <h4 class="fw-bold mb-4 order-views">
                    <i class="fas fa-concierge-bell text-primary-over me-2"></i>
                    Order Items
                </h4>
                    <table class="table table-bordered table-striped">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Item Name</th>
                                <th class="text-center">Qty</th>
                                <th class="text-end">Price</th>
                                <th class="text-end">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($order->items as $item)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $item->menuItem->name ?? '-' }}</td>
                                    <td class="text-center"><span class="qty-badge">{{ $item->quantity }} </span></td>
                                    <td class="text-end">₹{{ number_format($item->price, 2) }}</td>
                                    <td class="text-end">₹{{ number_format($item->subtotal, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Grand Total -->
                <div class="text-end mt-4">
                    <h5>
                        Grand Total :
                        <strong>₹{{ number_format($order->total ?? $order->items->sum('subtotal'), 2) }}</strong>
                    </h5>
                </div>

                <hr class="my-4">

                <!-- Action Buttons -->
                <div class="d-flex flex-wrap gap-2">
                    @php
                        $restaurantSlug = request()->route('restaurant');
                        $branchSlug = request()->route('branch');
                    @endphp

                    <!-- Back Button -->
                    @if (auth()->user()->role === 'super_admin')
                        <a href="{{ route('orders.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to Orders
                        </a>
                    @elseif($branchSlug)
                        <a href="{{ route('branch.orders.index', ['restaurant' => $restaurantSlug, 'branch' => $branchSlug]) }}"
                            class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to Orders
                        </a>
                    @elseif($restaurantSlug)
                        <a href="{{ route('restaurant.orders.index', ['restaurant' => $restaurantSlug]) }}"
                            class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to Orders
                        </a>
                    @endif

                    <!-- Status Actions -->
                    <td class="text-center">
                        @if ($order->status == 'pending' && auth()->user()->role == 'chef')
                            <form method="POST"
                                action="{{ route('restaurant.orders.prepare', ['restaurant' => $restaurant->slug, 'order' => $order->id]) }}"
                                class="swal-confirm" data-title="Mark this order as Prepared?"
                                data-text="Are you sure you want to mark this order as prepared?">
                                @csrf
                                <button type="submit" class="btn btn-success btn-sm">
                                    <i class="fas fa-check"></i> Mark Prepared
                                </button>
                            </form>
                        @endif

                        @if ($order->status == 'prepared' && in_array(auth()->user()->role, ['waiter', 'waiter_head']))
                            <form method="POST"
                                action="{{ route('restaurant.orders.delivered', ['restaurant' => $restaurant->slug, 'order' => $order->id]) }}"
                                class="swal-confirm" data-title="Mark this order as Delivered?"
                                data-text="Are you sure you want to mark this order as delivered?">
                                @csrf
                                <button type="submit" class="btn btn-primary btn-sm">
                                    <i class="fas fa-truck"></i> Mark Delivered
                                </button>
                            </form>
                        @endif
                    </td>
                </div>

            </div>


        </div>
    </div>
@endsection
{{-- notification scritp --}}
<audio id="notificationSound" preload="auto">
    <source src="{{ asset('sounds/order-notification.mp3') }}" type="audio/mpeg">
</audio>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {

        const notificationSound = document.getElementById('notificationSound');
        let isProcessing = false;

        document.addEventListener('click', function unlockAudio() {
            notificationSound.play().then(() => {
                notificationSound.pause();
                notificationSound.currentTime = 0;
            }).catch(() => { });
            document.removeEventListener('click', unlockAudio);
        }, {
            once: true
        });

        setInterval(fetchNotifications, 4000);

        async function fetchNotifications() {
            if (isProcessing) return;

            try {
                const response = await fetch("{{ route('chef.notifications') }}");
                const data = await response.json();

                if (!data || !data.length) return;

                isProcessing = true;
                const notif = data[0];
                const d = notif.data;

                notificationSound.currentTime = 0;
                notificationSound.play().catch(() => { });

                Swal.fire({
                    icon: 'success',
                    title: d.status === 'prepared' ? '✅ Order Ready!' : '🛎 New Order',
                    html: `<strong>Token #${d.token_no}</strong><br>${d.message}`,
                    confirmButtonText: 'View Order',
                    timer: 20000,
                    allowOutsideClick: false
                }).then(async (result) => {
                    await markReadAndDelete(notif.id);

                    if (result.isConfirmed) {
                        let url = '/' + d.restaurant_slug;
                        if (d.branch_slug) url += '/' + d.branch_slug;
                        url += '/orders/' + d.order_id;
                        window.location.href = url;
                    }
                }).finally(() => isProcessing = false);

            } catch (e) {
                console.error(e);
                isProcessing = false;
            }
        }

        async function markReadAndDelete(id) {
            const token = '{{ csrf_token() }}';
            await fetch(`/notifications/read/${id}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': token
                }
            });
            await fetch(`/notifications/delete/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': token
                }
            });
        }
    });
    document.addEventListener('submit', function (e) {

        const form = e.target;

        if (form.classList.contains('swal-confirm')) {
            e.preventDefault();

            let title = form.dataset.title || 'Are you sure?';
            let text = form.dataset.text || '';

            Swal.fire({
                title: title,
                text: text,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, Confirm'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        }
    });
</script>
