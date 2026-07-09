@extends('layouts.app')
@section('title', 'Create Table Allocation')
@section('content')
    @php
        $restaurant = request()->route('restaurant');
        $branch = request()->route('branch');
        if ($branch) {
            $route = 'branch.table-allocations.store';
            $params = [
                'restaurant' => $restaurant,
                'branch' => $branch,
            ];
        } else {
            $route = 'restaurant.table-allocations.store';
            $params = [
                'restaurant' => $restaurant,
            ];
        }
    @endphp
    <section class="section premium-dashboard">
        <div class="premium-floating-header">
            <div class="header-content">
                <div class="header-left">
                    <div class="header-icon">
                        <i class="fas fa-th-large"></i>
                    </div>
                    <div>
                        <span class="header-badge">Table Allocation Management</span>
                        <h2>Create Allocation Table</h2>
                        <p>Create restaurant table.</p>
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
    <section class="section premium-dashboard">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">New Table Allocation</h3>
            </div>
            <form action="{{ route($route, $params) }}" method="POST">
                @csrf
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Branch <span class="text-danger">*</span></label>
                                @if(auth()->user()->role == 'branch_manager')
                                    <input type="text" class="premium-input" value="{{ $branches->first()->name }}" readonly>
                                    <input type="hidden" name="branch_id" value="{{ $branches->first()->id }}">
                                @else
                                    <select name="branch_id" class="premium-input" required>
                                        <option value="">-- Select Branch --</option>
                                        @foreach($branches as $branch)
                                            <option value="{{ $branch->id }}" {{ old('branch_id') == $branch->id ? 'selected' : '' }}>
                                                {{ $branch->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                @endif
                                @error('branch_id')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Table <span class="text-danger">*</span></label>
                                <select name="table_id" class="premium-input" required>
                                    <option value="">-- Select Table --</option>
                                    @foreach($tables as $table)
                                        <option value="{{ $table->id }}" {{ old('table_id') == $table->id ? 'selected' : '' }}>
                                            {{ $table->table_number }} ({{ $table->capacity }} Seats)
                                        </option>
                                    @endforeach
                                </select>
                                @error('table_id')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Waiter</label>
                                <select name="waiter_id" class="premium-input" required>
                                    <option value="">-- Select Waiter --</option>
                                    @foreach($waiters as $waiter)
                                        <option value="{{ $waiter->id }}">{{ $waiter->name }} ({{ $waiter->email }})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Create Allocation</button>
                        @php
                            $restaurant = request()->route('restaurant');
                            $branch = request()->route('branch');
                            if ($branch) {
                                $cancelRoute = 'branch.table-allocations.index';
                                $cancelParams = [
                                    'restaurant' => $restaurant,
                                    'branch' => $branch,
                                ];
                            } else {
                                $cancelRoute = 'restaurant.table-allocations.index';
                                $cancelParams = [
                                    'restaurant' => $restaurant,
                                ];
                            }
                        @endphp
                        <a href="{{ route($cancelRoute, $cancelParams) }}" class="btn btn-secondary">
                            Cancel
                        </a>
                    </div>
            </form>
        </div>
        </div>
    </section>
@endsection
