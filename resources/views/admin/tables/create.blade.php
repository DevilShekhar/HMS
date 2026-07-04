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
                        <h1>Add New Table</h1>
                        <p>Configure a new restaurant table with its details and availability.</p>
                    </div>
                </div>
                <div class="header-right">
                    @php
                        $restaurantSlug = request()->route('restaurant');
                        $branchSlug = request()->route('branch');
                    @endphp
                    <div class="premium-head-actions">
                        @if (auth()->user()->role === 'super_admin')
                            <a href="{{ route('tables.index') }}" class="premium-back-btn"><i class="fas fa-arrow-left"></i>Back To Tables</a>
                        @elseif(!empty($restaurantSlug) && !empty($branchSlug))
                            <a href="{{ route('branch.tables.index', ['restaurant' => $restaurantSlug,'branch' => $branchSlug,]) }}" class="premium-back-btn"><i class="fas fa-arrow-left"></i> Back To Tables </a>
                        @elseif(!empty($restaurantSlug))
                            <a href="{{ route('restaurant.tables.index', ['restaurant' => $restaurantSlug,]) }}" class="premium-back-btn"> <i class="fas fa-arrow-left"></i> Back To Tables</a>
                        @endif              
                    </div>
                </div> 
            </div>
        </div>
    </section>
    
    <section class="section premium-dashboard pt-0">
        <form action="{{ route('restaurant.tables.store', ['restaurant' => request()->route('restaurant')]) }}"
            method="POST">
            @csrf
            <div class="card premium-block">
                <div class="premium-card-header">
                    <div class="card-title-group">                        
                        <div>
                            <h3>Create Table</h3>
                            <p>Fill in the table number, category, capacity, and status to add a new table.</p>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        @if (auth()->user()->role == 'owner')
                            <div class="col-md-6 mb-4">
                                <label class="form-label">Branch</label>
                                <select name="branch_id" id="branch_id" class=" premium-input">
                                    <option value="">Select Branch</option>
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
                        @elseif (isset($branch))
                            <input type="hidden" name="branch_id" value="{{ $branch->id }}">

                            <div class="col-md-6 mb-4">
                                <label class="form-label">Branch</label>
                                <input type="text" class=" premium-input" value="{{ $branch->name }}"
                                    readonly>
                            </div>
                        @endif
                        <div class="col-md-6 mb-4">
                            <label class="form-label"> Table Category </label>
                            <select name="cat_id" id="category_id" class=" premium-input" required>
                                <option value=""> Select Category </option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" data-branch="{{ $category->branch_id }}"
                                        @if (auth()->user()->role == 'owner') style="display:none;" @endif>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('cat_id')
                                <small class="text-danger">
                                    {{ $message }}
                                </small>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="form-label">
                                Table Number
                            </label>
                            <input type="text" name="table_number" value="{{ old('table_number') }}"
                                class="premium-input" placeholder="Ex: T-01" required>
                            @error('table_number')
                                <small class="text-danger">
                                    {{ $message }}
                                </small>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="form-label">
                                Capacity
                            </label>
                            <input type="number" name="capacity" value="{{ old('capacity', 4) }}" min="1"
                                class=" premium-input" required>
                            @error('capacity')
                                <small class="text-danger">
                                    {{ $message }}
                                </small>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="premium-card-footer">                       
                    <button type="submit" class="premium-btn btn-primary"> <i class="fas fa-plus-circle"></i>
                        Create Table
                    </button>
                </div>
            </div>            
        </form>
    </section>
    @if (auth()->user()->role == 'owner')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const branchSelect = document.getElementById('branch_id');
                const categorySelect = document.getElementById('category_id');
                branchSelect.addEventListener('change', function() {
                    let branchId = this.value;
                    categorySelect.value = '';
                    categorySelect.querySelectorAll('option').forEach(function(option) {
                        if (option.value === '') {
                            option.style.display = '';
                            return;
                        }
                        if (option.dataset.branch == branchId) {
                            option.style.display = '';
                        } else {
                            option.style.display = 'none';
                        }
                    });
                });
            });
        </script>
    @endif
@endsection
