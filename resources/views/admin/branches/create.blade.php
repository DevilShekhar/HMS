@can('create-branch')

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
                                Branch Managermebt
                            </span>
                            <h1>Create Branch</h1>
                            <p>Add a new Branch to the system with basic information.</p>
                        </div>
                    </div>

                <div class="header-right">
                    <a href="{{ route('branches.index') }}" class="premium-back-btn">
                        <i class="fas fa-arrow-left"></i>
                        Back to Branches
                    </a>
                </div>
                </div>
            </div>
        </section>
        <section class="section premium-dashboard pt-0">
            @if (auth()->user()->role == 'super_admin')
                <form action="{{ route('branches.store') }}" method="POST">
            @else
                    <form action="{{ route('restaurant.branches.store', ['restaurant' => request()->route('restaurant')]) }}"
                        method="POST">
                @endif
                    @csrf
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card premium-block">
                                <div class="card-header premium-card-header">
                                    <div>
                                        <h4>Branch Information</h4>
                                        <p class="header-subtext">Enter branch details.</p>
                                    </div>
                                </div>

                                <div class="card-body">

                                    <div class="row">

                                        <div class="col-lg-3 col-md-6 mb-4">
                                            <label>Restaurant</label>
                                            <select name="restaurant_id" class="form-control premium-input">
                                                <option value="">Select Restaurant</option>
                                                @foreach ($restaurants as $restaurant)
                                                    <option value="{{ $restaurant->id }}">
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
                                                    <option value="{{ $owner->id }}">
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
                                            <input type="text" name="name" class="form-control premium-input">
                                            @error('name')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>

                                        <div class="col-lg-3 col-md-6 mb-4">
                                            <label>Branch Code</label>
                                            <input type="text" name="code" class="form-control premium-input">
                                            @error('code')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>

                                        <div class="col-lg-3 col-md-6 mb-4">
                                            <label>Phone</label>
                                            <input type="text" name="phone" class="form-control premium-input">
                                            @error('phone')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>

                                        <div class="col-lg-3 col-md-6 mb-4">
                                            <label>Email</label>
                                            <input type="email" name="email" class="form-control premium-input">
                                            @error('email')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>

                                        <div class="col-lg-3 col-md-6 mb-4">
                                            <label>City</label>
                                            <input type="text" name="city" class="form-control premium-input">
                                            @error('city')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>

                                        <div class="col-lg-3 col-md-6 mb-4">
                                            <label>State</label>
                                            <input type="text" name="state" class="form-control premium-input">
                                            @error('state')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>

                                        <div class="col-lg-3 col-md-6 mb-4">
                                            <label>Country</label>
                                            <input type="text" name="country" class="form-control premium-input">
                                            @error('country')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>

                                        <div class="col-lg-3 col-md-6 mb-4">
                                            <label>Postal Code</label>
                                            <input type="text" name="postal_code" class="form-control premium-input">
                                            @error('postal_code')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>

                                        <div class="col-lg-3 col-md-6 mb-4">
                                            <label>GST Number</label>
                                            <input type="text" name="gst_number" class="form-control premium-input">
                                            @error('gst_number')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                        <div class="col-lg-3 col-md-6 mb-4">
                                            <label>FSSAI License</label>
                                            <input type="text" name="fssai_license" class="form-control premium-input">
                                            @error('fssai_license')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>

                                        <div class="col-lg-3 col-md-6 mb-4">
                                            <label>Opening Time</label>
                                            <input type="time" name="opening_time" class="form-control premium-input">
                                            @error('opening_time')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>

                                        <div class="col-lg-3 col-md-6 mb-4">
                                            <label>Closing Time</label>
                                            <input type="time" name="closing_time" class="form-control premium-input">
                                            @error('closing_time')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>

                                        <div class="col-12 mb-4">
                                            <label>Address</label>
                                            <textarea name="address" rows="3" class="form-control premium-input"></textarea>
                                            @error('address')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
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
                                                            id="plan_{{ $plan->id }}" {{ $loop->first ? 'checked' : '' }}>

                                                        <label class="form-check-label fw-bold" for="plan_{{ $plan->id }}">

                                                            {{ $plan->name }}

                                                        </label>
                                                    </div>

                                                    <div class="price-box mt-3">

                                                        <h3 class="plan-price">
                                                            ₹{{ number_format($plan->monthly_price, 2) }}
                                                        </h3>

                                                        <span>Per Month</span>

                                                    </div>

                                                    <div class="mt-3">
                                                        {!! $plan->description !!}
                                                    </div>

                                                    <div class="mt-3">

                                                        <select class="form-control duration-select" data-plan-id="{{ $plan->id }}"
                                                            style="{{ !$loop->first ? 'display:none' : '' }}">

                                                            <option value="monthly" data-price="{{ $plan->monthly_price }}">
                                                                Monthly - ₹{{ $plan->monthly_price }}
                                                            </option>

                                                            <option value="quarterly" data-price="{{ $plan->quarterly_price }}">
                                                                Quarterly - ₹{{ $plan->quarterly_price }}
                                                            </option>

                                                            <option value="half_yearly" data-price="{{ $plan->half_yearly_price }}">
                                                                Half Yearly - ₹{{ $plan->half_yearly_price }}
                                                            </option>

                                                            <option value="yearly" data-price="{{ $plan->yearly_price }}">
                                                                Yearly - ₹{{ $plan->yearly_price }}
                                                            </option>

                                                        </select>

                                                    </div>

                                                </div>

                                            </div>
                                        @endforeach

                                    </div>

                                    <input type="hidden" name="billing_cycle" id="billing_cycle" value="monthly">

                                </div>
                            </div>
                            <div class="mt-4">
                                <button type="submit" class="btn btn-primary">Create Branch </button>
                            </div>
                        </div>
                    </div>
                </form>
        </section>
        <script>
            const existingCombinations = @json($existingCombinations);

            $('#restaurant_id, #owner_id').on('change', function () {

                const restaurantId = $('#restaurant_id').val();
                const ownerId = $('#owner_id').val();

                $('#ownerError').text('');

                if (!restaurantId || !ownerId) return;

                const exists = existingCombinations.some(item =>
                    item.restaurant_id == restaurantId &&
                    item.owner_id == ownerId
                );

                if (exists) {
                    $('#ownerError').text('This owner is already assigned to the selected restaurant.');
                }
            });
        </script>
    @endsection
@else
    @php
        abort(403);
    @endphp
@endcan
