@extends('layouts.app')
@section('content')
    <section class="section premium-dashboard">
        <div class="premium-page-head">
            <div class="premium-page-title">
                <span class="mini-badge">Branch Management</span>
                <h2>Branch Details</h2>
                <p>View branch information.</p>
            </div>
            <div class="premium-head-actions">
                @if(auth()->user()->role == 'super_admin')
                    <a href="{{ route('branches.index') }}" class="btn premium-btn ghost-btn">
                        <i class="fas fa-arrow-left"></i>
                        Back To Branches
                    </a>
                @else
                            <a href="{{ route('restaurant.branches.index', [
                        'restaurant' => request()->route('restaurant')
                    ]) }}" class="btn premium-btn ghost-btn">
                                <i class="fas fa-arrow-left"></i>
                                Back To Branches
                            </a>
                @endif
            </div>
        </div>
    </section>
    <section class="section premium-dashboard pt-0">
        <div class="card premium-block">
            <div class="card-header premium-card-header">
                <div>
                    <h4>{{ $branch->name }}</h4>
                    <p class="header-subtext">
                        Branch Information
                    </p>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-3 col-md-6 mb-4">
                        <label><strong>Branch Name</strong></label>
                        <p>{{ $branch->name ?? '-' }}</p>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-4">
                        <label><strong>Branch Code</strong></label>
                        <p>{{ $branch->code ?? '-' }}</p>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-4">
                        <label><strong>Restaurant</strong></label>
                        <p>{{ optional($branch->restaurant)->name ?? '-' }}</p>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-4">
                        <label><strong>Owner</strong></label>
                        <p>{{ optional($branch->owner)->name ?? '-' }}</p>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-4">
                        <label><strong>Branch Manager</strong></label>
                        <p>{{ optional($branch->manager)->name ?? '-' }}</p>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-4">
                        <label><strong>Phone</strong></label>
                        <p>{{ $branch->phone ?? '-' }}</p>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-4">
                        <label><strong>Email</strong></label>
                        <p>{{ $branch->email ?? '-' }}</p>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-4">
                        <label><strong>GST Number</strong></label>
                        <p>{{ $branch->gst_number ?? '-' }}</p>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-4">
                        <label><strong>FSSAI License</strong></label>
                        <p>{{ $branch->fssai_license ?? '-' }}</p>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-4">
                        <label><strong>Opening Time</strong></label>
                        <p>{{ $branch->opening_time ?? '-' }}</p>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-4">
                        <label><strong>Closing Time</strong></label>
                        <p>{{ $branch->closing_time ?? '-' }}</p>
                    </div>
                    <div class="col-md-4 mb-4">
                        <label><strong>City</strong></label>
                        <p>{{ $branch->city ?? '-' }}</p>
                    </div>
                    <div class="col-md-4 mb-4">
                        <label><strong>State</strong></label>
                        <p>{{ $branch->state ?? '-' }}</p>
                    </div>
                    <div class="col-md-4 mb-4">
                        <label><strong>Country</strong></label>
                        <p>{{ $branch->country ?? '-' }}</p>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-4">
                        <label><strong>Postal Code</strong></label>
                        <p>{{ $branch->postal_code ?? '-' }}</p>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-4">
                        <label><strong>Status</strong></label>
                        @if($branch->is_active)
                            <p>
                                <span class="badge bg-success">
                                    Active
                                </span>
                            </p>
                        @else
                            <p>
                                <span class="badge bg-danger">
                                    Inactive
                                </span>
                            </p>
                        @endif
                    </div>
                    <div class="col-md-12 mb-4">
                        <label><strong>Address</strong></label>
                        <p>{{ $branch->address ?? '-' }}</p>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-4">
                        <label><strong>Latitude</strong></label>
                        <p>{{ $branch->latitude ?? '-' }}</p>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-4">
                        <label><strong>Longitude</strong></label>
                        <p>{{ $branch->longitude ?? '-' }}</p>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-4">
                        <label><strong>Created At</strong></label>
                        <p>{{ $branch->created_at?->format('d M Y h:i A') }}</p>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-4">
                        <label><strong>Updated At</strong></label>
                        <p>{{ $branch->updated_at?->format('d M Y h:i A') }}</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="card premium-block mt-4">
            <div class="card-header premium-card-header">
                <div>
                    <h4>Subscription Information</h4>
                    <p class="header-subtext">
                        Current active subscription details.
                    </p>
                </div>
            </div>

            <div class="card-body">

                @if($branch->activeSubscription)

                    @php
                        $subscription = $branch->activeSubscription;

                        $daysLeft = now()->diffInDays(
                            $subscription->end_date,
                            false
                        );
                    @endphp

                    <div class="row">

                        <div class="col-lg-3 col-md-6 mb-4">
                            <label><strong>Plan Name</strong></label>
                            <p>
                                {{ optional($subscription->plan)->name ?? '-' }}
                            </p>
                        </div>

                        <div class="col-lg-3 col-md-6 mb-4">
                            <label><strong>Billing Cycle</strong></label>
                            <p>
                                {{ ucwords(str_replace('_', ' ', $subscription->billing_cycle)) }}
                            </p>
                        </div>

                        <div class="col-lg-3 col-md-6 mb-4">
                            <label><strong>Amount</strong></label>
                            <p>
                                ₹{{ number_format($subscription->amount, 2) }}
                            </p>
                        </div>

                        <div class="col-lg-3 col-md-6 mb-4">
                            <label><strong>Status</strong></label>

                            @if($subscription->status == 'active')
                                <span class="badge bg-success">
                                    Active
                                </span>
                            @else
                                <span class="badge bg-danger">
                                    {{ ucfirst($subscription->status) }}
                                </span>
                            @endif
                        </div>

                        <div class="col-lg-3 col-md-6 mb-4">
                            <label><strong>Start Date</strong></label>
                            <p>
                                {{ \Carbon\Carbon::parse($subscription->start_date)->format('d M Y') }}
                            </p>
                        </div>

                        <div class="col-lg-3 col-md-6 mb-4">
                            <label><strong>Expiry Date</strong></label>

                            @if($daysLeft <= 10 && $daysLeft >= 0)

                                <p>
                                    <span class="badge bg-danger">
                                        {{ \Carbon\Carbon::parse($subscription->end_date)->format('d M Y') }}
                                        ({{ $daysLeft }} days left)
                                    </span>
                                </p>

                            @elseif($daysLeft < 0)

                                <p>
                                    <span class="badge bg-dark">
                                        Expired
                                    </span>
                                </p>

                            @else

                                <p>
                                    <span class="badge bg-success">
                                        {{ \Carbon\Carbon::parse($subscription->end_date)->format('d M Y') }}
                                    </span>
                                </p>

                            @endif

                        </div>

                        <div class="col-lg-3 col-md-6 mb-4">
                            <label><strong>Created At</strong></label>
                            <p>
                                {{ $subscription->created_at->format('d M Y h:i A') }}
                            </p>
                        </div>

                    </div>

                @else

                    <div class="alert alert-warning mb-0">
                        No subscription assigned to this branch.
                    </div>

                @endif

            </div>
        </div>
    </section>
    @if($branch->registration_qrcode && file_exists(public_path($branch->registration_qrcode)))
        <div class="card shadow-sm border-0">
            <div class="card-body text-center">
                <h5 class="fw-bold">Customer Registration QR</h5>

                <img src="{{ asset($branch->registration_qrcode) }}" width="300" height="300" alt="Registration QR Code"
                    class="img-fluid border p-2 bg-white">

                <p class="text-muted mt-3">
                    Scan this QR to register as a customer for <strong>{{ $branch->name }}</strong>
                </p>

                <a href="{{ asset($branch->registration_qrcode) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                    Download QR Code
                </a>
            </div>
        </div>
    @else
        <div class="alert alert-warning">
            QR Code not generated yet.
        </div>
    @endif
@endsection
