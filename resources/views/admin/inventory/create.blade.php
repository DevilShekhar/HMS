@can('create-inventory')
@extends('layouts.app')
@section('content')
    <section class="section premium-dashboard">
        <div class="premium-page-head">
            <div class="premium-page-title">
                <span class="mini-badge">
                    Inventory Management
                </span>
                <h2>Create Inventory Item</h2>
                <p>
                    Add new inventory stock item.
                </p>
            </div>
            @php
                $restaurantSlug = request()->route('restaurant');
                $branchSlug = request()->route('branch');
            @endphp

            <div class="premium-head-actions">

                @if ($branchSlug)
                    <a href="{{ route('branch.inventory.index', [
                        'restaurant' => $restaurantSlug,
                        'branch' => $branchSlug,
                    ]) }}"
                        class="btn premium-btn ghost-btn">
                        <i class="fas fa-arrow-left"></i>
                        Back To Inventory
                    </a>
                @else
                    <a href="{{ route('restaurant.inventory.index', [
                        'restaurant' => $restaurantSlug,
                    ]) }}"
                        class="btn premium-btn ghost-btn">
                        <i class="fas fa-arrow-left"></i>
                        Back To Inventory
                    </a>
                @endif

            </div>
        </div>
    </section>
    <section class="section premium-dashboard pt-0">
        <form action="{{ route('restaurant.inventory.store', ['restaurant' => request()->route('restaurant')]) }}"
            method="POST">
            @csrf
            <div class="card premium-block">

                <div class="card-header premium-card-header">
                    <div>
                        <h4>Inventory Information</h4>
                        <p class="header-subtext">
                            Enter inventory item details.
                        </p>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        @if (auth()->user()->hasRole('owner'))
                            <div class="col-md-6 mb-4">
                                <label>Branch</label>
                                <select name="branch_id" class="form-control premium-input">
                                    <option value=""> Select Branch</option>
                                    @foreach ($branches as $branch)
                                        <option value="{{ $branch->id }}"
                                            {{ old('branch_id') == $branch->id ? 'selected' : '' }}>
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
                        @else
                            <input type="hidden" name="branch_id" value="{{ $branch->id }}">
                            <div class="col-md-6 mb-4">
                                <label>Branch</label>
                                <input type="text" class="form-control premium-input" value="{{ $branch->name }}"
                                    readonly>
                            </div>
                        @endif
                        <div class="col-md-6 mb-4">
                            <label>Item Name</label>
                            <input type="text" name="name" value="{{ old('name') }}"
                                class="form-control premium-input" placeholder="Oil, Rice, Atta, Tomato">
                            @error('name')
                                <small class="text-danger">
                                    {{ $message }}
                                </small>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-4">
                            <label>Unit</label>
                            <select name="unit" class="form-control premium-input">
                                <option value="">Select Unit</option>
                                <option value="kg">Kg</option>
                                <option value="gram">Gram</option>
                                <option value="liter">Liter</option>
                                <option value="ml">ML</option>
                                <option value="packet">Packet</option>
                                <option value="piece">Piece</option>
                            </select>
                            @error('unit')
                                <small class="text-danger">
                                    {{ $message }}
                                </small>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-4">
                            <label>Total Stock</label>
                            <input type="number" step="0.01" name="total_stock" value="{{ old('total_stock') }}"
                                class="form-control premium-input">
                            @error('total_stock')
                                <small class="text-danger">
                                    {{ $message }}
                                </small>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-4">
                            <label>Minimum Stock</label>
                            <input type="number" step="0.01" name="minimum_stock" value="{{ old('minimum_stock') }}"
                                class="form-control premium-input">
                            @error('minimum_stock')
                                <small class="text-danger">
                                    {{ $message }}
                                </small>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-4">
                <button type="submit" class="btn btn-primary">
                    Create Inventory Item
                </button>
            </div>
        </form>
    </section>
@endsection
@else
    @php
        abort(403);
    @endphp
@endcan
