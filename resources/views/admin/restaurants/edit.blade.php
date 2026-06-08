@extends('layouts.app')
@section('content')
<section class="section premium-dashboard">
    <div class="premium-page-head">
        <div class="premium-page-title">
            <span class="mini-badge">  Restaurant Management </span>
            <h2>Edit Restaurant</h2>
            <p>Update restaurant information. </p>
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
    <form action="{{ route('restaurants.update', $restaurant->id) }}" method="POST">
    @csrf
    @method('PUT')
    <div class="row">
        <div class="col-lg-12">
            <div class="card premium-block">
                <div class="card-header premium-card-header">
                    <div>
                        <h4>Restaurant Information</h4>
                        <p class="header-subtext">
                            Update restaurant details.
                        </p>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label>Restaurant Name</label>
                            <input type="text" name="name" value="{{ old('name', $restaurant->name) }}" class="form-control premium-input">
                            @error('name')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                        </div>                  
                        <div class="col-md-6 mb-4">
                            <label>Status</label>
                            <select name="status"class="form-control premium-input">
                                <option value="1"{{ $restaurant->status == 1 ? 'selected' : '' }}> Active </option>
                                <option value="0" {{ $restaurant->status == 0 ? 'selected' : '' }}> Inactive </option>
                            </select>
                        </div>
                        <div class="col-md-12 mb-4">
                            <label>Restaurant URL</label>
                            <input type="text" readonly  value="{{ url($restaurant->slug) }}" class="form-control premium-input">
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-4">
                <a href="{{ route('restaurants.index') }}" class="btn btn-light">
                    Cancel
                </a>
                <button type="submit" class="btn btn-primary">
                    Update Restaurant
                </button>
            </div>
        </div>   
    </div>
    </form>
</section>
@endsection
