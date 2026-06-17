@extends('layouts.app')
@section('content')
    <section class="section premium-dashboard">
        <div class="premium-page-head">
            <div class="premium-page-title">
                <span class="mini-badge">Category Management</span>
                <h2>Edit Category</h2>
                <p>Update category information.</p>
            </div>
            @php
                $restaurantSlug = request()->route('restaurant');
                $branchSlug = request()->route('branch');
            @endphp

            <div class="premium-head-actions">

                @if (auth()->user()->role === 'super_admin')
                    <a href="{{ route('categories.index') }}" class="btn premium-btn ghost-btn">
                        <i class="fas fa-arrow-left"></i>
                        Back To Category
                    </a>
                @elseif(!empty($restaurantSlug) && !empty($branchSlug))
                    <a href="{{ route('branch.categories.index', [
                        'restaurant' => $restaurantSlug,
                        'branch' => $branchSlug,
                    ]) }}"
                        class="btn premium-btn ghost-btn">
                        <i class="fas fa-arrow-left"></i>
                        Back To Category
                    </a>
                @elseif(!empty($restaurantSlug))
                    <a href="{{ route('restaurant.categories.index', [
                        'restaurant' => $restaurantSlug,
                    ]) }}"
                        class="btn premium-btn ghost-btn">
                        <i class="fas fa-arrow-left"></i>
                        Back To Category
                    </a>
                @endif

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
                                        <label>Branch</label>
                                        <select name="branch_id" class="form-control premium-input">
                                            <option value=""> Select Branch</option>
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
                                    <label>Category Name</label>
                                    <input type="text" name="name" value="{{ old('name', $category->name) }}"
                                        class="form-control premium-input">
                                    @error('name')
                                        <small class="text-danger">
                                            {{ $message }}
                                        </small>
                                    @enderror
                                </div>
                                <div class="col-md-12 mb-4">
                                    <label>Description</label>
                                    <textarea name="description" rows="4" class="form-control premium-input">{{ old('description', $category->description) }}</textarea>
                                    @error('description')
                                        <small class="text-danger">
                                            {{ $message }}
                                        </small>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-4">
                                    <label>Category Image</label>
                                    <input type="file" name="image" class="form-control premium-input">
                                    @error('image')
                                        <small class="text-danger">
                                            {{ $message }}
                                        </small>
                                    @enderror
                                    @if ($category->image)
                                        <div class="mt-3">
                                            <img src="{{ asset($category->image) }}" width="120" class="img-thumbnail">
                                        </div>
                                    @endif
                                </div>
                                <div class="col-md-6 mb-4">
                                    <label>Status</label>
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
