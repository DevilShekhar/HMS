@extends('layouts.app')

@section('content')
    @php
        $currency = $order->branch?->country?->currency_symbol ?? '₹';
    @endphp
    <div class="section">
        <div class="page-card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h4>Order Details</h4>
                    <div>
                        @if ($order->status == 'pending')
                            <span
                                style="display: inline-block; padding: 4px 16px; border-radius: 50px; font-size: 0.7rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; background: #fef3e2; color: #f39c12; border: 1px solid #fde8c8;">
                                <i class="fas fa-clock me-1"></i> Pending
                            </span>
                        @elseif($order->status == 'prepared')
                            <span
                                style="display: inline-block; padding: 4px 16px; border-radius: 50px; font-size: 0.7rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; background: #e3f2fd; color: #2196f3; border: 1px solid #bbdefb;">
                                <i class="fas fa-utensils me-1"></i> Prepared
                            </span>
                        @elseif($order->status == 'completed')
                            <span
                                style="display: inline-block; padding: 4px 16px; border-radius: 50px; font-size: 0.7rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; background: #e8f5e9; color: #43a047; border: 1px solid #c8e6c9;">
                                <i class="fas fa-check-circle me-1"></i> Completed
                            </span>
                        @elseif($order->status == 'delivered')
                            <span
                                style="display: inline-block; padding: 4px 16px; border-radius: 50px; font-size: 0.7rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; background: #e8f5e9; color: #2e7d32; border: 1px solid #c8e6c9;">
                                <i class="fas fa-truck me-1"></i> Delivered
                            </span>
                        @elseif($order->status == 'cancelled')
                            <span
                                style="display: inline-block; padding: 4px 16px; border-radius: 50px; font-size: 0.7rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; background: #fce4ec; color: #e53935; border: 1px solid #f8bbd0;">
                                <i class="fas fa-times-circle me-1"></i> Cancelled
                            </span>
                        @else
                            <span
                                style="display: inline-block; padding: 4px 16px; border-radius: 50px; font-size: 0.7rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; background: var(--de-bg); color: var(--de-text-light); border: 1px solid var(--de-border);">
                                {{ ucfirst($order->status) }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="card-body ">
                <div class="card shadow-sm border-0 rounded-4 mb-4"
                    style="border: 1px solid var(--de-border) !important; border-radius: var(--de-radius) !important; overflow: hidden;">
                    <div
                        style="padding: 16px 20px; border-bottom: 1px solid var(--de-border); background: var(--de-white);">
                        <h5 style="margin: 0; font-weight: 700; color: var(--de-text); font-size: 1rem;">
                            <i class="fas fa-chart-line me-2" style="color: #32CD32;"></i> Order Status
                        </h5>
                    </div>

                    @php
                        $statuses = [
                            'pending' => 1,
                            'prepared' => 2,
                            'delivered' => 3,
                            'completed' => 4,
                        ];

                        $currentStep = $statuses[$order->status] ?? 1;
                    @endphp

                    <div style="padding: 20px 16px 24px; background: var(--de-white);">
                        <!-- Mobile/Desktop Stepper -->
                        <div
                            style="display: flex; justify-content: space-between; align-items: flex-start; position: relative; max-width: 100%; margin: 0 auto; flex-wrap: nowrap;">

                            <!-- Step 1: Order Placed -->
                            <div
                                style="text-align: center; flex: 1; position: relative; z-index: 2; min-width: 0; padding: 0 2px;">
                                <div style="width: 40px; height: 40px; border-radius: 50%; margin: 0 auto 6px; display: flex; align-items: center; justify-content: center; font-size: 0.9rem;
                                        {{ $currentStep >= 1 ? 'background: #32CD32; color: #fff; box-shadow: 0 4px 16px rgba(50, 205, 50, 0.35);' : 'background: var(--de-bg); color: var(--de-text-light); border: 2px solid var(--de-border);' }}
                                        transition: var(--de-transition);">
                                    @if($currentStep > 1)
                                        <i class="fas fa-check"></i>
                                    @else
                                        <i class="fas fa-shopping-cart"></i>
                                    @endif
                                </div>
                                <div
                                    style="font-size: 0.5rem; font-weight: 700; color: {{ $currentStep >= 1 ? '#32CD32' : 'var(--de-text-light)' }}; letter-spacing: 0.3px; margin-bottom: 1px; text-transform: uppercase;">
                                    Step 1</div>
                                <div
                                    style="font-weight: 600; font-size: 0.65rem; color: {{ $currentStep >= 1 ? 'var(--de-text)' : 'var(--de-text-light)' }}; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                    Placed</div>
                            </div>

                            <!-- Connector 1 -->
                            <div
                                style="flex: 1; height: 3px; margin: 18px -4px 0 -4px; background: {{ $currentStep >= 2 ? '#32CD32' : 'var(--de-border)' }}; border-radius: 10px; position: relative; z-index: 1; transition: var(--de-transition); min-width: 10px;">
                                @if($currentStep >= 2)
                                    <div
                                        style="position: absolute; right: -5px; top: -4px; width: 10px; height: 10px; border-radius: 50%; background: #32CD32; box-shadow: 0 0 12px rgba(50, 205, 50, 0.3);">
                                    </div>
                                @endif
                            </div>

                            <!-- Step 2: Prepared -->
                            <div
                                style="text-align: center; flex: 1; position: relative; z-index: 2; min-width: 0; padding: 0 2px;">
                                <div style="width: 40px; height: 40px; border-radius: 50%; margin: 0 auto 6px; display: flex; align-items: center; justify-content: center; font-size: 0.9rem;
                                        {{ $currentStep >= 2 ? 'background: #32CD32; color: #fff; box-shadow: 0 4px 16px rgba(50, 205, 50, 0.35);' : 'background: var(--de-bg); color: var(--de-text-light); border: 2px solid var(--de-border);' }}
                                        transition: var(--de-transition);">
                                    @if($currentStep > 2)
                                        <i class="fas fa-check"></i>
                                    @else
                                        <i class="fas fa-utensils"></i>
                                    @endif
                                </div>
                                <div
                                    style="font-size: 0.5rem; font-weight: 700; color: {{ $currentStep >= 2 ? '#32CD32' : 'var(--de-text-light)' }}; letter-spacing: 0.3px; margin-bottom: 1px; text-transform: uppercase;">
                                    Step 2</div>
                                <div
                                    style="font-weight: 600; font-size: 0.65rem; color: {{ $currentStep >= 2 ? 'var(--de-text)' : 'var(--de-text-light)' }}; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                    Prepared</div>
                            </div>

                            <!-- Connector 2 -->
                            <div
                                style="flex: 1; height: 3px; margin: 18px -4px 0 -4px; background: {{ $currentStep >= 3 ? '#32CD32' : 'var(--de-border)' }}; border-radius: 10px; position: relative; z-index: 1; transition: var(--de-transition); min-width: 10px;">
                                @if($currentStep >= 3)
                                    <div
                                        style="position: absolute; right: -5px; top: -4px; width: 10px; height: 10px; border-radius: 50%; background: #32CD32; box-shadow: 0 0 12px rgba(50, 205, 50, 0.3);">
                                    </div>
                                @endif
                            </div>

                            <!-- Step 3: Delivered -->
                            <div
                                style="text-align: center; flex: 1; position: relative; z-index: 2; min-width: 0; padding: 0 2px;">
                                <div style="width: 40px; height: 40px; border-radius: 50%; margin: 0 auto 6px; display: flex; align-items: center; justify-content: center; font-size: 0.9rem;
                                        {{ $currentStep >= 3 ? 'background: #32CD32; color: #fff; box-shadow: 0 4px 16px rgba(50, 205, 50, 0.35);' : 'background: var(--de-bg); color: var(--de-text-light); border: 2px solid var(--de-border);' }}
                                        transition: var(--de-transition);">
                                    @if($currentStep > 3)
                                        <i class="fas fa-check"></i>
                                    @else
                                        <i class="fas fa-motorcycle"></i>
                                    @endif
                                </div>
                                <div
                                    style="font-size: 0.5rem; font-weight: 700; color: {{ $currentStep >= 3 ? '#32CD32' : 'var(--de-text-light)' }}; letter-spacing: 0.3px; margin-bottom: 1px; text-transform: uppercase;">
                                    Step 3</div>
                                <div
                                    style="font-weight: 600; font-size: 0.65rem; color: {{ $currentStep >= 3 ? 'var(--de-text)' : 'var(--de-text-light)' }}; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                    Delivered</div>
                            </div>

                            <!-- Connector 3 -->
                            <div
                                style="flex: 1; height: 3px; margin: 18px -4px 0 -4px; background: {{ $currentStep >= 4 ? '#32CD32' : 'var(--de-border)' }}; border-radius: 10px; position: relative; z-index: 1; transition: var(--de-transition); min-width: 10px;">
                                @if($currentStep >= 4)
                                    <div
                                        style="position: absolute; right: -5px; top: -4px; width: 10px; height: 10px; border-radius: 50%; background: #32CD32; box-shadow: 0 0 12px rgba(50, 205, 50, 0.3);">
                                    </div>
                                @endif
                            </div>

                            <!-- Step 4: Completed -->
                            <div
                                style="text-align: center; flex: 1; position: relative; z-index: 2; min-width: 0; padding: 0 2px;">
                                <div style="width: 40px; height: 40px; border-radius: 50%; margin: 0 auto 6px; display: flex; align-items: center; justify-content: center; font-size: 0.9rem;
                                        {{ $currentStep >= 4 ? 'background: #32CD32; color: #fff; box-shadow: 0 4px 16px rgba(50, 205, 50, 0.35);' : 'background: var(--de-bg); color: var(--de-text-light); border: 2px solid var(--de-border);' }}
                                        transition: var(--de-transition);">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                                <div
                                    style="font-size: 0.5rem; font-weight: 700; color: {{ $currentStep >= 4 ? '#32CD32' : 'var(--de-text-light)' }}; letter-spacing: 0.3px; margin-bottom: 1px; text-transform: uppercase;">
                                    Step 4</div>
                                <div
                                    style="font-weight: 600; font-size: 0.65rem; color: {{ $currentStep >= 4 ? 'var(--de-text)' : 'var(--de-text-light)' }}; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                    Completed</div>
                            </div>

                        </div>

                        <!-- Current Status Badge - Lemon Green -->
                        <div
                            style="text-align: center; margin-top: 18px; padding-top: 16px; border-top: 1px solid var(--de-border);">
                            <span
                                style="display: inline-flex; align-items: center; gap: 8px; padding: 6px 18px; border-radius: 50px;
                        background: #32CD32; color: #fff; font-weight: 600; font-size: 0.8rem; box-shadow: 0 4px 16px rgba(50, 205, 50, 0.35);">
                                <i class="fas fa-circle"
                                    style="font-size: 0.5rem; animation: pulse-green 1.5s ease-in-out infinite;"></i>
                                <span style="text-transform: uppercase;">
                                    @if($order->status == 'pending')
                                        Cooking with love...
                                    @elseif($order->status == 'prepared')
                                        Ready to serve!
                                    @elseif($order->status == 'delivered')
                                        Enjoy your meal!
                                    @elseif($order->status == 'completed')
                                        Hope Every Bite Made You Smile!
                                    @else
                                        {{ ucfirst($order->status) }}
                                    @endif
                                </span>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Order Info -->
                <div class="card border-0 shadow-md rounded-4 mb-4 order-view">
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
                                        <h6>{{ $order->customer->name ?? ($order->customer_name ?? 'Walk-in Customer') }}
                                        </h6>
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
                                        @if($order->order_type == 'vip')
                                            <span class="badge bg-warning text-white px-3 py-2">
                                                {{ $order->token_no }}
                                            </span>
                                        @else
                                            <span class="badge bg-primary text-white px-3 py-2">
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
                                        <h6>
                                            {{ \Carbon\Carbon::parse($order->order_datetime)->format('d M Y, h:i A') }}
                                        </h6>
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
                                    <td>
                                        @if($item->menuItem && $item->menuItem->image)
                                            <img src="{{ asset($item->menuItem->image) }}" alt="{{ $item->menuItem->name }}"
                                                width="40" height="40" class="me-2" style="border-radius:8px; object-fit:cover;">
                                        @endif

                                        {{ $item->menuItem->name ?? '-' }}
                                    </td>
                                    <td class="text-center"><span class="qty-badge">{{ $item->quantity }} </span></td>
                                    <td class="text-end">{{$currency}}{{ number_format($item->price, 2) }}</td>
                                    <td class="text-end">{{$currency}}{{ number_format($item->subtotal, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>



                <hr class="my-4">

                <!-- Action Buttons -->
                <div class="card shadow-sm border-0 mt-4"
                    style="border-radius: var(--de-radius-sm); border: 1px solid var(--de-border) !important;">
                    <div class="card-body" style="padding: 24px 28px;">

                        <div class="row align-items-center">

                            {{-- Grand Total --}}
                            <div class="col-lg-4 mb-3 mb-lg-0">
                                <div class="d-flex align-items-center">

                                    <div class="icon rounded-3 p-3 me-3"
                                        style="background: rgba(255, 107, 53, 0.1); color: var(--de-primary);">
                                        <i class="fas fa-wallet fa-lg"></i>
                                    </div>

                                    <div>
                                        <small class="d-block"
                                            style="color: var(--de-text-light); font-weight: 500; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px;">Grand
                                            Total</small>
                                        <h2 class="mb-0 fw-bold"
                                            style="background: var(--de-gradient); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">
                                            {{$currency}}{{ number_format($order->total ?? $order->items->sum('subtotal'), 2) }}
                                        </h2>
                                    </div>

                                </div>
                            </div>

                            {{-- Action Buttons --}}
                            <div class="col-lg-8">
                                <div class="d-flex justify-content-lg-end flex-wrap gap-2">

                                    @php
                                        $restaurantSlug = request()->route('restaurant');
                                        $branchSlug = request()->route('branch');
                                    @endphp

                                    {{-- Back Button --}}
                                    @if(auth()->user()->role === 'super_admin')
                                        <a href="{{ route('orders.index') }}" class="btn"
                                            style="display: inline-flex; align-items: center; justify-content: center; gap: 8px; padding: 10px 20px; min-width: 140px; height: 44px;   background: var(--de-bg); color: var(--de-text); border: 1px solid var(--de-border); transition: var(--de-transition); font-weight: 500; text-decoration: none; font-size: 0.85rem;">
                                            <i class="fas fa-arrow-left"></i> Back
                                        </a>
                                    @elseif($branchSlug)
                                        <a href="{{ route('branch.orders.index', ['restaurant' => $restaurantSlug, 'branch' => $branchSlug]) }}"
                                            class="btn"
                                            style="display: inline-flex; align-items: center; justify-content: center; gap: 8px; padding: 10px 20px; min-width: 140px; height: 44px;   background: var(--de-bg); color: var(--de-text); border: 1px solid var(--de-border); transition: var(--de-transition); font-weight: 500; text-decoration: none; font-size: 0.85rem;">
                                            <i class="fas fa-arrow-left"></i> Back
                                        </a>
                                    @elseif($restaurantSlug)
                                        <a href="{{ route('restaurant.orders.index', ['restaurant' => $restaurantSlug]) }}"
                                            class="btn"
                                            style="display: inline-flex; align-items: center; justify-content: center; gap: 8px; padding: 10px 20px; min-width: 140px; height: 44px;   background: var(--de-bg); color: var(--de-text); border: 1px solid var(--de-border); transition: var(--de-transition); font-weight: 500; text-decoration: none; font-size: 0.85rem;">
                                            <i class="fas fa-arrow-left"></i> Back
                                        </a>
                                    @endif

                                    {{-- Mark Prepared (Chef) --}}
                                    @if ($order->status == 'pending' && auth()->user()->role == 'chef')
                                        <form method="POST"
                                            action="{{ route('restaurant.orders.prepare', ['restaurant' => $restaurant->slug, 'order' => $order->id]) }}"
                                            class="swal-confirm" data-title="Mark this order as Prepared?"
                                            data-text="Are you sure you want to mark this order as prepared?"
                                            style="display: inline;">
                                            @csrf

                                            <button class="btn"
                                                style="display: inline-flex; align-items: center; justify-content: center; gap: 8px; padding: 10px 20px; min-width: 140px; height: 44px;   background: #3498db; color: #fff; border: none; font-weight: 600; box-shadow: 0 4px 16px rgba(52, 152, 219, 0.3); transition: var(--de-transition); cursor: pointer; font-size: 0.85rem;">
                                                <i class="fas fa-check-circle"></i> Mark Prepared
                                            </button>
                                        </form>
                                    @endif

                                    {{-- Mark as Delivered (Waiter) --}}
                                    @if ($order->status == 'prepared' && in_array(auth()->user()->role, ['waiter', 'waiter_head']))
                                        <form method="POST"
                                            action="{{ route('restaurant.orders.delivered', ['restaurant' => $restaurant->slug, 'order' => $order->id]) }}"
                                            class="swal-confirm" data-title="Mark this order as Delivered?"
                                            data-text="Are you sure you want to mark this order as delivered?"
                                            style="display: inline;">
                                            @csrf

                                            <button class="btn"
                                                style="display: inline-flex; align-items: center; justify-content: center; gap: 8px; padding: 10px 20px; min-width: 140px; height: 44px;   background: #2ecc71; color: #fff; border: none; font-weight: 600; box-shadow: 0 4px 16px rgba(46, 204, 113, 0.3); transition: var(--de-transition); cursor: pointer; font-size: 0.85rem;">
                                                <i class="fas fa-check-circle"></i> Deliver
                                            </button>
                                        </form>
                                    @endif

                                </div>
                            </div>

                        </div>

                    </div>
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
