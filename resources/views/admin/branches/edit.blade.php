@extends('layouts.app')
@section('content')
    <section class="section premium-dashboard">
        <div class="premium-page-head">
            <div class="premium-page-title">
                <span class="mini-badge">Branch Management</span>
                <h2>Edit Branch</h2>
                <p>Update branch information.</p>
            </div>
            <div class="premium-head-actions">
                @if (auth()->user()->role == 'super_admin')
                    <a href="{{ route('branches.index') }}" class="btn premium-btn ghost-btn">
                        <i class="fas fa-arrow-left"></i>
                        Back To Branches
                    </a>
                @else
                    <a href="{{ route('restaurant.branches.index', [
                        'restaurant' => request()->route('restaurant'),
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
        @if (auth()->user()->role == 'super_admin')
            <form action="{{ route('branches.update', $branch->id) }}" method="POST">
            @else
                <form
                    action="{{ route('restaurant.branches.update', ['restaurant' => request()->route('restaurant'), 'branch' => $branch->id]) }}"method="POST">
        @endif
        @csrf
        @method('PUT')
        <div class="row">
            <div class="col-lg-12">
                <div class="card premium-block">
                    <div class="card-header premium-card-header">
                        <div>
                            <h4>Branch Information</h4>
                            <p class="header-subtext"> Update branch details.</p>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-3 col-md-6 mb-4">
                                <label>Restaurant</label>
                                <select name="restaurant_id" class="form-control premium-input">
                                    <option value="">Select Restaurant</option>
                                    @foreach ($restaurants as $restaurant)
                                        <option value="{{ $restaurant->id }}"
                                            {{ old('restaurant_id', $branch->restaurant_id) == $restaurant->id ? 'selected' : '' }}>
                                            {{ $restaurant->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('restaurant_id')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-lg-3 col-md-6 mb-4">
                                <label>Owner</label>
                                <select name="owner_id" class="form-control premium-input">
                                    <option value="">Select Owner</option>
                                    @foreach ($owners as $owner)
                                        <option value="{{ $owner->id }}"
                                            {{ old('owner_id', $branch->owner_id) == $owner->id ? 'selected' : '' }}>
                                            {{ $owner->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('owner_id')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-lg-3 col-md-6 mb-4">
                                <label>Branch Name</label>
                                <input type="text" name="name"value="{{ old('name', $branch->name) }}"
                                    class="form-control premium-input">
                                @error('name')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-lg-3 col-md-6 mb-4">
                                <label>Branch Code</label>
                                <input type="text" name="code" value="{{ old('code', $branch->code) }}"
                                    class="form-control premium-input">
                            </div>
                            <div class="col-lg-3 col-md-6 mb-4">
                                <label>Phone</label>
                                <input type="text" name="phone" value="{{ old('phone', $branch->phone) }}"
                                    class="form-control premium-input">
                            </div>
                            <div class="col-lg-3 col-md-6 mb-4">
                                <label>Email</label>
                                <input type="email" name="email" value="{{ old('email', $branch->email) }}"
                                    class="form-control premium-input">
                            </div>
                            <div class="col-lg-3 col-md-6 mb-4">
                                <label>City</label>
                                <input type="text" name="city" value="{{ old('city', $branch->city) }}"
                                    class="form-control premium-input">
                            </div>
                            <div class="col-lg-3 col-md-6 mb-4">
                                <label>State</label>
                                <input type="text" name="state" value="{{ old('state', $branch->state) }}"
                                    class="form-control premium-input">
                            </div>
                            <div class="col-lg-3 col-md-6 mb-4">
                                <label>Country</label>
                                <input type="text"ame="country" value="{{ old('country', $branch->country) }}"
                                    class="form-control premium-input">
                            </div>
                            <div class="col-lg-3 col-md-6 mb-4">
                                <label>Postal Code</label>
                                <input type="text" name="postal_code"
                                    value="{{ old('postal_code', $branch->postal_code) }}"
                                    class="form-control premium-input">
                            </div>
                            <div class="col-lg-3 col-md-6 mb-4">
                                <label>GST Number</label>
                                <input type="text" name="gst_number" value="{{ old('gst_number', $branch->gst_number) }}"
                                    class="form-control premium-input">
                            </div>
                            <div class="col-lg-3 col-md-6 mb-4">
                                <label>FSSAI License</label>
                                <input type="text" name="fssai_license"
                                    value="{{ old('fssai_license', $branch->fssai_license) }}"
                                    class="form-control premium-input">
                            </div>
                            <div class="col-lg-3 col-md-6 mb-4">
                                <label>Opening Time</label>
                                <input type="time" name="opening_time"
                                    value="{{ old('opening_time', $branch->opening_time) }}"
                                    class="form-control premium-input">
                            </div>
                            <div class="col-lg-3 col-md-6 mb-4">
                                <label>Closing Time</label>
                                <input type="time" name="closing_time"
                                    value="{{ old('closing_time', $branch->closing_time) }}"
                                    class="form-control premium-input">
                            </div>
                            <div class="col-lg-3 col-md-6 mb-4">
                                <label>Status</label>
                                <select name="is_active" class="form-control premium-input">
                                    <option value="1"
                                        {{ old('is_active', $branch->is_active) == 1 ? 'selected' : '' }}>
                                        Active
                                    </option>
                                    <option value="0"
                                        {{ old('is_active', $branch->is_active) == 0 ? 'selected' : '' }}>
                                        Inactive
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-12 mb-4">
                                <label>Address</label>
                                <textarea name="address" rows="4" class="form-control premium-input">{{ old('address', $branch->address) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card premium-block mt-4">
                    <div class="card-header premium-card-header">
                        <div>
                            <h4>Subscription Plan</h4>
                            <p class="header-subtext">
                                Select plan and billing cycle.
                            </p>
                        </div>
                    </div>

                    <div class="card-body">

                        <div class="row">

                            @foreach ($plans as $plan)
                                <div class="col-lg-4 mb-4">

                                    <div class="plan-card">

                                        <div class="form-check">

                                            <input class="form-check-input plan-radio" type="radio"
                                                name="subscription_plan_id" value="{{ $plan->id }}"
                                                id="plan_{{ $plan->id }}"
                                                {{ old('subscription_plan_id', optional($currentSubscription)->subscription_plan_id) == $plan->id
                                                    ? 'checked'
                                                    : '' }}>

                                            <label class="form-check-label fw-bold" for="plan_{{ $plan->id }}">

                                                {{ $plan->name }}

                                            </label>

                                        </div>

                                        <div class="price-box mt-3">

                                            <h3 class="plan-price">
                                                ₹{{ number_format($plan->monthly_price, 2) }}
                                            </h3>

                                            <span class="plan-cycle">
                                                Per Month
                                            </span>

                                        </div>

                                        <div class="mt-3">
                                            {!! $plan->description !!}
                                        </div>

                                        <div class="mt-3">

                                            <select class="form-control duration-select"
                                                data-plan-id="{{ $plan->id }}"
                                                style="{{ old('subscription_plan_id', optional($currentSubscription)->subscription_plan_id) == $plan->id
                                                    ? ''
                                                    : 'display:none' }}"
                                                name="{{ old('subscription_plan_id', optional($currentSubscription)->subscription_plan_id) == $plan->id
                                                    ? 'billing_cycle'
                                                    : '' }}">

                                                <option value="monthly" data-price="{{ $plan->monthly_price }}"
                                                    {{ optional($currentSubscription)->billing_cycle == 'monthly' ? 'selected' : '' }}>
                                                    Monthly - ₹{{ $plan->monthly_price }}
                                                </option>

                                                <option value="quarterly" data-price="{{ $plan->quarterly_price }}"
                                                    {{ optional($currentSubscription)->billing_cycle == 'quarterly' ? 'selected' : '' }}>
                                                    Quarterly - ₹{{ $plan->quarterly_price }}
                                                </option>

                                                <option value="half_yearly" data-price="{{ $plan->half_yearly_price }}"
                                                    {{ optional($currentSubscription)->billing_cycle == 'half_yearly' ? 'selected' : '' }}>
                                                    Half Yearly - ₹{{ $plan->half_yearly_price }}
                                                </option>

                                                <option value="yearly" data-price="{{ $plan->yearly_price }}"
                                                    {{ optional($currentSubscription)->billing_cycle == 'yearly' ? 'selected' : '' }}>
                                                    Yearly - ₹{{ $plan->yearly_price }}
                                                </option>

                                            </select>

                                        </div>

                                    </div>

                                </div>
                            @endforeach

                        </div>

                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary"> Update Branch </button>
                </div>
            </div>
        </div>
        </form>
    </section>
@endsection
