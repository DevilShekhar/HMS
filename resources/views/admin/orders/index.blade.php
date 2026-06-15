@can('view-order')
    @extends('layouts.app')
    @section('content')
        <section class="section premium-dashboard">
            <div class="premium-page-head">
                <div class="premium-page-title">
                    <span class="mini-badge"> Order Management </span>
                    <h2>Orders</h2>
                    <p> Manage restaurant orders and track status. </p>
                </div>
                <div class="premium-head-actions">
                    <a href="{{ route('restaurant.orders.create', ['restaurant' => $restaurant->slug]) }}"
                        class="btn btn-primary">
                        <i class="fas fa-plus"></i> Create Order
                    </a>
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
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Token No</th>
                                    <th>Customer</th>
                                    <th>Mobile</th>
                                    <th>Table No</th>
                                    <th>Order Type</th>
                                    <th>Status</th>
                                    <th>Total</th>
                                    <th>assign</th>
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
                                        <td>
                                            @if ($order->status == 'pending')
                                                <span class="badge bg-warning text-dark">
                                                    Pending
                                                </span>
                                            @elseif($order->status == 'preparing')
                                                <span class="badge bg-info">
                                                    Preparing
                                                </span>
                                            @elseif($order->status == 'completed')
                                                <span class="badge bg-success">
                                                    Completed
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
                                        <td> ₹{{ number_format($order->total, 2) }} </td>
                                        <td>{{ $order->chef?->name ?? 'Not Assigned' }}</td>
                                        <td>
                                            <div class="d-flex gap-2">
                                                <a href="{{ route('restaurant.orders.show', [$restaurant->slug, $order->id]) }}"
                                                    class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('restaurant.orders.edit', [$restaurant->slug, $order->id]) }}"
                                                    class="btn btn-sm btn-warning">
                                                    <i class="fas fa-edit"></i>
                                                </a>
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
    @endsection
@else
    @php
        abort(403);
    @endphp
@endcan
