@extends('layouts.app')

@section('content')
    <div class="section">
        <div class="card">
            <div class="card-header">
                <h4>Order Details</h4>
            </div>

            <div class="card-body">

                <div class="row mb-4">
                    <div class="col-md-4">
                        <strong>Order ID:</strong>
                        #{{ $order->id }}
                    </div>

                    <div class="col-md-4">
                        <strong>Token No:</strong>
                        {{ $order->token_no }}
                    </div>

                    <div class="col-md-4">
                        <strong>Status:</strong>


                        @if ($order->status == 'pending')
                            <span class="badge bg-warning text-dark">
                                Pending
                            </span>
                        @elseif($order->status == 'preparing')
                            <span class="badge bg-info">
                                Preparing
                            </span>
                        @endif
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6">
                        <strong>Customer:</strong><br>
                        {{ $order->customer->name ?? 'Walk In Customer' }}
                    </div>

                    <div class="col-md-6">
                        <strong>Table:</strong><br>
                        {{ $order->table->name ?? 'Take Away' }}
                    </div>
                </div>

                <hr>

                <h5>Order Items</h5>

                <div class="table-responsive">
                    <table class="table table-bordered">

                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Item</th>
                                <th>Qty</th>
                                <th>Price</th>
                                <th>Total</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($order->items as $item)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>

                                    <td>
                                        {{ $item->menuItem->name ?? '-' }}
                                    </td>

                                    <td>{{ $item->quantity }}</td>

                                    <td>₹{{ number_format($item->price, 2) }}</td>

                                    <td>
                                        ₹{{ number_format($item->quantity * $item->price, 2) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>

                    </table>
                </div>

                @php
                    $grandTotal = $order->items->sum('subtotal');
                @endphp

                <div class="text-right mt-4">
                    <h4>
                        Grand Total :
                        ₹{{ number_format($grandTotal, 2) }}
                    </h4>
                </div>

                <div class="mt-4 d-flex gap-2 align-items-center">

                    <a href="{{ route('restaurant.orders.index', ['restaurant' => $restaurant->slug]) }}"
                        class="btn btn-primary">
                        Back To Orders
                    </a>

                    @if ($order->status == 'pending')
                        <form method="POST"
                            action="{{ route('restaurant.orders.prepare', ['restaurant' => $restaurant->slug, 'order' => $order->id]) }}"
                            class="m-0">
                            @csrf

                            <button class="btn btn-success">
                                Mark as Preparing
                            </button>
                        </form>
                    @endif

                </div>
            </div>
        </div>
    </div>
@endsection
