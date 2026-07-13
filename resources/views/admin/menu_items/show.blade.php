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
                        <span class="header-badge">Menu Management</span>
                        <h1>Menu Item Details</h1>
                        <p>View complete information of the menu item.</p>
                    </div>
                </div>

                @php
                    $restaurantSlug = request()->route('restaurant');
                    $branchSlug = request()->route('branch');
                @endphp

                <div class="premium-head-actions">
                    @if (auth()->user()->role === 'super_admin')
                        <a href="{{ route('menu-items.index') }}" class="premium-back-btn">
                            <i class="fas fa-arrow-left"></i> Back To Menu Items
                        </a>
                    @elseif(!empty($restaurantSlug) && !empty($branchSlug))
                        <a href="{{ route('branch.menu-items.index', ['restaurant' => $restaurantSlug, 'branch' => $branchSlug]) }}"
                            class="premium-back-btn">
                            <i class="fas fa-arrow-left"></i> Back To Menu Items
                        </a>
                    @elseif(!empty($restaurantSlug))
                        <a href="{{ route('restaurant.menu-items.index', ['restaurant' => $restaurantSlug]) }}"
                            class="premium-back-btn">
                            <i class="fas fa-arrow-left"></i> Back To Menu Items
                        </a>
                    @endif

                    @if (auth()->user()->hasRole('owner') || auth()->user()->hasRole('branch_manager'))

                    @if (!empty($restaurantSlug) && !empty($branchSlug))
                        <a href="{{ route('branch.menu-items.edit', [
                            'restaurant' => $restaurantSlug,
                            'branch' => $branchSlug,
                            'menu_item' => $menuItem->id,
                        ]) }}" class="premium-btn btn-primary">
                            <i class="fas fa-edit"></i> Edit Item
                        </a>
                    @elseif(!empty($restaurantSlug))
                        <a href="{{ route('restaurant.menu-items.edit', [
                            'restaurant' => $restaurantSlug,
                            'menu_item' => $menuItem->id,
                        ]) }}" class="premium-btn btn-primary">
                            <i class="fas fa-edit"></i> Edit Item
                        </a>
                    @else
                        <a href="{{ route('menu-items.edit', $menuItem->id) }}" class="premium-btn btn-primary">
                            <i class="fas fa-edit"></i> Edit Item
                        </a>
                    @endif

                @endif
                </div>
            </div>
        </div>
    </section>

    <section class="section premium-dashboard pt-0">
        <div class="premium-card">
            <div class="premium-card-body">

                <!-- Basic Information - 3 Column Grid -->
                <div class="row mb-5">
                    <div class="col-md-12">
                        <h5 class="premium-label mb-4">Basic Information</h5>
                    </div>

                    <div class="col-md-4 mb-4">
                        <div class="info-box">
                            <label class="premium-label">Branch</label>
                            <p class="info-value">{{ $menuItem->branch->name ?? 'N/A' }}</p>
                        </div>
                    </div>

                    <div class="col-md-4 mb-4">
                        <div class="info-box">
                            <label class="premium-label">Category</label>
                            <p class="info-value">{{ $menuItem->category->name ?? 'N/A' }}</p>
                        </div>
                    </div>

                    <div class="col-md-4 mb-4">
                        <div class="info-box">
                            <label class="premium-label">Menu Name</label>
                            <p class="info-value"><strong>{{ $menuItem->name }}</strong></p>
                        </div>
                    </div>

                    <div class="col-md-4 mb-4">
                        <div class="info-box">
                            <label class="premium-label">Price</label>
                            <p class="info-value"><strong>{{ number_format($menuItem->price, 2) }}</strong></p>
                        </div>
                    </div>

                    <div class="col-md-4 mb-4">
                        <div class="info-box">
                            <label class="premium-label">Food Type</label>
                            <p class="info-value">
                                @if($menuItem->food_type === 'veg')
                                    <span class="badge bg-success">Veg</span>
                                @else
                                    <span class="badge bg-danger">Non-Veg</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="col-md-4 mb-4">
                        <div class="info-box">
                            <label class="premium-label">Status</label>
                            <p class="info-value">
                                @if($menuItem->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">Inactive</span>
                                @endif
                                @if($menuItem->is_available)
                                    <span class="badge bg-info">Available</span>
                                @else
                                    <span class="badge bg-warning">Not Available</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Image -->
                <div class="row mb-5">
                    <div class="col-md-12">
                        <h5 class="premium-label mb-3">Item Image</h5>
                        <div class="upload-preview show-preview text-center">
                            @if($menuItem->image)
                                <img src="{{ asset($menuItem->image) }}" alt="{{ $menuItem->name }}"
                                    class="img-fluid rounded shadow-sm" style="max-height: 340px; object-fit: contain;">
                            @else
                                <img src="{{ asset('images/no-image.png') }}" alt="No Image" class="img-fluid rounded shadow-sm"
                                    style="max-height: 340px; object-fit: contain;">
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Description -->
                <div class="row">
                    <div class="col-md-12">
                        <h5 class="premium-label mb-3">Description</h5>
                        <div class="premium-description-box">
                            {!! $menuItem->description !!}
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>
@endsection
