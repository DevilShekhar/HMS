@extends('layouts.app')
@section('content')
<section class="section premium-dashboard">
        <div class="premium-floating-header">
            <div class="header-content">
                <div class="header-left">
                    <div class="header-icon">
                        <i class="fas fa-tags"></i>
                    </div>
                    <div>
                        <span class="header-badge">
                            Table Categories
                        </span>
                       <h2>Edit Table Category</h2>
                <p>Update table category information.</p>
                    </div>
                </div>
                <div class="header-right">
                    @php
                $restaurantSlug = request()->route('restaurant');
                $branchSlug = request()->route('branch');
            @endphp

            <div class="premium-head-actions">

                @if (auth()->user()->role === 'super_admin')
                    <a href="{{ route('table-categories.index') }}" class="premium-back-btn">
                        <i class="fas fa-arrow-left"></i>
                        Back To Table Categories
                    </a>
                @elseif(!empty($restaurantSlug) && !empty($branchSlug))
                    <a href="{{ route('branch.table-categories.index', [
                        'restaurant' => $restaurantSlug,
                        'branch' => $branchSlug,
                    ]) }}"
                        class="premium-back-btn">
                        <i class="fas fa-arrow-left"></i>
                        Back To Table Categories
                    </a>
                @elseif(!empty($restaurantSlug))
                    <a href="{{ route('restaurant.table-categories.index', [
                        'restaurant' => $restaurantSlug,
                    ]) }}"
                        class="premium-back-btn">
                        <i class="fas fa-arrow-left"></i>
                        Back To Table Categories
                    </a>
                @endif

            </div>        
                </div>
            </div>    
        </div>    
    </section>  
    
    <section class="section premium-dashboard pt-0">
        <form
            action="{{ request()->route('branch')
                ? route('branch.table-categories.update', [
                    'restaurant' => request()->route('restaurant'),
                    'branch' => request()->route('branch'),
                    'table_category' => $tableCategory->id,
                ])
                : route('restaurant.table-categories.update', [
                    'restaurant' => request()->route('restaurant'),
                    'table_category' => $tableCategory->id,
                ]) }}"
            method="POST">
            @csrf
            @method('PUT')
            <div class="row">
                <div class="col-lg-12">
                    <div class="card premium-block">
                        <div class="card-header premium-card-header">
                            <div>
                                <h4>Table Category Information</h4>
                                <p class="header-subtext">
                                    Update table category details.
                                </p>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                @if (auth()->user()->role == 'owner')
                                    <div class="col-md-6 mb-4">
                                        <label class="form-label">
                                            Branch
                                        </label>
                                        <select name="branch_id" class=" premium-input">
                                            <option value="">
                                                Select Branch
                                            </option>
                                            @foreach ($branches as $branch)
                                                <option value="{{ $branch->id }}"
                                                    {{ old('branch_id', $tableCategory->branch_id) == $branch->id ? 'selected' : '' }}>
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
                                @if(request()->route('branch'))
                                <input type="hidden"
                                    name="branch_id"
                                    value="{{ $branch->id }}">

                                <div class="col-md-6 mb-4">
                                    <label class="form-label">
                                        Branch
                                    </label>

                                    <input type="text"
                                        class="premium-input"
                                        value="{{ $branch->name }}"
                                        readonly>
                                </div>
                            @endif
                                <div class="col-md-6 mb-4">
                                    <label class="form-label">
                                        Table Category Name
                                    </label>
                                    <input type="text" name="name" value="{{ old('name', $tableCategory->name) }}"
                                        class=" premium-input" placeholder="Enter category name">
                                    @error('name')
                                        <small class="text-danger">
                                            {{ $message }}
                                        </small>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="premium-card-footer">                       
                            <button type="submit" class="premium-btn btn-primary"> <i class="fas fa-plus-circle"></i>
                                 Update Table Category
                            </button>
                        </div>
                    </div>                    
                </div>
            </div>
        </form>
    </section>
@endsection
