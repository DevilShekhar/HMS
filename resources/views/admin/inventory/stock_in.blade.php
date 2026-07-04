@extends('layouts.app')
@section('content')
    <section class="section premium-dashboard">
        <div class="premium-floating-header">
            <div class="header-content">
                <div class="header-left">
                    <div class="header-icon">
                        <i class="fas fa-box-open"></i>
                    </div>
                    <div>
                        <span class="header-badge">
                            Inventory Management
                        </span>
                        <h1>Stock IN</h1>
                        <p>Add stock to inventory item.</p>
                    </div>
                </div>

                <div class="header-right">
                    <a href="{{ route('restaurant.inventory.index', ['restaurant' => request()->route('restaurant')]) }}"
                        class="premium-back-btn">
                        <i class="fas fa-arrow-left"></i>
                        Back To Inventory
                    </a>
                </div>
            </div>
        </div>
    </section>
    <section class="section premium-dashboard pt-0">
        <form
            action="{{ route('restaurant.inventory.stock-in.store', ['restaurant' => request()->route('restaurant'), 'inventory' => $inventory->id]) }}"
            method="POST">
            @csrf
            <div class="card premium-block">
                <div class="card-header premium-card-header">
                    <h4>Stock IN</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label class="premium-label">Item Name <span>*</span></label>
                            <input type="text" class="form-control premium-input" value="{{ $inventory->name }}" readonly>
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="premium-label">Current Remaining Stock <span>*</span></label>
                            <input type="text" class="form-control premium-input"
                                value="{{ $inventory->remaining_stock }} {{ ucfirst($inventory->unit) }}" readonly>
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="premium-label">Add Quantity <span>*</span></label>
                            <input type="number" step="0.01" name="quantity" class="form-control premium-input" required>
                            @error('quantity')
                                <span class="text-danger">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>
                        <div class="col-md-12 mb-4">
                            <label class="premium-label">Remarks <span>*</span></label>
                            <textarea name="remarks" rows="4" class="form-control premium-input"
                                placeholder="Purchase, Supplier Delivery etc"></textarea>
                        </div>
                    </div>
                    <div class="premium-card-footer">

                        <button type="submit" class="premium-btn btn-primary"> <i class="fas fa-plus-circle"></i>
                            Add Stock
                        </button>
                    </div>
                </div>
            </div>

        </form>
    </section>
@endsection