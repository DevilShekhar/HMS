@extends('layouts.app')
@section('content')
    <section class="section premium-dashboard">
        <div class="premium-floating-header">
            <div class="header-content">
                <div class="header-left">
                    <div class="header-icon">
                        <i class="fas fa-history"></i>
                    </div>
                    <div>
                        <span class="header-badge">
                            Inventory Management
                        </span>
                        <h1>{{ $inventory->name }} Transactions</h1>
                        <p>Inventory Stock IN / OUT History</p>
                    </div>
                </div>

                <div class="header-right">
                    <a href="{{ route('restaurant.inventory.index', ['restaurant' => request()->route('restaurant')]) }}"
                        class="premium-back-btn">
                        <i class="fas fa-arrow-left"></i>
                        Back To Inventory
                    </a>
                </div>
            </div>
        </div>
    </section>
    <section class="section premium-dashboard pt-0">
        <div class="card premium-block">
            <div class="card-header premium-card-header">
                <div>
                    <h4>Transaction History</h4>
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-3">
                        <strong>Item:</strong>
                        {{ $inventory->name }}
                    </div>
                    <div class="col-md-3">
                        <strong>Total Stock:</strong>
                        {{ $inventory->total_stock }}
                        {{ ucfirst($inventory->unit) }}
                    </div>
                    <div class="col-md-3">
                        <strong>Remaining:</strong>
                        {{ $inventory->remaining_stock }}
                        {{ ucfirst($inventory->unit) }}
                    </div>
                    <div class="col-md-3">
                        <strong>Minimum:</strong>
                        {{ $inventory->minimum_stock }}
                        {{ ucfirst($inventory->unit) }}
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Date</th>
                                <th>Type</th>
                                <th>Quantity</th>
                                <th>Remarks</th>
                                <th>Created By</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transactions as $transaction)
                                <tr>
                                    <td> {{ $loop->iteration }} </td>
                                    <td> {{ $transaction->created_at->format('d M Y h:i A') }} </td>
                                    <td>
                                        @if($transaction->type == 'in')
                                            <span class="badge bg-success">
                                                STOCK IN
                                            </span>
                                        @else
                                            <span class="badge bg-danger">
                                                STOCK OUT
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $transaction->quantity }}
                                        {{ ucfirst($inventory->unit) }}
                                    </td>
                                    <td>{{ $transaction->remarks ?? '-' }}</td>
                                    <td> {{ $transaction->creator->name ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">
                                        No Transactions Found
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