@extends('layouts.app')

@section('content')
    <div class="section">
        <div class="card">
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

            <div class="card-body">

                <!-- Order Info -->
                <div class="row mb-2">
                    <div class="col-md-2">
                        <strong>Order ID:</strong><br>
                        {{ $order->id }}
                    </div>
                    <div class="col-md-2">
                        <strong>Token No:</strong><br>
                        <span class="fw-bold">{{ $order->token_no }}</span>
                    </div>
                    <div class="col-md-2">
                        <strong>Table No:</strong><br>
                        {{ $order->table_no ?? ($order->table?->name ?? 'Take Away') }}
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-2">
                        <strong>Customer Name:</strong><br>
                        {{ $order->customer->name ?? ($order->customer_name ?? 'Walk-in Customer') }}
                    </div>
                    <div class="col-md-2">
                        <strong>Mobile:</strong><br>
                        {{ $order->mobile_number ?? 'N/A' }}
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6">
                        <strong>Order Date:</strong><br>
                        {{ $order->created_at->format('d M Y, h:i A') }}
                    </div>
                </div>

                <hr>

                <!-- Order Items -->
                <h5 class="mb-3">Order Items</h5>

                <div class="table-responsive">
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
                                    <td class="text-center">{{ $item->quantity }}</td>
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
                        <a href="{{ route('orders.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Orders
                        </a>
                    @elseif($branchSlug)
                        <a href="{{ route('branch.orders.index', ['restaurant' => $restaurantSlug, 'branch' => $branchSlug]) }}"
                            class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Orders
                        </a>
                    @elseif($restaurantSlug)
                        <a href="{{ route('restaurant.orders.index', ['restaurant' => $restaurantSlug]) }}"
                            class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Orders
                        </a>
                    @endif

                    <!-- Status Actions -->
                    @if ($order->status == 'pending' && auth()->user()->role == 'chef')
                        <form method="POST"
                            action="{{ route('restaurant.orders.prepare', ['restaurant' => $restaurant->slug, 'order' => $order->id]) }}"
                            class="m-0">
                            @csrf
                            <button type="submit" class="btn btn-success">
                                Mark as Prepared
                            </button>
                        </form>
                    @endif
                    @if ($order->status == 'prepared' && in_array(auth()->user()->role, ['waiter', 'waiter-head']))
                        <form method="POST"
                            action="{{ route('restaurant.orders.delivered', ['restaurant' => $restaurant->slug, 'order' => $order->id]) }}"
                            class="m-0">
                            @csrf
                            <button type="submit" class="btn btn-primary">
                                Mark as Delivered
                            </button>
                        </form>
                    @endif
                    @if ($order->status == 'delivered' && auth()->user()->role == ['waiter', 'waiter-head', 'owner'])
                        <form method="POST"
                            action="{{ route('restaurant.orders.completed', ['restaurant' => $restaurant->slug, 'order' => $order->id]) }}"
                            class="m-0">
                            @csrf
                            <button type="submit" class="btn btn-info">
                                Mark as Completed
                            </button>
                        </form>
                    @endif
                </div>

            </div>
        </div>
    </div>
@endsection
