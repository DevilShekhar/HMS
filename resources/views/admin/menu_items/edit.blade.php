@extends('layouts.app')
@section('content')
    <section class="section premium-dashboard">
        <div class="premium-page-head">
            <div class="premium-page-title">
                <span class="mini-badge">Menu Management</span>
                <h2>Edit Menu Item</h2>
                <p>Update menu item details.</p>
            </div>
            @php
                $restaurantSlug = request()->route('restaurant');
                $branchSlug = request()->route('branch');
            @endphp

            <div class="premium-head-actions">

                @if (auth()->user()->role === 'super_admin')
                    <a href="{{ route('menu-items.index') }}" class="btn premium-btn ghost-btn">
                        <i class="fas fa-arrow-left"></i>
                        Back To Menu Items
                    </a>
                @elseif(!empty($restaurantSlug) && !empty($branchSlug))
                    <a href="{{ route('branch.menu-items.index', [
                        'restaurant' => $restaurantSlug,
                        'branch' => $branchSlug,
                    ]) }}"
                        class="btn premium-btn ghost-btn">
                        <i class="fas fa-arrow-left"></i>
                        Back To Menu Items
                    </a>
                @elseif(!empty($restaurantSlug))
                    <a href="{{ route('restaurant.menu-items.index', [
                        'restaurant' => $restaurantSlug,
                    ]) }}"
                        class="btn premium-btn ghost-btn">
                        <i class="fas fa-arrow-left"></i>
                        Back To Menu Items
                    </a>
                @endif

            </div>
        </div>
    </section>
    <section class="section premium-dashboard pt-0">
        <form action="{{ route('restaurant.menu-items.update', ['restaurant' => request()->route('restaurant'),'menu_item' => $menuItem->id]) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="card premium-block">
                <div class="card-header premium-card-header">
                    <div>
                        <h4>Menu Information</h4>
                        <p class="header-subtext">
                            Update menu item information.
                        </p>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        @if(auth()->user()->hasRole('owner'))
                        <div class="col-md-6 mb-4">
                            <label>Branch</label>
                            <select name="branch_id" id="branch_id" class="form-control premium-input">
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}"
                                        {{ $menuItem->branch_id == $branch->id ? 'selected' : '' }}>
                                        {{ $branch->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @else
                        <input type="hidden" name="branch_id" value="{{ $branches->first()->id }}">
                        <div class="col-md-6 mb-4">
                            <label>Branch</label>
                            <input type="text" class="form-control premium-input" value="{{ $branches->first()->name }}" readonly>
                        </div>
                        @endif
                        <div class="col-md-6 mb-4">
                            <label>Category</label>
                            <select name="category_id" id="category_id" class="form-control premium-input">
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}"
                                        {{ $menuItem->category_id == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-4">
                            <label>Menu Name</label>
                            <input type="text" name="name" value="{{ old('name', $menuItem->name) }}"  class="form-control premium-input" required>
                        </div>
                        <div class="col-md-6 mb-4">
                            <label>Price</label>
                            <input type="number" step="0.01" name="price"  value="{{ old('price', $menuItem->price) }}"  class="form-control premium-input"  required>
                        </div>
                        <div class="col-md-6 mb-4">
                            <label>Food Type</label>
                            <select name="food_type"class="form-control premium-input">
                                <option value="veg" {{ $menuItem->food_type == 'veg' ? 'selected' : '' }}> Veg</option>
                                <option value="non_veg"  {{ $menuItem->food_type == 'non_veg' ? 'selected' : '' }}> Non Veg </option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-4">
                            <label>Available</label>
                            <select name="is_available"  class="form-control premium-input">
                                <option value="1"
                                    {{ $menuItem->is_available ? 'selected' : '' }}>
                                    Yes
                                </option>
                                <option value="0"
                                    {{ !$menuItem->is_available ? 'selected' : '' }}>
                                    No
                                </option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-4">
                            <label>Status</label>
                            <select name="is_active" class="form-control premium-input">
                                <option value="1"
                                    {{ $menuItem->is_active ? 'selected' : '' }}>
                                    Active
                                </option>
                                <option value="0"
                                    {{ !$menuItem->is_active ? 'selected' : '' }}>
                                    Inactive
                                </option>
                            </select>
                        </div>
                        <div class="col-md-12 mb-4">
                            <label>Description</label>
                            <textarea name="description"  rows="4" class="form-control premium-input">{{ old('description', $menuItem->description) }}</textarea>
                        </div>
                        <div class="col-md-6 mb-4">
                            <label>Menu Image</label>
                            <input type="file" name="image" class="form-control premium-input">
                        </div>
                        @if($menuItem->image)
                        <div class="col-md-6 mb-4">
                            <label>Current Image</label>
                            <br>
                            <img src="{{ asset($menuItem->image) }}" width="120" class="rounded border">
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="mt-4">
                <button type="submit"
                        class="btn btn-primary">
                    Update Menu Item
                </button>
            </div>
        </form>
    </section>
    @if(auth()->user()->hasRole('owner'))
        <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.getElementById('branch_id')
                .addEventListener('change', function () {
                    let branchId = this.value;
                    fetch('/{{ request()->route("restaurant") }}/categories-by-branch/' + branchId)
                        .then(response => response.json())
                        .then(data => {
                            let categorySelect =
                                document.getElementById('category_id');
                            categorySelect.innerHTML =
                                '<option value="">Select Category</option>';
                            data.forEach(function(category) {
                                categorySelect.innerHTML +=
                                    `<option value="${category.id}">
                                        ${category.name}
                                    </option>`;
                            });
                        });
                });
        });
        </script>
    @endif
@endsection
