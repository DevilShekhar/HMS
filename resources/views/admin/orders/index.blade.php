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
                @php
                    $restaurantSlug = request()->route('restaurant');
                    $branchSlug = request()->route('branch');
                @endphp

                <div class="premium-head-actions">

                    @can('create-order')
                    @if ($branchSlug)
                        <a href="{{ route('branch.orders.create', [
                            'restaurant' => $restaurantSlug,
                            'branch' => $branchSlug,
                        ]) }}"
                            class="btn btn-primary">
                            <i class="fas fa-plus"></i>
                            Create Order
                        </a>
                    @else
                        <a href="{{ route('restaurant.orders.create', [
                            'restaurant' => $restaurantSlug,
                        ]) }}"
                            class="btn btn-primary">
                            <i class="fas fa-plus"></i>
                            Create Order
                        </a>
                    @endif
                    @endcan
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
                                                @if (!empty($restaurantSlug) && !empty($branchSlug))
                                                    {{-- Branch View --}}
                                                    <a href="{{ route('branch.orders.show', [
                                                        'restaurant' => $restaurantSlug,
                                                        'branch' => $branchSlug,
                                                        'order' => $order->id,
                                                    ]) }}"
                                                        class="btn btn-sm btn-info">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                @elseif(!empty($restaurantSlug))
                                                    {{-- Restaurant View --}}
                                                    <a href="{{ route('restaurant.orders.show', [
                                                        'restaurant' => $restaurantSlug,
                                                        'order' => $order->id,
                                                    ]) }}"
                                                        class="btn btn-sm btn-info">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                @else
                                                    {{-- Super Admin View --}}
                                                    <a href="{{ route('orders.show', [
                                                        'order' => $order->id,
                                                    ]) }}"
                                                        class="btn btn-sm btn-info">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                @endif
                                                @php
                                                    $restaurantSlug = request()->route('restaurant');
                                                    $branchSlug = request()->route('branch');
                                                @endphp

                                                {{-- Edit --}}
                                                @can('edit-order')
                                                @if (!empty($restaurantSlug) && !empty($branchSlug))
                                                    <a href="{{ route('branch.orders.edit', [
                                                        'restaurant' => $restaurantSlug,
                                                        'branch' => $branchSlug,
                                                        'order' => $order->id,
                                                    ]) }}"
                                                        class="btn btn-warning btn-sm">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                @elseif(!empty($restaurantSlug))
                                                    <a href="{{ route('restaurant.orders.edit', [
                                                        'restaurant' => $restaurantSlug,
                                                        'order' => $order->id,
                                                    ]) }}"
                                                        class="btn btn-warning btn-sm">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                @else
                                                    <a href="{{ route('orders.edit', [
                                                        'order' => $order->id,
                                                    ]) }}"
                                                        class="btn btn-warning btn-sm">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                @endif
                                                @endcan
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
    @if (session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
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
        document.addEventListener('DOMContentLoaded', function() {

            document.querySelectorAll('.delete-form').forEach(form => {

                form.addEventListener('submit', function(e) {

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
    </script>
@else
    @php
        abort(403);
    @endphp
@endcan
