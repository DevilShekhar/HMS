@extends('layouts.app')
@section('content')
    <section class="section premium-dashboard">
        <div class="premium-floating-header">
            <div class="header-content">
                <div class="header-left">
                    <div class="header-icon">
                        <i class="fas fa-edit"></i>
                    </div>
                    <div>
                        <span class="header-badge">
                            Restaurant Management
                        </span>
                        <h1>Edit Restaurant</h1>
                        <p>Update restaurant information and manage restaurant status.</p>
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
                <form action="{{ route('restaurants.update', $restaurant->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="premium-card">
                        <div class="premium-card-header">
                            <div class="card-title-group">
                                <div>
                                    <h3>Restaurant Information</h3>
                                    <p>Update restaurant details below.</p>
                                </div>
                            </div>
                        </div>
                        <div class="premium-card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="premium-form-group">
                                        <label class="premium-label">
                                            Restaurant Name
                                            <span>*</span>
                                        </label>
                                        <input type="text" name="name" value="{{ old('name', $restaurant->name) }}" class="premium-input @error('name') input-error @enderror" placeholder="Enter restaurant name">
                                        @error('name')
                                            <small class="premium-error">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="premium-form-group">
                                        <label class="premium-label">
                                            Status
                                        </label>
                                        <select name="status" class="premium-input">
                                            <option value="1" {{ $restaurant->status == 1 ? 'selected' : '' }}> Active</option>
                                            <option value="0" {{ $restaurant->status == 0 ? 'selected' : '' }}> Inactive</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="premium-form-group">
                                        <label class="premium-label">
                                            Restaurant URL
                                        </label>
                                        <input type="text" readonly value="{{ url($restaurant->slug) }}" class="premium-input" placeholder="Restaurant URL">
                                        <small class="text-muted mt-2 d-block">
                                            This URL is automatically generated.
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="premium-card-footer">
                            <a href="{{ route('restaurants.index') }}" class="premium-btn btn-outline">
                                <i class="fas fa-arrow-left"></i>
                                Cancel
                            </a>
                            <button type="submit" class="premium-btn btn-primary">
                                <i class="fas fa-save"></i>
                                Update Restaurant
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection