@extends('layouts.app')

@section('content')
    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/eht-users.css') }}">
    @endpush

    <div class="eht-container">
        <!-- Header -->
        <div class="eht-header">
            <div class="eht-page-title">
                <span class="eht-badge">
                    <i class="fas fa-crown"></i> Subscription Management
                </span>
                <h1>Edit Subscription Plan</h1>
                <p>Update subscription plan details</p>
            </div>

            <div class="eht-back-btn">
                <a href="{{ route('subscription-plans.index') }}" class="eht-btn eht-btn-ghost">
                    <i class="fas fa-arrow-left"></i> Back to Plans
                </a>
            </div>
        </div>

        <div class="eht-form-wrapper">
            <form method="POST" action="{{ route('subscription-plans.update', $subscriptionPlan) }}">
                @csrf
                @method('PUT')

                <div class="eht-main-card">
                    <div class="eht-card-body">
                        <div class="eht-form-grid">

                            <!-- Left: Form Fields -->
                            <div class="eht-left-form">
                                <h3 class="section-title">
                                    <i class="fas fa-crown" style="color: #FA5603;"></i> Plan Information
                                </h3>
                                <p class="eht-subtext">Update subscription plan details.</p>

                                <div class="eht-fields-grid">
                                    <!-- Plan Name -->
                                    <div class="eht-field full-width">
                                        <label>Plan Name <span class="required">*</span></label>
                                        <input type="text" name="name" value="{{ old('name', $subscriptionPlan->name ?? '') }}"
                                               class="eht-input @error('name') is-invalid @enderror"
                                               placeholder="Enter plan name">
                                        @error('name') <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>

                                    <!-- Max Branches -->
                                    <div class="eht-field full-width">
                                        <label>Max Branches <span class="required">*</span></label>
                                        <input type="number" name="max_branches" value="{{ old('max_branches', $subscriptionPlan->max_branches ?? 1) }}"
                                               class="eht-input @error('max_branches') is-invalid @enderror"
                                               placeholder="Enter max branches" min="1">
                                        @error('max_branches') <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>

                                    <!-- Description -->
                                    <div class="eht-field full-width">
                                        <label>Description</label>
                                        <textarea name="description" id="description" rows="4"
                                                  class="eht-input @error('description') is-invalid @enderror"
                                                  placeholder="Enter plan description">{{ old('description', $subscriptionPlan->description ?? '') }}</textarea>
                                        @error('description') <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>

                                    <!-- Pricing Row -->
                                    <div class="eht-field">
                                        <label>Monthly Price <span class="required">*</span></label>
                                        <input type="number" step="0.01" name="monthly_price" value="{{ old('monthly_price', $subscriptionPlan->monthly_price ?? 0) }}"
                                               class="eht-input @error('monthly_price') is-invalid @enderror"
                                               placeholder="0.00">
                                        @error('monthly_price') <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>

                                    <div class="eht-field">
                                        <label>Quarterly Price</label>
                                        <input type="number" step="0.01" name="quarterly_price" value="{{ old('quarterly_price', $subscriptionPlan->quarterly_price ?? '') }}"
                                               class="eht-input @error('quarterly_price') is-invalid @enderror"
                                               placeholder="0.00">
                                        @error('quarterly_price') <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>

                                    <div class="eht-field">
                                        <label>Half Yearly Price</label>
                                        <input type="number" step="0.01" name="half_yearly_price" value="{{ old('half_yearly_price', $subscriptionPlan->half_yearly_price ?? '') }}"
                                               class="eht-input @error('half_yearly_price') is-invalid @enderror"
                                               placeholder="0.00">
                                        @error('half_yearly_price') <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>

                                    <div class="eht-field">
                                        <label>Yearly Price</label>
                                        <input type="number" step="0.01" name="yearly_price" value="{{ old('yearly_price', $subscriptionPlan->yearly_price ?? '') }}"
                                               class="eht-input @error('yearly_price') is-invalid @enderror"
                                               placeholder="0.00">
                                        @error('yearly_price') <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>

                                    <!-- Status - Full Width -->
                                    <div class="eht-field full-width">
                                        <div class="eht-checkbox-wrapper">
                                            <input type="checkbox" name="status" value="1" class="eht-checkbox"
                                                   id="statusCheck" {{ old('status', $subscriptionPlan->is_active ?? true) ? 'checked' : '' }}>
                                            <label class="eht-checkbox-label" for="statusCheck">
                                                <i class="fas fa-check-circle" style="color: #27ae60;"></i> Active
                                            </label>
                                        </div>
                                        <small class="eht-help-text">Enable this plan for subscriptions</small>
                                    </div>
                                </div>
                            </div>

                            <!-- Right: Quick Info -->
                            <div class="eht-profile-section">
                                <h3 class="section-title">
                                    <i class="fas fa-info-circle" style="color: #FA5603;"></i> Quick Info
                                </h3>
                                <div class="eht-info-box">
                                    <div class="eht-info-item">
                                        <i class="fas fa-tag"></i>
                                        <div>
                                            <strong>Pricing Notes</strong>
                                            <p>Set prices for different billing cycles</p>
                                        </div>
                                    </div>

                                    <div class="eht-info-item">
                                        <i class="fas fa-check-circle" style="color: #27ae60;"></i>
                                        <div>
                                            <strong>Status</strong>
                                            <p>Plans can be active or inactive</p>
                                        </div>
                                    </div>

                                    <div class="eht-info-item">
                                        <i class="fas fa-clock" style="color: #f39c12;"></i>
                                        <div>
                                            <strong>Flexible Billing</strong>
                                            <p>Monthly, Quarterly, Half-Yearly, Yearly</p>
                                        </div>
                                    </div>

                                    <div class="eht-info-item">
                                        <i class="fas fa-store" style="color: #FA5603;"></i>
                                        <div>
                                            <strong>Branches</strong>
                                            <p>Max {{ $subscriptionPlan->max_branches ?? 1 }} branch(es) allowed</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="eht-form-footer">
                        <button type="submit" class="eht-btn eht-btn-primary eht-btn-large">
                            <i class="fas fa-save"></i> Update Plan
                        </button>
                        <a href="{{ route('subscription-plans.index') }}" class="eht-btn eht-btn-ghost">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function() {
                // Form validation
                $('form').on('submit', function(e) {
                    let isValid = true;
                    $(this).find('.eht-input').each(function() {
                        if ($(this).prop('required') && !$(this).val()) {
                            $(this).addClass('is-invalid');
                            isValid = false;
                        } else {
                            $(this).removeClass('is-invalid');
                        }
                    });

                    if (!isValid) {
                        e.preventDefault();
                        $('html, body').animate({
                            scrollTop: $('.is-invalid:first').offset().top - 100
                        }, 500);
                    }
                });

                // Clear validation on input
                $('.eht-input').on('input change', function() {
                    $(this).removeClass('is-invalid');
                });
            });
        </script>
    @endpush
@endsection
