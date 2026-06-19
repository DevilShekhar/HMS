@extends('layouts.app')
@section('content')
    <section class="section premium-dashboard">
        <div class="premium-page-head">
            <div class="premium-page-title">
                <span class="mini-badge">Category Management</span>
                <h2>Create Category</h2>
                <p>Add a new category for a branch.</p>
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
        @php
            $restaurantSlug = request()->route('restaurant');
        @endphp

        @if ($restaurantSlug)
            <form
                action="{{ route('restaurant.categories.store', [
                    'restaurant' => $restaurantSlug,
                ]) }}"
                method="POST" enctype="multipart/form-data">
            @else
                <form action="{{ route('categories.store') }}" method="POST" enctype="multipart/form-data">
        @endif
        @csrf
        <div class="row">
            <div class="col-lg-12">
                <div class="card premium-block">
                    <div class="card-header premium-card-header">
                        <div>
                            <h4>Category Information</h4>
                            <p class="header-subtext">
                                Enter category details.
                            </p>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @if (auth()->user()->role == 'owner')
                                <div class="col-md-6 mb-4">
                                    <label>Branch</label>
                                    <select name="branch_id" class="form-control premium-input">
                                        <option value=""> Select Branch </option>
                                        @foreach ($branches as $branch)
                                            <option value="{{ $branch->id }}"
                                                {{ old('branch_id') == $branch->id ? 'selected' : '' }}>
                                                {{ $branch->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('branch_id')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            @endif
                            <div class="col-md-6 mb-4">
                                <label>Category Name</label>
                                <input type="text" name="name" value="{{ old('name') }}"
                                    class="form-control premium-input">
                                @error('name')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-12 mb-4">
                                <label>Description</label>
                                <textarea name="description" rows="4" class="form-control premium-input">{{ old('description') }}</textarea>
                                @error('description')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-4">
                                <label>Status</label>
                                <select name="is_active" class="form-control premium-input">
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-4">
                    <button type="submit" class="btn btn-primary"> Create Category</button>
                </div>
            </div>
        </div>
        </form>
    </section>
@endsection
