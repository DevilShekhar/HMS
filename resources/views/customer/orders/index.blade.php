@extends('layouts.app')

@section('content')
    <section class="section premium-dashboard">
        <div class="premium-page-head">
            <div class="premium-page-title">
                <span class="mini-badge">Customer Orders</span>
                <h2>My Order History</h2>
                <p>
                    View your previous orders.
                </p>
            </div>
        </div>
    </section>

    <section class="section premium-dashboard pt-0">

        <div class="card premium-block">

            <div class="card-header premium-card-header">
                <div>
                    <h4>Order History</h4>
                    <p class="header-subtext">
                        Your previous orders from this restaurant.
                    </p>
                </div>
            </div>

            <div class="card-body">

                <div class="table-responsive">

                    <table class="table table-hover align-middle">

                        <thead>
                            <tr>
                                <th>SrNo.</th>
                                <th>Previous Token</th>
                                <th>Date</th>
                                <th>Items</th>
                                <th>Status</th>
                                <th>Total</th>
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
                                        {{ $order->created_at->format('d M Y') }}
                                    </td>

                                    <td>
                                        <ul class="mb-0 ps-3">
                                            @foreach ($order->items as $item)
                                                <li>
                                                    {{ $item->menuItem->name }}
                                                    <span class="text-muted">
                                                        x {{ $item->quantity }}
                                                    </span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </td>

                                    <td>

                                        @if ($order->status == 'pending')
                                            <span class="badge bg-warning text-dark">
                                                Pending
                                            </span>
                                        @elseif($order->status == 'prepared')
                                            <span class="badge bg-info">
                                                Prepared
                                            </span>
                                        @elseif($order->status == 'delivered')
                                            <span class="badge bg-primary">
                                                Delivered
                                            </span>
                                        @elseif($order->status == 'completed')
                                            <span class="badge bg-success">
                                                Completed
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">
                                                {{ ucfirst($order->status) }}
                                            </span>
                                        @endif

                                    </td>

                                    <td>
                                        <strong>
                                            ₹{{ number_format($order->total, 2) }}
                                        </strong>
                                    </td>

                                </tr>

                            @empty

                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        <div class="text-muted">
                                            No Order History Found
                                        </div>
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
