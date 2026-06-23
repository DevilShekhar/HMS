@extends('layouts.app')
@section('content')
    <section class="section premium-dashboard">
        <div class="premium-page-head">
            <div class="premium-page-title">
                <span class="mini-badge">
                    Inventory Management
                </span>
                <h2>Stock IN</h2>
                <p>
                    Add stock to inventory item.
                </p>
            </div>
            <div class="premium-head-actions">
                <a href="{{ route('restaurant.inventory.index', ['restaurant' => request()->route('restaurant')]) }}"
                    class="btn premium-btn ghost-btn"><i class="fas fa-arrow-left"></i> Back To Inventory</a>
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
                            <label>Item Name</label>
                            <input type="text" class="form-control premium-input" value="{{ $inventory->name }}"
                                readonly>
                        </div>
                        <div class="col-md-6 mb-4">
                            <label>Current Remaining Stock</label>
                            <input type="text" class="form-control premium-input"
                                value="{{ $inventory->remaining_stock }} {{ ucfirst($inventory->unit) }}" readonly>
                        </div>
                        <div class="col-md-6 mb-4">
                            <label>Add Quantity</label>
                            <input type="number" step="0.01" name="quantity" class="form-control premium-input"
                                required>
                            @error('quantity')
                                <span class="text-danger">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>
                        <div class="col-md-12 mb-4">
                            <label>Remarks</label>
                            <textarea name="remarks" rows="4" class="form-control premium-input"
                                placeholder="Purchase, Supplier Delivery etc"></textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-4">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-plus-circle"></i>
                    Add Stock
                </button>
            </div>
        </form>
    </section>
@endsection
