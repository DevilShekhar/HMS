@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        Order #{{ $order->token_no }}
                    </h4>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                           <div class="border rounded p-3">
                                <small class="text-muted">
                                   Customer
                                </small>
                                <h6 class="mb-0">{{ $order->customer_name }}</h6>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="border rounded p-3">
                                <small class="text-muted">
                                    Mobile
                                </small>
                                <h6 class="mb-0">
                                    {{ $order->mobile_number }}
                                </h6>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="border rounded p-3">
                                <small class="text-muted">
                                    Table
                                </small>
                                <h6 class="mb-0">
                                    {{ $order->table_no }}
                                </h6>
                            </div>
                        </div>
                    </div>
                    <div class="row g-3 mt-2">
                        <div class="col-md-4">
                            <div class="border rounded p-3">
                                <small class="text-muted">
                                    Branch
                                </small>
                                <h6 class="mb-0">
                                    {{ $order->branch?->name }}
                                </h6>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="border rounded p-3">
                                <small class="text-muted">
                                    Created By
                                </small>
                                <h6 class="mb-0">
                                    {{ $order->creator?->name }}
                                </h6>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="border rounded p-3">
                                <small class="text-muted">
                                    Order Type
                                </small>
                                <h6 class="mb-0">
                                    {{ ucfirst($order->order_type) }}
                                </h6>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <h5 class="mb-3"> Order Items  </h5>
                    <table class="table table-striped align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Item</th>
                                <th width="120">Price</th>
                                <th width="100">Qty</th>
                                <th width="150">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->items as $item)
                            <tr>
                                <td> {{ $item->menuItem?->name }} </td>
                                <td> ₹{{ number_format($item->price,2) }} </td>
                                <td>  {{ $item->quantity }} </td>
                                <td> ₹{{ number_format($item->subtotal,2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="3" class="text-end">
                                    Grand Total
                                </th>
                                <th>
                                    ₹{{ number_format($order->total,2) }}
                                </th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 mb-3">
                <div class="card-header">
                    <h5 class="mb-0">
                        Kitchen Status
                    </h5>
                </div>
                <div class="card-body text-center">
                    <h4>
                        @if($order->kitchen_status == 'accepted')
                            <span class="badge bg-primary">
                                Accepted
                            </span>
                        @elseif($order->kitchen_status == 'preparing')
                            <span class="badge bg-warning text-dark">
                                Preparing
                            </span>
                        @elseif($order->kitchen_status == 'ready')
                            <span class="badge bg-success">
                                Ready
                            </span>
                        @elseif($order->kitchen_status == 'completed')
                            <span class="badge bg-dark">
                                Completed
                            </span>
                        @else
                            <span class="badge bg-secondary">
                                Pending
                            </span>
                        @endif
                    </h4>
                </div>
            </div>
            @if(auth()->user()->role == 'chef')
            <div class="card shadow-sm border-0">
                <div class="card-header">
                    <h5 class="mb-0">
                        Update Kitchen Status
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('restaurant.orders.kitchen-status', [$restaurant->slug, $order->id]) }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">  Status </label>
                            <select name="kitchen_status" class="form-select">
                                <option value="accepted">  Accepted </option>
                                <option value="preparing">  Preparing </option>
                                <option value="ready"> Ready</option>
                                <option value="completed"> Completed </option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-success w-100"> Update Status </button>
                    </form>
                </div>
            </div>
            @endif
            <div class="mt-3 d-grid gap-2">
                <a href="{{ route('restaurant.orders.index', $restaurant->slug) }}" class="btn btn-secondary">
                    Back
                </a>
                @if(auth()->user()->role != 'chef')
                <a href="{{ route('restaurant.orders.edit',[$restaurant->slug, $order->id]) }}" class="btn btn-warning">
                    Edit Order
                </a>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection