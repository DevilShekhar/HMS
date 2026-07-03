@can('create-branch')

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
                    @if (auth()->user()->role == 'super_admin')
                        <a href="{{ route('branches.index') }}" class="btn premium-btn ghost-btn"> <i class="fas fa-arrow-left"></i>
                            Back To Branches</a>
                    @else
                            <a href="{{ route('restaurant.branches.index', [
                            'restaurant' => request()->route('restaurant'),
                        ]) }}" class="btn premium-btn ghost-btn">
                                <i class="fas fa-arrow-left"></i>
                                Back To Branches
                            </a>
                    @endif
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
                                        {{-- GST Enable Checkbox --}}
                                        <div class="col-lg-3 col-md-6 mb-4">
                                            <label class="form-label">Enable GST</label>
                                            <div class="form-check form-switch mt-2">
                                                <input class="form-check-input" type="checkbox" id="gst_enabled"
                                                    name="gst_enabled" value="1" {{ old('gst_enabled') ? 'checked' : '' }}>
                                                <label class="form-check-label" for="gst_enabled">
                                                    Apply GST
                                                </label>
                                            </div>
                                            @error('gst_enabled')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>

                                        {{-- GST Section --}}
                                        <div id="gstSection" style="{{ old('gst_enabled') ? '' : 'display: none;' }}">
                                            <div class="row">
                                                <div class="col-lg-3 mb-4">
                                                    <label>GST %</label>
                                                    <input type="number" step="0.01" name="gst" id="gst"
                                                        value="{{ old('gst') }}" class="form-control">
                                                </div>
                                                <div class="col-lg-3 mb-4">
                                                    <label>CGST %</label>
                                                    <input type="number" id="cgst" class="form-control" readonly>
                                                </div>
                                                <div class="col-lg-3 mb-4">
                                                    <label>SGST %</label>
                                                    <input type="number" id="sgst" class="form-control" readonly>
                                                </div>
                                            </div>
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
            document.addEventListener("DOMContentLoaded", function () {
                const toggle = document.getElementById("gst_enabled");
                const section = document.getElementById("gstSection");
                const gstInput = document.getElementById("gst");
                const cgstInput = document.getElementById("cgst");
                const sgstInput = document.getElementById("sgst");

                function calculateGST() {
                    const value = parseFloat(gstInput.value);
                    if (isNaN(value) || value <= 0) {
                        cgstInput.value = '';
                        sgstInput.value = '';
                        return;
                    }
                    const half = (value / 2).toFixed(2);
                    cgstInput.value = half;
                    sgstInput.value = half;
                }

                function toggleGSTSection() {
                    if (toggle.checked) {
                        section.style.display = "block";
                    } else {
                        section.style.display = "none";
                        gstInput.value = '';
                        cgstInput.value = '';
                        sgstInput.value = '';
                    }
                }

                // Initial state
                toggleGSTSection();
                calculateGST();

                // Event listeners
                toggle.addEventListener("change", toggleGSTSection);
                gstInput.addEventListener("input", calculateGST);
                gstInput.addEventListener("change", calculateGST);
            });
        </script>
    @endsection
@else
    @php
        abort(403);
    @endphp
@endcan
