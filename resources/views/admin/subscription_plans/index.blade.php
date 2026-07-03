@can('view-subscription')
    @extends('layouts.app')

    @section('content')

        <div class="eht-container">
            <!-- Header -->
            <div class="eht-header">
                <div class="eht-page-title">
                    <span class="eht-badge">
                        <i class="fas fa-crown"></i> Subscription Management
                    </span>
                    <h1>Subscription Plans</h1>
                    <p>Manage all subscription plans from API server</p>
                </div>

                    <a href="{{ route('subscription-plans.create') }}" class="eht-btn eht-btn-primary">
                        <i class="fas fa-plus"></i> Create New Plan
                    </a>
            </div>

            <!-- Plans Grid -->
            <div class="row g-4">
                @forelse ($plans as $plan)
                    <div class="col-xl-4 col-md-6">
                        <div class="eht-plan-card {{ $loop->iteration == 2 ? 'eht-popular' : '' }}">
                            @if ($loop->iteration == 2)
                                <div class="eht-popular-badge">
                                    <i class="fas fa-star"></i> Most Popular
                                </div>
                            @endif

                            <div class="eht-plan-body">
                                <!-- Plan Name -->
                                <div class="text-center">
                                    <h3 class="eht-plan-name">{{ $plan->name }}</h3>
                                    <span class="eht-plan-branch-badge">
                                        <i class="fas fa-store"></i> {{ $plan->max_branches }} Branch(es)
                                    </span>
                                </div>

                                <!-- Price -->
                                <div class="eht-plan-price-wrapper">
                                    <span class="eht-plan-currency">₹</span>
                                    <span class="eht-plan-price">{{ number_format($plan->monthly_price, 2) }}</span>
                                    <span class="eht-plan-period">/month</span>
                                    @if ($plan->yearly_price)
                                        <div class="eht-plan-savings">
                                            <i class="fas fa-tag"></i> Save 20% with annual billing
                                        </div>
                                    @endif
                                </div>

                                <!-- Description -->
                                <div class="eht-plan-description text-center">
                                    {!! $plan->description !!}
                                </div>

                                <!-- Duration Selector -->
                                <div class="eht-plan-duration">
                                    <label>Billing Cycle</label>
                                    <select class="form-select pricing-select" data-plan-id="{{ $plan->id }}">
                                        <option value="monthly" data-price="{{ $plan->monthly_price }}">
                                            Monthly - ₹{{ number_format($plan->monthly_price, 2) }}
                                        </option>
                                        @if ($plan->quarterly_price)
                                            <option value="quarterly" data-price="{{ $plan->quarterly_price }}">
                                                Quarterly - ₹{{ number_format($plan->quarterly_price, 2) }}
                                            </option>
                                        @endif
                                        @if ($plan->half_yearly_price)
                                            <option value="half_yearly" data-price="{{ $plan->half_yearly_price }}">
                                                Half Yearly - ₹{{ number_format($plan->half_yearly_price, 2) }}
                                            </option>
                                        @endif
                                        @if ($plan->yearly_price)
                                            <option value="yearly" data-price="{{ $plan->yearly_price }}">
                                                Yearly - ₹{{ number_format($plan->yearly_price, 2) }}
                                                <span class="text-success">(Best Value)</span>
                                            </option>
                                        @endif
                                    </select>
                                </div>
                            </div>

                            <!-- Footer with Action Buttons -->
                            <div class="eht-plan-footer">
                                <!-- Edit Button -->
                                    <a href="{{ route('subscription-plans.edit', $plan) }}" class="eht-btn eht-btn-primary">
                                        <i class="fas fa-edit"></i> Edit Plan
                                    </a>

                                <!-- Delete Button -->
                                    <button class="eht-btn eht-btn-danger-outline" onclick="deletePlan({{ $plan->id }})">
                                        <i class="fas fa-trash-alt"></i> Delete Plan
                                    </button>

                                <form id="delete-form-{{ $plan->id }}"
                                      action="{{ route('subscription-plans.destroy', $plan) }}"
                                      method="POST" style="display: none;">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="eht-empty">
                            <i class="fas fa-crown"></i>
                            <strong>No Subscription Plans Found</strong>
                            <p>Start by creating your first subscription plan.</p>
                                <a href="{{ route('subscription-plans.create') }}" class="eht-btn eht-btn-primary mt-3">
                                    <i class="fas fa-plus"></i> Create First Plan
                                </a>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>

        @push('scripts')
            <script>
                // ============================================
                // SUCCESS ALERT (Auto-close after 3 seconds)
                // ============================================
                @if (session('success'))
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: '{{ session('success') }}',
                        timer: 3000,
                        timerProgressBar: true,
                        showConfirmButton: false,
                        toast: true,
                        position: 'top-end',
                        background: '#f0fdf4',
                        color: '#166534',
                        iconColor: '#22c55e',
                        showClass: {
                            popup: 'animate__animated animate__fadeInRight'
                        },
                        hideClass: {
                            popup: 'animate__animated animate__fadeOutRight'
                        }
                    });
                @endif

                // ============================================
                // ERROR ALERT (Auto-close after 4 seconds)
                // ============================================
                @if (session('error'))
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: '{{ session('error') }}',
                        timer: 4000,
                        timerProgressBar: true,
                        showConfirmButton: true,
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#FA5603',
                        toast: true,
                        position: 'top-end',
                        background: '#fef2f2',
                        color: '#991b1b',
                        iconColor: '#ef4444',
                        showClass: {
                            popup: 'animate__animated animate__fadeInRight'
                        },
                        hideClass: {
                            popup: 'animate__animated animate__fadeOutRight'
                        }
                    });
                @endif

                // ============================================
                // INFO ALERT (Auto-close after 3 seconds)
                // ============================================
                @if (session('info'))
                    Swal.fire({
                        icon: 'info',
                        title: 'Info',
                        text: '{{ session('info') }}',
                        timer: 3000,
                        timerProgressBar: true,
                        showConfirmButton: false,
                        toast: true,
                        position: 'top-end',
                        background: '#eff6ff',
                        color: '#1e40af',
                        iconColor: '#3b82f6',
                        showClass: {
                            popup: 'animate__animated animate__fadeInRight'
                        },
                        hideClass: {
                            popup: 'animate__animated animate__fadeOutRight'
                        }
                    });
                @endif

                // ============================================
                // WARNING ALERT (Auto-close after 3 seconds)
                // ============================================
                @if (session('warning'))
                    Swal.fire({
                        icon: 'warning',
                        title: 'Warning!',
                        text: '{{ session('warning') }}',
                        timer: 3000,
                        timerProgressBar: true,
                        showConfirmButton: false,
                        toast: true,
                        position: 'top-end',
                        background: '#fffbeb',
                        color: '#92400e',
                        iconColor: '#f59e0b',
                        showClass: {
                            popup: 'animate__animated animate__fadeInRight'
                        },
                        hideClass: {
                            popup: 'animate__animated animate__fadeOutRight'
                        }
                    });
                @endif

                // ============================================
                // DELETE CONFIRMATION
                // ============================================
                function deletePlan(id) {
                    Swal.fire({
                        title: 'Delete Plan?',
                        text: 'This action cannot be undone. All associated data will be permanently removed.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Yes, Delete',
                        cancelButtonText: 'Cancel',
                        confirmButtonColor: '#FA5603',
                        cancelButtonColor: '#6c757d',
                        reverseButtons: false,
                        showLoaderOnConfirm: true,
                        preConfirm: function() {
                            return new Promise(function(resolve) {
                                setTimeout(function() {
                                    resolve();
                                }, 500);
                            });
                        },
                        allowOutsideClick: false,
                        backdrop: 'rgba(0,0,0,0.4)',
                        showClass: {
                            popup: 'animate__animated animate__zoomIn'
                        },
                        hideClass: {
                            popup: 'animate__animated animate__zoomOut'
                        },
                        customClass: {
                            confirmButton: 'eht-btn eht-btn-primary',
                            cancelButton: 'eht-btn eht-btn-ghost'
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Show loading state
                            Swal.fire({
                                title: 'Deleting...',
                                text: 'Please wait while we delete the plan.',
                                icon: 'info',
                                showConfirmButton: false,
                                allowOutsideClick: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                }
                            });

                            // Submit the form
                            document.getElementById('delete-form-' + id).submit();
                        }
                    });
                }

                // ============================================
                // PRICE UPDATE ON BILLING CYCLE CHANGE
                // ============================================
                document.addEventListener('DOMContentLoaded', function() {
                    document.querySelectorAll('.pricing-select').forEach(select => {
                        select.addEventListener('change', function() {
                            const selected = this.options[this.selectedIndex];
                            const price = selected.dataset.price;
                            const parent = this.closest('.eht-plan-body');
                            const priceDisplay = parent.querySelector('.eht-plan-price');
                            if (priceDisplay) {
                                // Animate price change
                                priceDisplay.style.transition = 'all 0.3s ease';
                                priceDisplay.style.transform = 'scale(1.1)';
                                priceDisplay.textContent = parseFloat(price).toFixed(2);
                                setTimeout(() => {
                                    priceDisplay.style.transform = 'scale(1)';
                                }, 300);
                            }
                        });
                    });
                });

                // ============================================
                // CUSTOM TOAST HELPER FUNCTIONS
                // ============================================
                function showSuccessToast(message) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: message,
                        timer: 3000,
                        timerProgressBar: true,
                        showConfirmButton: false,
                        toast: true,
                        position: 'top-end',
                        background: '#f0fdf4',
                        color: '#166534',
                        iconColor: '#22c55e'
                    });
                }

                function showErrorToast(message) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: message,
                        timer: 4000,
                        timerProgressBar: true,
                        showConfirmButton: true,
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#FA5603',
                        toast: true,
                        position: 'top-end',
                        background: '#fef2f2',
                        color: '#991b1b',
                        iconColor: '#ef4444'
                    });
                }

                function showInfoToast(message) {
                    Swal.fire({
                        icon: 'info',
                        title: 'Info',
                        text: message,
                        timer: 3000,
                        timerProgressBar: true,
                        showConfirmButton: false,
                        toast: true,
                        position: 'top-end',
                        background: '#eff6ff',
                        color: '#1e40af',
                        iconColor: '#3b82f6'
                    });
                }

                // ============================================
                // CONFIRM BEFORE BULK ACTION (Optional)
                // ============================================
                function confirmAction(message, callback) {
                    Swal.fire({
                        title: 'Are you sure?',
                        text: message,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Yes, Proceed',
                        cancelButtonText: 'Cancel',
                        confirmButtonColor: '#FA5603',
                        cancelButtonColor: '#6c757d'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            callback();
                        }
                    });
                }
            </script>
        @endpush
    @endsection
@else
    @php abort(403); @endphp
@endcan
