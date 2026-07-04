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
                        Category Management
                    </span>
                    <h1>Edit Category</h1>
                    <p>Update category information.</p>
                </div>
            </div>

            @php
                $restaurantSlug = request()->route('restaurant');
                $branchSlug = request()->route('branch');
            @endphp

            <div class="header-right">
                {{-- SUPER ADMIN LEVEL --}}
                @if (auth()->user()->role === 'super_admin')
                    <a href="{{ route('categories.index') }}" class="premium-back-btn">
                        <i class="fas fa-arrow-left"></i>
                        Back To Category
                    </a>
                {{-- BRANCH LEVEL --}}
                @elseif(!empty($restaurantSlug) && !empty($branchSlug))
                    <a href="{{ route('branch.categories.index', [
                        'restaurant' => $restaurantSlug,
                        'branch' => $branchSlug,
                    ]) }}" class="premium-back-btn">
                        <i class="fas fa-arrow-left"></i>
                        Back To Category
                    </a>
                {{-- RESTAURANT LEVEL --}}
                @elseif(!empty($restaurantSlug))
                    <a href="{{ route('restaurant.categories.index', [
                        'restaurant' => $restaurantSlug,
                    ]) }}" class="premium-back-btn">
                        <i class="fas fa-arrow-left"></i>
                        Back To Category
                    </a>
                @endif
            </div>
        </div>
    </div>
</section>
    <section class="section premium-dashboard pt-0">
        <form
            action="{{ route('restaurant.categories.update', ['restaurant' => request()->route('restaurant'), 'category' => $category->id]) }}"
            method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="row">
                <div class="col-lg-12">
                    <div class="card premium-block">
                        <div class="card-header premium-card-header">
                            <div>
                                <h4>Category Information</h4>
                                <p class="header-subtext">
                                    Update category details.
                                </p>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                @if (auth()->user()->role == 'owner')
                                    <div class="col-md-6 mb-4">
                                        <label class="premium-label">Branch <span>*</span></label>
                                        <select name="branch_id" class="form-control premium-input">
                                            <option value=""> Select Branch <span>*</span></option>
                                            @foreach ($branches as $branch)
                                                <option value="{{ $branch->id }}"
                                                    {{ old('branch_id', $category->branch_id) == $branch->id ? 'selected' : '' }}>
                                                    {{ $branch->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('branch_id')
                                            <small class="text-danger">
                                                {{ $message }}
                                            </small>
                                        @enderror
                                    </div>
                                @endif
                                <div class="col-md-6 mb-4">
                                    <label class="premium-label">Category Name <span>*</span></label>
                                    <input type="text" name="name" value="{{ old('name', $category->name) }}"
                                        class="form-control premium-input">
                                    @error('name')
                                        <small class="text-danger">
                                            {{ $message }}
                                        </small>
                                    @enderror
                                </div>
                                <div class="col-md-12 mb-4">
                                    <label class="premium-label">Description <span>*</span></label>
                                    <textarea name="description" rows="4" class="form-control premium-input">{{ old('description', $category->description) }}</textarea>
                                    @error('description')
                                        <small class="text-danger">
                                            {{ $message }}
                                        </small>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-4">
                                    <label class="premium-label">
                                            Status
                                         <span>*</span></label>
                                    <select name="is_active" class="form-control premium-input">
                                        <option value="1" {{ $category->is_active == 1 ? 'selected' : '' }}> Active
                                        </option>
                                        <option value="0" {{ $category->is_active == 0 ? 'selected' : '' }}> Inactive
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary"> Update Category</button>
                    </div>
                </div>
            </div>
        </form>
    </section>
@endsection
