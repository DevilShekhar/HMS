@extends('layouts.app')
@section('content')
    <section class="section premium-dashboard">
        <div class="premium-page-head">
            <div class="premium-page-title">
                <span class="mini-badge">Menu Management</span>
                <h2>Menu Items</h2>
                <p>Manage restaurant menu items.</p>
            </div>
            @php
                $restaurantSlug = request()->route('restaurant');
                $branchSlug = request()->route('branch');
            @endphp

            <div class="premium-head-actions">

                @if (auth()->user()->role === 'super_admin')
                    <a href="{{ route('menu-items.create') }}" class="btn premium-btn">
                        <i class="fas fa-plus"></i>
                        Add Menu Items
                    </a>
                @elseif (!empty($restaurantSlug) && !empty($branchSlug))
                    <a href="{{ route('branch.menu-items.create', [
                        'restaurant' => $restaurantSlug,
                        'branch' => $branchSlug,
                    ]) }}"
                        class="btn premium-btn">
                        <i class="fas fa-plus"></i>
                        Add Menu Items
                    </a>
                @elseif (!empty($restaurantSlug))
                    <a href="{{ route('restaurant.menu-items.create', [
                        'restaurant' => $restaurantSlug,
                    ]) }}"
                        class="btn premium-btn">
                        <i class="fas fa-plus"></i>
                        Add Menu Items
                    </a>
                @endif

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
                    <h4>Menu Items List</h4>
                    <p class="header-subtext">
                        All menu items for this restaurant.
                    </p>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Image</th>
                                <th>Menu Name</th>
                                <th>Branch</th>
                                <th>Category</th>
                                <th>Price</th>
                                <th>Food Type</th>
                                <th>Status</th>
                                <th width="180">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($menuItems as $key => $item)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>
                                        @if ($item->image)
                                            <img src="{{ asset($item->image) }}" width="60" class="rounded">
                                        @else
                                            <span class="badge bg-secondary"> No Image </span>
                                        @endif
                                    </td>
                                    <td><strong>{{ $item->name }}</strong> </td>
                                    <td> {{ $item->branch->name ?? '-' }} </td>
                                    <td> {{ $item->category->name ?? '-' }} </td>
                                    <td> ₹{{ number_format($item->price, 2) }} </td>
                                    <td>
                                        @if ($item->food_type == 'veg')
                                            <span class="badge bg-success">
                                                Veg
                                            </span>
                                        @else
                                            <span class="badge bg-danger">
                                                Non Veg
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($item->is_active)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-danger">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            @php
                                                $restaurantSlug = request()->route('restaurant');
                                                $branchSlug = request()->route('branch');
                                            @endphp


                                            {{-- EDIT --}}
                                            @if (!empty($restaurantSlug) && !empty($branchSlug))
                                                <a href="{{ route('branch.menu-items.edit', [
                                                    'restaurant' => $restaurantSlug,
                                                    'branch' => $branchSlug,
                                                    'menu_item' => $item->id,
                                                ]) }}"
                                                    class="btn btn-sm btn-warning">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            @elseif(!empty($restaurantSlug))
                                                <a href="{{ route('restaurant.menu-items.edit', [
                                                    'restaurant' => $restaurantSlug,
                                                    'menu_item' => $item->id,
                                                ]) }}"
                                                    class="btn btn-sm btn-warning">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            @endif



                                            {{-- DELETE --}}
                                            @if (!empty($restaurantSlug) && !empty($branchSlug))
                                                <form id="delete-form-{{ $item->id }}" method="POST"
                                                    action="{{ route('branch.menu-items.destroy', [
                                                        'restaurant' => $restaurantSlug,
                                                        'branch' => $branchSlug,
                                                        'menu_item' => $item->id,
                                                    ]) }}"
                                                    style="display:inline" class="delete-form">
                                                @elseif(!empty($restaurantSlug))
                                                    <form id="delete-form-{{ $item->id }}" method="POST"
                                                        action="{{ route('restaurant.menu-items.destroy', [
                                                            'restaurant' => $restaurantSlug,
                                                            'menu_item' => $item->id,
                                                        ]) }}"
                                                        style="display:inline" class="delete-form">
                                            @endif

                                            @csrf
                                            @method('DELETE')

                                            <button type="submit" class="btn btn-sm btn-danger">

                                                <i class="fas fa-trash"></i>

                                            </button>

                                            </form>

                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center">
                                        No Menu Items Found
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
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
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            $('.add-recipe-btn').click(function() {

                let menuId = $(this).data('menu-id');
                let menuName = $(this).data('menu-name');

                $('#recipe_menu_item_id').val(menuId);
                $('#recipe_menu_item_name').val(menuName);

            });

        });
    </script>
@endsection
