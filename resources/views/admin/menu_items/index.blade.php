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
                            Menu Management
                        </span>
                        <h1>Menu Items</h1>
                        <p>Manage restaurant menu items and keep your menu up to date.</p>
                    </div>
                </div>
                @php
                    $restaurantSlug = request()->route('restaurant');
                    $branchSlug = request()->route('branch');
                @endphp
                <div class="header-right">
                    @if (auth()->user()->role === 'super_admin')
                        <a href="{{ route('menu-items.create') }}" class="premium-back-btn">
                            <i class="fas fa-plus"></i>
                            Add Menu Item
                        </a>
                    @elseif (!empty($restaurantSlug) && !empty($branchSlug))
                                    <a href="{{ route('branch.menu-items.create', [
                            'restaurant' => $restaurantSlug,
                            'branch' => $branchSlug,
                        ]) }}" class="premium-back-btn">
                                        <i class="fas fa-plus"></i>
                                        Add Menu Item
                                    </a>
                    @elseif (!empty($restaurantSlug))
                                    <a href="{{ route('restaurant.menu-items.create', [
                            'restaurant' => $restaurantSlug,
                        ]) }}" class="premium-back-btn">
                                        <i class="fas fa-plus"></i>
                                        Add Menu Item
                                    </a>
                    @endif
                </div>
            </div>
        </div>
    </section>
    <section class="section premium-dashboard pt-0">
        <div class="card premium-block">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle" id="tableExport">
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
                                    <td>
                                        {{ $item->branch?->country?->currency_symbol ?? '₹' }}
                                        {{ number_format($item->price, 2) }}
                                    </td>
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
                                                ]) }}" class="btn btn-md btn-warning">
                                                                                <i class="fas fa-edit"></i>
                                                                            </a>
                                            @elseif(!empty($restaurantSlug))
                                                                            <a href="{{ route('restaurant.menu-items.edit', [
                                                    'restaurant' => $restaurantSlug,
                                                    'menu_item' => $item->id,
                                                ]) }}" class="btn btn-md btn-warning">
                                                                                <i class="fas fa-edit"></i>
                                                                            </a>
                                            @endif



                                            {{-- DELETE --}}
                                            @if (!empty($restaurantSlug) && !empty($branchSlug))
                                                                            <form id="delete-form-{{ $item->id }}" method="POST" action="{{ route('branch.menu-items.destroy', [
                                                    'restaurant' => $restaurantSlug,
                                                    'branch' => $branchSlug,
                                                    'menu_item' => $item->id,
                                                ]) }}" style="display:inline" class="delete-form btn-md">
                                            @elseif(!empty($restaurantSlug))
                                                                                    <form id="delete-form-{{ $item->id }}" method="POST" action="{{ route('restaurant.menu-items.destroy', [
                                                        'restaurant' => $restaurantSlug,
                                                        'menu_item' => $item->id,
                                                    ]) }}" style="display:inline" class="delete-form btn-md">
                                                @endif

                                                    @csrf
                                                    @method('DELETE')

                                                    <button type="submit" class="btn btn-md btn-danger">

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
    <script>
        document.addEventListener('DOMContentLoaded', function () {

            $('.add-recipe-btn').click(function () {

                let menuId = $(this).data('menu-id');
                let menuName = $(this).data('menu-name');

                $('#recipe_menu_item_id').val(menuId);
                $('#recipe_menu_item_name').val(menuName);

            });

        });
    </script>
@endsection
