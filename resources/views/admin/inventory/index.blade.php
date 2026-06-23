@extends('layouts.app')
@section('content')
    <section class="section premium-dashboard">
        <div class="premium-page-head">
            <div class="premium-page-title">
                <span class="mini-badge">
                    Inventory Management
                </span>
                <h2>Inventory Items</h2>
                <p>
                    Manage restaurant inventory stock.
                </p>
            </div>
            @php
                $restaurantSlug = request()->route('restaurant');
                $branchSlug = request()->route('branch');
            @endphp

            <div class="premium-head-actions">
                @can('create-inventory')
                    @if ($branchSlug)
                        <a href="{{ route('branch.inventory.create', [
                            'restaurant' => $restaurantSlug,
                            'branch' => $branchSlug,
                        ]) }}"
                            class="btn btn-primary">
                            <i class="fas fa-plus"></i>
                            Add Inventory Item
                        </a>
                    @else
                        <a href="{{ route('restaurant.inventory.create', [
                            'restaurant' => $restaurantSlug,
                        ]) }}"
                            class="btn btn-primary">
                            <i class="fas fa-plus"></i>
                            Add Inventory Item
                        </a>
                    @endif
                @endcan
            </div>
        </div>
    </section>
    <section class="section premium-dashboard pt-0">
        <div class="card premium-block">
            <div class="card-header premium-card-header">
                <h4>Inventory List</h4>
            </div>
            <div class="card-body">
                @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif
                <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Branch</th>
                                <th>Item Name</th>
                                <th>Total Stock</th>
                                <th>Remaining Stock</th>
                                <th>Minimum Stock</th>
                                <th>Status</th>
                                <th>Is Active</th>
                                <th>Created By</th>
                                <th>Updated By</th>
                                <th width="150">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($items as $item)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $item->branch->name ?? '-' }}</td>
                                    <td>{{ ucfirst($item->name) }}</td>
                                    <td>
                                        {{ (float) $item->total_stock }}
                                        {{ ucfirst($item->unit) }}
                                    </td>
                                    <td>
                                        @if ($item->remaining_stock <= $item->minimum_stock)
                                            <span class="badge bg-danger">
                                                {{ (float) $item->remaining_stock }}
                                                {{ ucfirst($item->unit) }}
                                            </span>
                                        @else
                                            <span class="badge bg-success">
                                                {{ (float) $item->remaining_stock }}
                                                {{ ucfirst($item->unit) }}
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        {{ (float) $item->minimum_stock }}
                                        {{ ucfirst($item->unit) }}
                                    </td>
                                    <td>
                                        @if ($item->remaining_stock == 0)
                                            <span class="badge bg-danger">
                                                Out Of Stock
                                            </span>
                                        @elseif($item->remaining_stock <= $item->minimum_stock)
                                            <span class="badge bg-warning text-dark">
                                                Low Stock
                                            </span>
                                        @else
                                            <span class="badge bg-success">
                                                In Stock
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($item->is_active)
                                            <span class="badge bg-success">
                                                Active
                                            </span>
                                        @else
                                            <span class="badge bg-danger">
                                                Inactive
                                            </span>
                                        @endif
                                    </td>
                                    <td>{{ $item->creator->name ?? '-' }}</td>
                                    <td>{{ $item->updater->name ?? '-' }}</td>
                                    <td>

                                        @if (request()->route('branch'))
                                            {{-- Branch Stock In --}}
                                            <a href="{{ route('restaurant.inventory.stock-in', [
                                                'restaurant' => request()->route('restaurant'),
                                                'branch' => request()->route('branch'),
                                                'inventory' => $item->id,
                                            ]) }}"
                                                class="btn btn-success btn-sm">
                                                <i class="fas fa-plus-circle"></i>
                                            </a>

                                            {{-- Branch Transactions --}}
                                            <a href="{{ route('restaurant.inventory.transactions', [
                                                'restaurant' => request()->route('restaurant'),
                                                'branch' => request()->route('branch'),
                                                'inventory' => $item->id,
                                            ]) }}"
                                                class="btn btn-secondary btn-sm">
                                                <i class="fas fa-history"></i>
                                            </a>


                                            {{-- Branch Edit --}}
                                            <a href="{{ route('branch.inventory.edit', [
                                                'restaurant' => request()->route('restaurant'),
                                                'branch' => request()->route('branch'),
                                                'inventory' => $item->id,
                                            ]) }}"
                                                class="btn btn-warning btn-sm">
                                                <i class="fas fa-edit"></i>
                                            </a>


                                            {{-- Branch Delete --}}
                                            <form
                                                action="{{ route('branch.inventory.destroy', [
                                                    'restaurant' => request()->route('restaurant'),
                                                    'branch' => request()->route('branch'),
                                                    'inventory' => $item->id,
                                                ]) }}"
                                                method="POST" class="d-inline delete-form">

                                                @csrf
                                                @method('DELETE')

                                                <button type="submit" class="btn btn-danger btn-sm">
                                                    <i class="fas fa-trash"></i>
                                                </button>

                                            </form>
                                        @else
                                            {{-- Restaurant Stock In --}}
                                            <a href="{{ route('restaurant.inventory.stock-in', [
                                                'restaurant' => request()->route('restaurant'),
                                                'inventory' => $item->id,
                                            ]) }}"
                                                class="btn btn-success btn-sm">
                                                <i class="fas fa-plus-circle"></i>
                                            </a>


                                            {{-- Restaurant Transactions --}}
                                            <a href="{{ route('restaurant.inventory.transactions', [
                                                'restaurant' => request()->route('restaurant'),
                                                'inventory' => $item->id,
                                            ]) }}"
                                                class="btn btn-secondary btn-sm">
                                                <i class="fas fa-history"></i>
                                            </a>


                                            {{-- Restaurant Edit --}}
                                            <a href="{{ route('restaurant.inventory.edit', [
                                                'restaurant' => request()->route('restaurant'),
                                                'inventory' => $item->id,
                                            ]) }}"
                                                class="btn btn-warning btn-sm">
                                                <i class="fas fa-edit"></i>
                                            </a>


                                            {{-- Restaurant Delete --}}
                                            <form
                                                action="{{ route('restaurant.inventory.destroy', [
                                                    'restaurant' => request()->route('restaurant'),
                                                    'inventory' => $item->id,
                                                ]) }}"
                                                method="POST" class="d-inline delete-form">

                                                @csrf
                                                @method('DELETE')

                                                <button type="submit" class="btn btn-danger btn-sm">
                                                    <i class="fas fa-trash"></i>
                                                </button>

                                            </form>
                                        @endif

                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center">
                                        No Inventory Items Found
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    {{ $items->links() }}
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
