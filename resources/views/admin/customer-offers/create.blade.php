@extends('layouts.app')

@section('content')
<section class="section premium-dashboard">
        <div class="premium-floating-header">
            <div class="header-content">
                <div class="header-left">
                    <div class="header-icon">
                         <i class="fas fa-bullhorn"></i>
                    </div>
                    <div>
                        <span class="header-badge">
                            Offer Management
                        </span>
                            <h1>Create Customer Offer</h1>
                            <p>Create and manage special offers for your customers.</p>
                    </div>
                </div>
                @php
                    $restaurantSlug = request()->route('restaurant');
                    $branchSlug = request()->route('branch');
                @endphp
                <div class="premium-head-actions">
                    @if (auth()->user()->role === 'super_admin')
                        <a href="{{ route('customer-offers.index') }}" class="premium-back-btn">
                            <i class="fas fa-arrow-left"></i>
                            Back To Offers
                        </a>
                    @elseif(!empty($restaurantSlug) && !empty($branchSlug))
                        <a href="{{ route('branch.customer-offers.index', [
                                'restaurant' => $restaurantSlug,
                                'branch' => $branchSlug,
                            ]) }}" class="premium-back-btn">
                            <i class="fas fa-arrow-left"></i>
                            Back To Offers
                        </a>
                    @elseif(!empty($restaurantSlug))
                        <a href="{{ route('restaurant.customer-offers.index', [
                            'restaurant' => $restaurantSlug,
                            ]) }}" class="premium-back-btn">
                            <i class="fas fa-arrow-left"></i>
                            Back To Offers
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </section>    
    <div class="card">
        <div class="card-header">
            <h4>Create Customer Offer</h4>
        </div>
        <div class="card-body"> 
            <form method="POST" action="{{ route('customer-offers.store') }}">
                @csrf
                <div class="row">
                <div class="col-lg-4 mb-3">
                    <label class="form-label">Category</label>
                    <select name="category" class="premium-input" value="{{ old('category') }}">
                        <option value="">Select Category</option>
                        <option value="birthday">Birthday</option>
                        <option value="anniversary">Anniversary</option>
                        <option value="other">Other</option>
                    </select>
                    @error('category')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
                <div class="col-lg-8 mb-3">
                    <label>Title</label>
                    <input type="text" name="title" class="premium-input" value="{{ old('title') }}">
                    @error('title')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
                <div class="col-lg-12 mb-3">
                    <label class="form-label">Description</label>
                    <textarea name="description" id="description" class="premium-input">{{ old('description') }}</textarea>
                    @error('description')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>   
                </div>                  
                <div class="premium-card-footer">
                    <button  class="premium-btn btn-primary"> <i class="fas fa-plus-circle"></i>
                        Save Offer
                    </button>
                </div>     
                
            </form>
        </div>
    </div>
@endsection
