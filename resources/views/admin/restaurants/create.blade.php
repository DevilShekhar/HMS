@extends('layouts.app')
@section('content')
<section class="section premium-dashboard">
    <div class="premium-floating-header">
    <div class="header-content">
        <div class="header-left">
            <div class="header-icon">
                <i class="fas fa-store"></i>
            </div>
            <div>
                <span class="header-badge">
                    Restaurant Management
                </span>
                <h1>Create Restaurant</h1>
                <p>Add a new restaurant to the system with basic information.</p>
            </div>
        </div>
        <div class="header-right">
            <a href="{{ route('restaurants.index') }}" class="premium-back-btn">
                <i class="fas fa-arrow-left"></i>
                Back to Restaurants
            </a>
        </div>
    </div>
</div>
</section>
<section class="section premium-dashboard pt-0">
    <div class="row justify-content-center">
        <div class="col-xl-8 col-lg-9 col-md-11">
            <form action="{{ route('restaurants.store') }}" method="POST">
                @csrf
                <div class="premium-card">
                    <div class="premium-card-header">
                        <div class="card-title-group">                        
                            <div>
                                <h3>Restaurant Information</h3>
                                <p>Enter restaurant details below.</p>
                            </div>
                        </div>
                    </div>
                    <div class="premium-card-body">
                        <div class="premium-form-group">
                            <label class="premium-label">
                                Restaurant Name
                                <span>*</span>
                            </label>
                            <input type="text" name="name" value="{{ old('name') }}" class="premium-input @error('name') input-error @enderror" placeholder="Enter restaurant name">
                            @error('name')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>                           
                    </div>
                    <div class="premium-card-footer">
                        <a href="{{ route('restaurants.index') }}" class="premium-btn btn-outline">
                            <i class="fas fa-arrow-left"></i>Cancel
                        </a>
                        <button type="submit" class="premium-btn btn-primary"> <i class="fas fa-plus-circle"></i>
                            Create Restaurant
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>
@endsection
