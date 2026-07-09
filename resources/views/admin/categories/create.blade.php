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
                        <h1>Create Category</h1>
                        <p>Add a new category for a branch.</p>
                    </div>
                </div>
                @php
                    $restaurantSlug = request()->route('restaurant');
                    $branchSlug = request()->route('branch');
                @endphp

                <div class="premium-head-actions">

                    @if (auth()->user()->role === 'super_admin')
                        <a href="{{ route('categories.index') }}" class="premium-back-btn">
                            <i class="fas fa-arrow-left"></i>
                            Back To Category
                        </a>
                    @elseif(!empty($restaurantSlug) && !empty($branchSlug))
                                    <a href="{{ route('branch.categories.index', [
                            'restaurant' => $restaurantSlug,
                            'branch' => $branchSlug,
                        ]) }}" class="premium-back-btn">
                                        <i class="fas fa-arrow-left"></i>
                                        Back To Category
                                    </a>
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
        <div class="row">
            <div class=" col-md-12">
                @php
                    $restaurantSlug = request()->route('restaurant');
                @endphp

                @if ($restaurantSlug)
                            <form action="{{ route('restaurant.categories.store', [
                        'restaurant' => $restaurantSlug,
                    ]) }}" method="POST" enctype="multipart/form-data">
                @else
                        <form action="{{ route('categories.store') }}" method="POST" enctype="multipart/form-data">
                    @endif
                        @csrf
                        <div class="premium-card">
                            <div class="premium-card-header">
                                <div class="card-title-group">
                                    <div>
                                        <h3>Category Information</h3>
                                        <p>Enter category details below.</p>
                                    </div>
                                </div>
                            </div>
                            @if(auth()->user()->role == 'owner')

                                <div class="premium-card-body">
                                    <div class="premium-form-group">
                                        <label class="premium-label">Branch <span>*</span></label>

                                        <select name="branch_id" class="form-control premium-input">
                                            <option value="">Select Branch</option>
                                            @foreach($branches as $branch)
                                                <option value="{{ $branch->id }}" {{ old('branch_id') == $branch->id ? 'selected' : '' }}>
                                                    {{ $branch->name }}
                                                </option>
                                            @endforeach
                                        </select>

                                        @error('branch_id')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>

                            @elseif(auth()->user()->role == 'branch_manager')

                                <div class="premium-card-body">
                                    <div class="premium-form-group">
                                        <label class="premium-label">Branch <span>*</span></label>

                                        <input type="hidden" name="branch_id" value="{{ auth()->user()->branch_id }}">

                                        <select class="form-control premium-input" disabled>
                                            <option selected>
                                                {{ optional(auth()->user()->branch)->name }}
                                            </option>
                                        </select>

                                        @error('branch_id')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>

                            @endif

                            <div class="premium-card-body">
                                <div class="premium-form-group">

                                    <label class="premium-label">Category Name <span>*</span></label>
                                    <input type="text" name="name" value="{{ old('name') }}" class=" premium-input">
                                    @error('name')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="premium-card-body">
                                <div class="premium-form-group">
                                    <label class="premium-label">Description <span>*</span></label>
                                    <textarea name="description" rows="4"
                                        class=" premium-input">{{ old('description') }}</textarea>
                                    @error('description')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="premium-card-footer">

                                <button type="submit" class="premium-btn btn-primary"> <i class="fas fa-plus-circle"></i>
                                    Create Category
                                </button>
                            </div>
                        </div>
                    </form>
            </div>
        </div>
    </section>

@endsection
