@can('view-branch')
@extends('layouts.app')

@section('content')
<section class="section premium-dashboard">
    <div class="premium-floating-header">
        <div class="header-content">
            <div class="header-left">
                <div class="header-icon">
                    <i class="fas fa-code-branch"></i>
                </div>
                <div>
                    <span class="header-badge">Branch Management</span>
                    <h1>{{ $branch->name }}</h1>
                    <p>Branch Details & Information</p>
                </div>
            </div>
            <div class="header-right">
                @if(auth()->user()->role == 'super_admin')
                    <a href="{{ route('branches.index') }}" class="premium-back-btn">
                        <i class="fas fa-arrow-left"></i> Back to Branches
                    </a>
                @else
                    <a href="{{ route('restaurant.branches.index', ['restaurant' => request()->route('restaurant')]) }}"
                       class="premium-back-btn">
                        <i class="fas fa-arrow-left"></i> Back to Branches
                    </a>
                @endif
            </div>
        </div>
    </div>
</section>

<section class="section premium-dashboard pt-0">
    <div class="row justify-content-center">
        <div class="col-xl-12">

            <!-- Single Main Card -->
            <div class="premium-card mt-5">

                <div class="premium-card-header">
                    <div class="card-title-group">
                        <div>
                            <h3>Branch Information</h3>
                            <p>Complete details of {{ $branch->name }}</p>
                        </div>
                    </div>
                </div>

                <div class="premium-card-body">

                    <div class="row g-4">

                        <!-- Basic Information -->
                        <div class="col-lg-7">
                            <h5 class="mb-3 text-warning">Basic Information</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="premium-form-group">
                                        <label class="premium-label">Branch Name</label>
                                        <p class="fw-bold">{{ $branch->name }}</p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="premium-form-group">
                                        <label class="premium-label">Branch Code</label>
                                        <p>{{ $branch->code ?? '-' }}</p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="premium-form-group">
                                        <label class="premium-label">Restaurant</label>
                                        <p>{{ optional($branch->restaurant)->name ?? '-' }}</p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="premium-form-group">
                                        <label class="premium-label">Owner</label>
                                        <p>{{ optional($branch->owner)->name ?? '-' }}</p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="premium-form-group">
                                        <label class="premium-label">Branch Manager</label>
                                        <p>{{ optional($branch->manager)->name ?? '-' }}</p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="premium-form-group">
                                        <label class="premium-label">Status</label>
                                        @if($branch->is_active)
                                            <span class="badge bg-success px-3 py-2">Active</span>
                                        @else
                                            <span class="badge bg-danger px-3 py-2">Inactive</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Contact & Location -->
                        <div class="col-lg-5">
                            <h5 class="mb-3 text-warning">Contact & Location</h5>
                            <div class="premium-form-group">
                                <label class="premium-label">Phone</label>
                                <p>{{ $branch->phone ?? '-' }}</p>
                            </div>
                            <div class="premium-form-group">
                                <label class="premium-label">Email</label>
                                <p>{{ $branch->email ?? '-' }}</p>
                            </div>
                            <div class="premium-form-group">
                                <label class="premium-label">Full Address</label>
                                <p>{{ $branch->address ?? '-' }}</p>
                            </div>
                            <div class="row">
                                <div class="col-6">
                                    <div class="premium-form-group">
                                        <label class="premium-label">City</label>
                                        <p>{{ $branch->city ?? '-' }}</p>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="premium-form-group">
                                        <label class="premium-label">State</label>
                                        <p>{{ $branch->state ?? '-' }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-6">
                                    <div class="premium-form-group">
                                        <label class="premium-label">Postal Code</label>
                                        <p>{{ $branch->postal_code ?? '-' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tax & Compliance -->
                        <div class="col-12">
                            <h5 class="mb-3 text-warning">Tax & Compliance</h5>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="premium-form-group">
                                        <label class="premium-label">GST Number</label>
                                        <p>{{ $branch->gst_number ?? '-' }}</p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="premium-form-group">
                                        <label class="premium-label">FSSAI License</label>
                                        <p>{{ $branch->fssai_license ?? '-' }}</p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="premium-form-group">
                                        <label class="premium-label">GST Breakdown</label>
                                        <div class="row g-3 text-center">
                                            <div class="col-4">
                                                <small class="text-muted">GST (%)</small>
                                                <p class="fw-bold mb-0">{{ $branch->gst ?? '0' }}</p>
                                            </div>
                                            <div class="col-4">
                                                <small class="text-muted">CGST (%)</small>
                                                <p class="fw-bold mb-0">{{ $branch->cgst ?? '0' }}</p>
                                            </div>
                                            <div class="col-4">
                                                <small class="text-muted">SGST (%)</small>
                                                <p class="fw-bold mb-0">{{ $branch->sgst ?? '0' }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Subscription -->
                        <div class="col-12">
                            <h5 class="mb-3 text-warning">Subscription Information</h5>
                            @if($branch->activeSubscription)
                                @php
                                    $subscription = $branch->activeSubscription;
                                    $daysLeft = now()->diffInDays($subscription->end_date, false);
                                @endphp
                                <div class="row">
                                    <div class="col-md-3 col-6">
                                        <div class="premium-form-group">
                                            <label class="premium-label">Plan</label>
                                            <p class="fw-bold">{{ optional($subscription->plan)->name ?? '-' }}</p>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-6">
                                        <div class="premium-form-group">
                                            <label class="premium-label">Billing Cycle</label>
                                            <p>{{ ucwords(str_replace('_', ' ', $subscription->billing_cycle)) }}</p>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-6">
                                        <div class="premium-form-group">
                                            <label class="premium-label">Amount</label>
                                            <p class="fw-bold">₹{{ number_format($subscription->amount, 2) }}</p>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-6">
                                        <div class="premium-form-group">
                                            <label class="premium-label">Status</label>
                                            @if($subscription->status == 'active')
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-danger">{{ ucfirst($subscription->status) }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="premium-form-group">
                                            <label class="premium-label">Start Date</label>
                                            <p>{{ \Carbon\Carbon::parse($subscription->start_date)->format('d M Y') }}</p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="premium-form-group">
                                            <label class="premium-label">Expiry Date</label>
                                            @if($daysLeft <= 10 && $daysLeft >= 0)
                                                <span class="badge bg-danger">{{ \Carbon\Carbon::parse($subscription->end_date)->format('d M Y') }} ({{ $daysLeft }} days left)</span>
                                            @elseif($daysLeft < 0)
                                                <span class="badge bg-dark">Expired</span>
                                            @else
                                                <span class="badge bg-success">{{ \Carbon\Carbon::parse($subscription->end_date)->format('d M Y') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="alert alert-warning mb-0 text-center">
                                    <i class="fas fa-info-circle"></i> No active subscription found for this branch.
                                </div>
                            @endif
                        </div>

                        <!-- QR Code -->
                        @if($branch->registration_qrcode && file_exists(public_path($branch->registration_qrcode)))
                        <div class="col-12 mt-4">
                            <h5 class="mb-3 text-warning">Customer Registration QR Code</h5>
                            <div class="text-center">
                                <img src="{{ asset($branch->registration_qrcode) }}"
                                     width="260" height="260"
                                     class="img-fluid border p-3 bg-white rounded shadow-sm"
                                     alt="QR Code">

                                <p class="mt-4 text-muted">
                                    Scan this QR code to register as a customer at <strong>{{ $branch->name }}</strong>
                                </p>

                                <a href="{{ asset($branch->registration_qrcode) }}" target="_blank"
                                   class="premium-btn btn-outline mt-3">
                                    <i class="fas fa-download"></i> Download QR Code
                                </a>
                            </div>
                        </div>
                        @endif

                    </div>
                </div>
            </div>

        </div>
    </div>
</section>
@endsection
@endcan
