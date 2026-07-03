@extends('layouts.app')
@section('content')
    <section class="section premium-dashboard">
        <div class="premium-page-head">
            <div class="premium-page-title">
                <span class="mini-badge">Menu Management</span>
                <h2>Create Menu Item</h2>
                <p>Add a new menu item.</p>
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
                    ]) }}" class="btn premium-btn ghost-btn">
                                <i class="fas fa-arrow-left"></i>
                                Back To Menu Items
                            </a>
                @elseif(!empty($restaurantSlug))
                            <a href="{{ route('restaurant.menu-items.index', [
                        'restaurant' => $restaurantSlug,
                    ]) }}" class="btn premium-btn ghost-btn">
                                <i class="fas fa-arrow-left"></i>
                                Back To Menu Items
                            </a>
                @endif

            </div>
        </div>
    </section>
    <section class="section premium-dashboard pt-0">
        <form action="{{ route('restaurant.menu-items.store', ['restaurant' => request()->route('restaurant')]) }}"
            method="POST" enctype="multipart/form-data">
            @csrf
            <div class="card premium-block">
                <div class="card-header premium-card-header">
                    <div>
                        <h4>Menu Information</h4>
                        <p class="header-subtext">
                            Enter menu item details.
                        </p>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        @if (auth()->user()->hasRole('owner'))
                            <div class="col-md-6 mb-4">
                                <label>Branch</label>
                                <select name="branch_id" id="branch_id" class="form-control premium-input">
                                    <option value="">
                                        Select Branch
                                    </option>
                                    @foreach ($branches as $branch)
                                        <option value="{{ $branch->id }}">
                                            {{ $branch->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('branch_id')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        @else
                            <input type="hidden" name="branch_id" value="{{ $branches->first()->id }}">
                            <div class="col-md-6 mb-4">
                                <label>Branch</label>
                                <input type="text" class="form-control premium-input" value="{{ $branches->first()->name }}"
                                    readonly>
                                @error('branch_id')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        @endif
                        <div class="col-md-6 mb-4">
                            <label>Category</label>
                            <select name="category_id" id="category_id" class="form-control premium-input">
                                <option value="">
                                    Select Category
                                </option>
                                @if (!auth()->user()->hasRole('owner'))
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}">
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                            @error('category_id')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-4">
                            <label>Menu Name</label>
                            <input type="text" name="name" class="form-control premium-input">
                            @error('name')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-4">
                            <label>Price</label>
                            <input type="number" step="0.01" name="price" class="form-control premium-input">
                            @error('number')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-4">
                            <label>Food Type</label>
                            <select name="food_type" class="form-control premium-input">
                                <option value="veg">Veg</option>
                                <option value="non_veg">Non Veg</option>
                            </select>
                            @error('food_type')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-4">
                            <label>Available</label>
                            <select name="is_available" class="form-control premium-input">
                                <option value="1">Yes</option>
                                <option value="0">No</option>
                            </select>
                            @error('is_available')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-4">
                            <label>Image</label>
                            <input type="file" name="image" class="form-control premium-input">
                        </div>
                        <div class="col-md-12 mb-4">
                            <label>Description</label>
                            <textarea name="description" rows="4" class="form-control premium-input"></textarea>
                            @error('description')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-4">
                <button type="submit" class="btn btn-primary">
                    Create Menu Item
                </button>
            </div>
        </form>
    </section>
    @if (auth()->user()->hasRole('owner'))
        <script>
            document.getElementById('branch_id').addEventListener('change', function () {
                let branchId = this.value;
                fetch('/{{ request()->route('restaurant') }}/categories-by-branch/' + branchId)
                    .then(response => response.json())
                    .then(data => {
                        let category = document.getElementById('category_id');
                        category.innerHTML =
                            '<option value="">Select Category</option>';
                        data.forEach(function (item) {
                            category.innerHTML +=
                                `<option value="${item.id}">
                                        ${item.name}
                                    </option>`;
                        });
                    });
            });
        </script>
    @endif

@endsection
