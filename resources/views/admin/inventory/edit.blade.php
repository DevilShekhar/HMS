@can('edit-inventory')
@extends('layouts.app')
@section('content')
    <section class="section premium-dashboard">
        <div class="premium-page-head">
            <div class="premium-page-title">
                <span class="mini-badge">
                    Inventory Management
                </span>
                <h2>Edit Inventory Item</h2>
                <p>
                    Update inventory item details.
                </p>
            </div>
            <div class="premium-head-actions">
                <a href="{{ route('restaurant.inventory.index', ['restaurant' => request()->route('restaurant')]) }}" class="btn premium-btn ghost-btn">
                    <i class="fas fa-arrow-left"></i>
                    Back To Inventory
                </a>
            </div>
        </div>
    </section>
    <section class="section premium-dashboard pt-0">
        <form action="{{ route('restaurant.inventory.update', ['restaurant' => request()->route('restaurant'),'inventory' => $inventory->id]) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="card premium-block">
                <div class="card-header premium-card-header">
                    <div>
                        <h4>Inventory Information</h4>
                        <p class="header-subtext">
                            Update inventory item details.
                        </p>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        @if(auth()->user()->hasRole('owner'))
                            <div class="col-md-6 mb-4">
                                <label>Branch</label>
                                <select name="branch_id" class="form-control premium-input">
                                    @foreach($branches as $branch)
                                        <option value="{{ $branch->id }}"
                                            {{ $inventory->branch_id == $branch->id ? 'selected' : '' }}>
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
                        <input type="hidden" name="branch_id" value="{{ $inventory->branch_id }}">
                        <div class="col-md-6 mb-4">
                            <label>Branch</label>
                            <input type="text" class="form-control premium-input" value="{{ $inventory->branch->name ?? '' }}" readonly>
                        </div>
                        @endif
                        <div class="col-md-6 mb-4">
                            <label>Item Name</label>
                            <input type="text" name="name" value="{{ old('name', $inventory->name) }}" class="form-control premium-input">
                            @error('name')
                                <small class="text-danger">
                                    {{ $message }}
                                </small>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-4">
                            <label>Unit</label>
                            <select name="unit" class="form-control premium-input">
                                <option value="kg" {{ $inventory->unit == 'kg' ? 'selected' : '' }}>Kg</option>
                                <option value="gram" {{ $inventory->unit == 'gram' ? 'selected' : '' }}>Gram</option>
                                <option value="liter" {{ $inventory->unit == 'liter' ? 'selected' : '' }}>Liter</option>
                                <option value="ml" {{ $inventory->unit == 'ml' ? 'selected' : '' }}>ML</option>
                                <option value="packet" {{ $inventory->unit == 'packet' ? 'selected' : '' }}>Packet</option>
                                <option value="piece" {{ $inventory->unit == 'piece' ? 'selected' : '' }}>Piece</option>
                            </select>
                            @error('unit')
                                <small class="text-danger">
                                    {{ $message }}
                                </small>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-4">
                            <label>Total Stock</label>
                            <input type="number" step="0.01" name="total_stock" value="{{ old('total_stock', $inventory->total_stock) }}" class="form-control premium-input">
                            @error('total_stock')
                                <small class="text-danger">
                                    {{ $message }}
                                </small>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-4">
                            <label>Remaining Stock</label>
                            <input type="number" step="0.01" name="remaining_stock" value="{{ old('remaining_stock', $inventory->remaining_stock) }}" class="form-control premium-input">
                            @error('remaining_stock')
                                <small class="text-danger">
                                    {{ $message }}
                                </small>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-4">
                            <label>Minimum Stock</label>
                            <input type="number" step="0.01" name="minimum_stock" value="{{ old('minimum_stock', $inventory->minimum_stock) }}"  class="form-control premium-input">
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
                    Update Inventory Item
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
