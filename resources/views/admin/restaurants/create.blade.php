@extends('layouts.app')
@section('content')
<section class="section premium-dashboard">
    <div class="premium-page-head">
        <div class="premium-page-title">
            <span class="mini-badge">
                Restaurant Management
            </span>
            <h2>Create Restaurant</h2>
            <p> Add a new restaurant to the system. </p>
        </div>
        <div class="premium-head-actions">
            <a href="{{ route('restaurants.index') }}" class="btn premium-btn ghost-btn">
                <i class="fas fa-arrow-left"></i>
                Back To Restaurants
            </a>
        </div>
    </div>
</section>
<section class="section premium-dashboard pt-0">
    <form action="{{ route('restaurants.store') }}" method="POST">
        @csrf
        <div class="row">
            <div class="col-lg-12">
                <div class="card premium-block">
                    <div class="card-header premium-card-header">
                        <div>
                            <h4>Restaurant Information</h4>
                            <p class="header-subtext"> Enter restaurant details. </p>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label>Restaurant Name</label>
                                <input type="text" name="name" value="{{ old('name') }}"  class="form-control premium-input">
                                @error('name')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>                              
                        </div>
                    </div>
                </div>
                <div class="mt-4">
                    <a href="{{ route('restaurants.index') }}"  class="btn btn-light">
                        Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        Create Restaurant
                    </button>
                </div>
            </div>
        </div>
    </form>
</section>
@endsection