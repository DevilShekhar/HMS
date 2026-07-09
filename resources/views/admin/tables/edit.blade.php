@extends('layouts.app')
@section('content')
    <section class="section premium-dashboard">
        <div class="premium-floating-header">
            <div class="header-content">
                <div class="header-left">
                    <div class="header-icon">
                        <i class="fas fa-th-large"></i>
                    </div>
                    <div>
                        <span class="header-badge">Table Management</span>
                         <h2>Edit Table</h2>
                            <p>Update restaurant table.</p>
                    </div>
                </div>
                @php
                    $restaurantSlug = request()->route('restaurant');
                    $branchSlug = request()->route('branch');
                @endphp
                <div class="header-right">
                    {{-- SUPER ADMIN LEVEL --}}
                    @if (auth()->user()->role === 'super_admin')
                        <a href="{{ route('tables.index') }}" class="premium-back-btn">
                            <i class="fas fa-arrow-left"></i>
                            Back To Tables
                        </a>
                        {{-- BRANCH LEVEL --}}
                    @elseif(!empty($restaurantSlug) && !empty($branchSlug))
                                    <a href="{{ route('branch.tables.index', [
                            'restaurant' => $restaurantSlug,
                            'branch' => $branchSlug,
                        ]) }}" class="premium-back-btn">
                                        <i class="fas fa-arrow-left"></i>
                                        Back To Tables
                                    </a>
                                    {{-- RESTAURANT LEVEL --}}
                    @elseif(!empty($restaurantSlug))
                                    <a href="{{ route('restaurant.tables.index', [
                            'restaurant' => $restaurantSlug,
                        ]) }}" class="premium-back-btn">
                                        <i class="fas fa-arrow-left"></i>
                                        Back To Tables
                                    </a>
                    @endif
                </div>
            </div>
        </div>
    </section>
    <section class="section premium-dashboard pt-0">
        <form action="{{ route('restaurant.tables.update',['restaurant' => request()->route('restaurant'),'table' => $table->id]) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="card premium-block">
                <div class="card-header premium-card-header">
                    <div>
                        <h4>Table Information</h4>
                        <p class="header-subtext">
                            Update table details.
                        </p>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        @if(auth()->user()->role == 'owner')
                        <div class="col-md-6 mb-4">
                            <label>Branch</label>
                            <select name="branch_id" class=" premium-input">
                                @foreach($branches as $branch)
                                <option value="{{ $branch->id }}"
                                    {{ $table->branch_id == $branch->id ? 'selected' : '' }}>
                                    {{ $branch->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        @endif
                        @if(auth()->user()->role == 'branch_manager')
                        <input type="hidden" name="branch_id" value="{{ $branch->id }}">
                        <div class="col-md-6 mb-4">
                            <label>Branch</label>
                            <input type="text" class=" premium-input" value="{{ $branch->name }}" readonly>
                        </div>
                        @endif
                        <div class="col-md-6 mb-4">
                            <label>Table Category</label>
                            <select name="cat_id" class=" premium-input">
                                @foreach($categories as $category)
                                <option value="{{ $category->id }}"
                                    {{ $table->cat_id == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-4">
                            <label>Table Number</label>
                            <input type="text" name="table_number" class=" premium-input" value="{{ $table->table_number }}">
                        </div>
                        <div class="col-md-6 mb-4">
                            <label>Capacity</label>
                            <input type="number" name="capacity" class=" premium-input" value="{{ $table->capacity }}">
                        </div>
                        <div class="col-md-6 mb-4">
                                    <label class="premium-label">
                                            Status
                                         <span>*</span></label>
                                    <select name="status" class="form-control premium-input">
                                        <option value="1" {{ $table->status == 1 ? 'selected' : '' }}> Active
                                        </option>
                                        <option value="0" {{ $table->status == 0 ? 'selected' : '' }}> Inactive
                                        </option>
                                    </select>
                                </div>
                    </div>
                    <div class="premium-card-footer">
                        <button type="submit" class="premium-btn btn-primary"> <i class="fas fa-plus-circle"></i>
                            Update Table
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </section>
@endsection
