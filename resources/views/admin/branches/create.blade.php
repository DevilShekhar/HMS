@extends('layouts.app')
@section('content')
<section class="section premium-dashboard">
    <div class="premium-page-head">
        <div class="premium-page-title">
            <span class="mini-badge">Branch Management</span>
            <h2>Create Branch</h2>
            <p>Add a new restaurant branch.</p>
        </div>
        <div class="premium-head-actions">
            @if(auth()->user()->role == 'super_admin')
                <a href="{{ route('branches.index') }}" class="btn premium-btn ghost-btn"> <i class="fas fa-arrow-left"></i> Back To Branches</a>
            @else
                <a href="{{ route('restaurant.branches.index',[
                    'restaurant' => request()->route('restaurant')
                ]) }}"
                   class="btn premium-btn ghost-btn">
                    <i class="fas fa-arrow-left"></i>
                    Back To Branches
                </a>
            @endif
        </div>
    </div>
</section>
<section class="section premium-dashboard pt-0">
    @if(auth()->user()->role == 'super_admin')
        <form action="{{ route('branches.store') }}" method="POST">
    @else
        <form action="{{ route('restaurant.branches.store',['restaurant' => request()->route('restaurant')]) }}" method="POST">
    @endif
    @csrf
        <div class="row">
            <div class="col-lg-12">
                <div class="card premium-block">
                    <div class="card-header premium-card-header">
                        <div>
                            <h4>Branch Information</h4>
                            <p class="header-subtext"> Enter branch details.</p>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label>Restaurant</label>
                                <select name="restaurant_id" id="restaurant_id" class="form-control premium-input">
                                    <option value="">  Select Restaurant </option>
                                    @foreach($restaurants as $restaurant)
                                        <option value="{{ $restaurant->id }}"
                                            {{ old('restaurant_id') == $restaurant->id ? 'selected' : '' }}>
                                            {{ $restaurant->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('restaurant_id')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-4">
                                <label>Owner</label>
                                <select name="owner_id" id="owner_id" class="form-control premium-input">
                                    <option value="">Select Owner</option>
                                    @foreach($owners as $owner)
                                        <option value="{{ $owner->id }}">{{ $owner->name }}</option>
                                    @endforeach
                                </select>
                                @error('owner_id')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>                  
                            <div class="col-md-6 mb-4">
                                <label>Branch Name</label>
                                <input type="text" name="name" value="{{ old('name') }}" class="form-control premium-input">
                                @error('name')                                   
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-4">
                                <label>Branch Code</label>
                                <input type="text" name="code" value="{{ old('code') }}" class="form-control premium-input">
                                @error('code')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-4">
                                <label>Phone</label>
                                <input type="text"  name="phone" value="{{ old('phone') }}"  class="form-control premium-input">
                                @error('phone')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-4">
                                <label>Email</label>
                                <input type="email" name="email" value="{{ old('email') }}" class="form-control premium-input">
                                @error('email')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-4">
                                <label>City</label>
                                <input type="text" name="city" value="{{ old('city') }}" class="form-control premium-input">
                            </div>
                            <div class="col-md-6 mb-4">
                                <label>State</label>
                                <input type="text" name="state" value="{{ old('state') }}"  class="form-control premium-input">
                            </div>
                            <div class="col-md-6 mb-4">
                                <label>Country</label>
                                <input type="text" name="country" value="{{ old('country') }}" class="form-control premium-input">
                            </div>
                            <div class="col-md-6 mb-4">
                                <label>Postal Code</label>
                                <input type="text" name="postal_code"value="{{ old('postal_code') }}" class="form-control premium-input">
                            </div>
                            <div class="col-md-6 mb-4">
                                <label>GST Number</label>
                                <input type="text" name="gst_number"  value="{{ old('gst_number') }}" class="form-control premium-input">
                            </div>
                            <div class="col-md-6 mb-4">
                                <label>FSSAI License</label>
                                <input type="text" name="fssai_license" value="{{ old('fssai_license') }}" class="form-control premium-input">
                            </div>
                            <div class="col-md-6 mb-4">
                                <label>Opening Time</label>
                                <input type="time" name="opening_time" class="form-control premium-input">
                            </div>
                            <div class="col-md-6 mb-4">
                                <label>Closing Time</label>
                                <input type="time" name="closing_time" class="form-control premium-input">
                            </div>
                            <div class="col-md-12 mb-4">
                                <label>Address</label>
                                <textarea name="address" rows="4" class="form-control premium-input">{{ old('address') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">Create Branch </button>
                </div>
            </div>
        </div>
    </form>
</section>
@endsection