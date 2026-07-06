@can('view-inventory')
    @extends('layouts.app')
    @section('content')
        <section class="section premium-dashboard">
            <div class="premium-floating-header">
                <div class="header-content">
                    <div class="header-left">
                        <div class="header-icon">
                            <i class="fas fa-clipboard-list"></i>
                        </div>
                        <div>
                            <span class="header-badge">
                                Inventory Management
                            </span>
                            <h1>Inventory Items</h1>
                            <p>Manage restaurant inventory stock.</p>
                        </div>
                    </div>
                    @php
                        $restaurantSlug = request()->route('restaurant');
                        $branchSlug = request()->route('branch');
                    @endphp

                    <div class="header-right">
                        @can('create-inventory')
                            @if ($branchSlug)
                                    <a href="{{ route('branch.inventory.create', [
                                    'restaurant' => $restaurantSlug,
                                    'branch' => $branchSlug,
                                ]) }}" class="premium-back-btn">
                                        <i class="fas fa-plus"></i>
                                        Add Inventory Item
                                    </a>
                            @else
                                    <a href="{{ route('restaurant.inventory.create', [
                                    'restaurant' => $restaurantSlug,
                                ]) }}" class="premium-back-btn">
                                        <i class="fas fa-plus"></i>
                                        Add Inventory Item
                                    </a>
                            @endif
                        @endcan
                    </div>
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
                        <table class="table table-bordered align-middle" id="tableExport">
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
                                                <span class="status inactive">
                                                    {{ (float) $item->remaining_stock }}
                                                    {{ ucfirst($item->unit) }}
                                                </span>
                                            @else
                                                <span class="status active">
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
                                                <span class="status inactive">
                                                    Out Of Stock
                                                </span>
                                            @elseif($item->remaining_stock <= $item->minimum_stock)
                                                <span class="badge bg-warning text-dark">
                                                    Low Stock
                                                </span>
                                            @else
                                                <span class="status active">
                                                    In Stock
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($item->is_active)
                                                <span class="status active">
                                                    <i class="fas fa-circle"></i>Active
                                                </span>
                                            @else
                                                <span class="status inactive">
                                                    <i class="fas fa-circle"></i>Inactive
                                                </span>
                                            @endif
                                        </td>
                                        <td>{{ $item->creator->name ?? '-' }}</td>
                                        <td>{{ $item->updater->name ?? '-' }}</td>
                                        <td class="text-center">
                                            <div class="dropdown eht-action-dropdown">
                                                <button class="btn btn-sm btn-link text-secondary p-0" type="button"
                                                    id="dropdownMenuInventory{{ $item->id }}" data-bs-toggle="dropdown"
                                                    aria-expanded="false" style="width: 30px; height: 30px; line-height: 30px;">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </button>

                                                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0"
                                                    aria-labelledby="dropdownMenuInventory{{ $item->id }}"
                                                    style="border-radius: 8px; font-size: 0.85rem;">

                                                    @php
                                                        $restaurantSlug = request()->route('restaurant');
                                                        $branchSlug = request()->route('branch');
                                                    @endphp

                                                    @if ($branchSlug)
                                                         {{-- Branch Stock In --}}
                                                        <li>
                                                            <a class="dropdown-item py-2"
                                                                href="{{ route('branch.inventory.stock-in', ['restaurant' => $restaurantSlug, 'branch' => $branchSlug, 'inventory' => $item->id]) }}">
                                                                <i class="fas fa-plus-circle me-2"
                                                                    style="color: #28a745; width: 16px;"></i> Stock In
                                                            </a>
                                                        </li>

                                                        {{-- Branch Transactions --}}
                                                        <li>
                                                            <a class="dropdown-item py-2"
                                                                href="{{ route('branch.inventory.transactions', ['restaurant' => $restaurantSlug, 'branch' => $branchSlug, 'inventory' => $item->id]) }}">
                                                                <i class="fas fa-history me-2" style="color: #6c757d; width: 16px;"></i>
                                                                Transactions
                                                            </a>
                                                        </li>
                                                        {{-- Branch Edit --}}
                                                        <li>
                                                            <a class="dropdown-item py-2" href="{{ route('branch.inventory.edit', [
                                                                    'restaurant' => $restaurantSlug,
                                                                    'branch' => $branchSlug,
                                                                    'inventory' => $item->id,
                                                                ]) }}">
                                                                <i class="fas fa-edit me-2" style="color: #FA5603; width: 16px;"></i>
                                                                Edit Item
                                                            </a>
                                                        </li>

                                                        {{-- Divider line inside menu --}}
                                                        <li>
                                                            <hr class="dropdown-divider opacity-50">
                                                        </li>

                                                        {{-- Branch Delete --}}
                                                        <li>
                                                            <form
                                                                action="{{ route('branch.inventory.destroy', ['restaurant' => $restaurantSlug, 'branch' => $branchSlug, 'inventory' => $item->id]) }}"
                                                                method="POST" class="delete-form m-0">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="dropdown-item text-danger py-2">
                                                                    <i class="fas fa-trash me-2" style="width: 16px;"></i> Delete Item
                                                                </button>
                                                            </form>
                                                        </li>
                                                    @else
                                                        {{-- Restaurant Stock In --}}
                                                        <li>
                                                            <a class="dropdown-item py-2"
                                                                href="{{ route('restaurant.inventory.stock-in', ['restaurant' => $restaurantSlug, 'inventory' => $item->id]) }}">
                                                                <i class="fas fa-plus-circle me-2"
                                                                    style="color: #28a745; width: 16px;"></i> Stock In
                                                            </a>
                                                        </li>

                                                        {{-- Restaurant Transactions --}}
                                                        <li>
                                                            <a class="dropdown-item py-2"
                                                                href="{{ route('restaurant.inventory.transactions', ['restaurant' => $restaurantSlug, 'inventory' => $item->id]) }}">
                                                                <i class="fas fa-history me-2" style="color: #6c757d; width: 16px;"></i>
                                                                Transactions
                                                            </a>
                                                        </li>

                                                        {{-- Restaurant Edit --}}
                                                        <li>
                                                            <a class="dropdown-item py-2"
                                                                href="{{ route('restaurant.inventory.edit', ['restaurant' => $restaurantSlug, 'inventory' => $item->id]) }}">
                                                                <i class="fas fa-edit me-2" style="color: #FA5603; width: 16px;"></i>
                                                                Edit Item
                                                            </a>
                                                        </li>

                                                        {{-- Divider line inside menu --}}
                                                        <li>
                                                            <hr class="dropdown-divider opacity-50">
                                                        </li>

                                                        {{-- Restaurant Delete --}}
                                                        <li>
                                                            <form
                                                                action="{{ route('restaurant.inventory.destroy', ['restaurant' => $restaurantSlug, 'inventory' => $item->id]) }}"
                                                                method="POST" class="delete-form m-0">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="dropdown-item text-danger py-2">
                                                                    <i class="fas fa-trash me-2" style="width: 16px;"></i> Delete Item
                                                                </button>
                                                            </form>
                                                        </li>
                                                    @endif
                                                </ul>
                                            </div>
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
            document.addEventListener('DOMContentLoaded', function () {
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
        document.addEventListener('DOMContentLoaded', function () {

            document.querySelectorAll('.delete-form').forEach(form => {

                form.addEventListener('submit', function (e) {

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
