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
                        <p>Manage restaurant Enter menu item details.</p>
                    </div>
                </div>
                @php
                    $restaurantSlug = request()->route('restaurant');
                    $branchSlug = request()->route('branch');
                @endphp
                <div class="premium-head-actions">
                    @if (auth()->user()->role === 'super_admin')
                        <a href="{{ route('menu-items.index') }}" class="premium-back-btn">
                            <i class="fas fa-arrow-left"></i>
                            Back To Menu Items
                        </a>
                    @elseif(!empty($restaurantSlug) && !empty($branchSlug))
                        <a href="{{ route('branch.menu-items.index', [
                                'restaurant' => $restaurantSlug,
                                'branch' => $branchSlug,
                            ]) }}" class="premium-back-btn">
                            <i class="fas fa-arrow-left"></i>
                            Back To Menu Items
                        </a>
                    @elseif(!empty($restaurantSlug))
                        <a href="{{ route('restaurant.menu-items.index', [
                            'restaurant' => $restaurantSlug,
                            ]) }}" class="premium-back-btn">
                            <i class="fas fa-arrow-left"></i>
                            Back To Menu Items
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </section>    
    <section class="section premium-dashboard pt-0">
        <form action="{{ route('restaurant.menu-items.store', ['restaurant' => request()->route('restaurant')]) }}"
            method="POST" enctype="multipart/form-data">
            @csrf
            <div class="premium-card">                
                <div class="premium-card-body">
                    <div class="row">
                        @if (auth()->user()->hasRole('owner'))
                            <div class="col-md-4 mb-4">
                                <label class="premium-label"> Branch <span>*</span></label>
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
                            <div class="col-md-4 mb-4">
                                <label class="premium-label">Branch <span>*</span></label>
                                <input type="text" class="form-control premium-input" value="{{ $branches->first()->name }}"
                                    readonly>
                                @error('branch_id')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        @endif
                        <div class="col-md-4 mb-4">
                            <label class="premium-label">Category <span>*</span></label>
                            <select name="category_id" id="category_id" class="form-control premium-input">
                                <option value="">
                                    Select Category
                                </option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}">
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                            </select>
                            @error('category_id')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-4">
                            <label class="premium-label">Menu Name <span>*</span></label>
                            <input type="text" name="name" class="form-control premium-input">
                            @error('name')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="col-md-3 mb-4">
                            <label class="premium-label">Price <span>*</span></label>
                            <input type="number" step="0.01" name="price" class="form-control premium-input">
                            @error('number')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="col-md-3 mb-4">
                            <label class="premium-label">Food Type <span>*</span></label>
                            <select name="food_type" class="form-control premium-input">
                                <option value="veg">Veg</option>
                                <option value="non_veg">Non Veg</option>
                            </select>
                            @error('food_type')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>                       
                        <div class="col-md-6 mb-4">
                            <div class="premium-form-group">
                                <label class="premium-label">
                                    <i class="fas fa-image me-2"></i>
                                    Image <span>*</span>
                                </label>
                                <div class="upload-wrapper">
                                    <div class="upload-input">
                                        <input type="file" name="image" id="image" class="form-control "  accept="image/png,image/jpeg,image/jpg,image/webp" onchange="previewImage(event)">
                                        <small>JPG, PNG, JPEG, WEBP (Max 2 MB)</small>
                                         @error('image')
                                            <div class="invalid-feedback d-block">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                    <div class="upload-preview">
                                        <img id="preview" src="{{ asset('images/no-image.png') }}" alt="Preview">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 mb-4">
                            <label class="premium-label">Description <span>*</span></label>
                            <textarea name="description" id="description" rows="4" class="form-control premium-input"></textarea>
                            @error('description')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="premium-card-footer">                       
                    <button type="submit" class="premium-btn btn-primary"> <i class="fas fa-plus-circle"></i>
                        Create Menu Item
                    </button>
                </div>
            </div>           
        </form>
    </section>
    <script>
        function previewImage(event) {
            const reader = new FileReader();
            reader.onload = function () {
                document.getElementById('preview').src = reader.result;
            };
            reader.readAsDataURL(event.target.files[0]);
        }
        </script>
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