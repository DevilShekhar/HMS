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
                <a href="{{ route('branches.index') }}"
                   class="btn premium-btn ghost-btn">
                    <i class="fas fa-arrow-left"></i>
                    Back To Branches
                </a>
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
                <div class="col-md-6 mb-4">
                    <label><strong>Branch Name</strong></label>
                    <p>{{ $branch->name ?? '-' }}</p>
                </div>
                <div class="col-md-6 mb-4">
                    <label><strong>Branch Code</strong></label>
                    <p>{{ $branch->code ?? '-' }}</p>
                </div>
                <div class="col-md-6 mb-4">
                    <label><strong>Restaurant</strong></label>
                    <p>{{ optional($branch->restaurant)->name ?? '-' }}</p>
                </div>
                <div class="col-md-6 mb-4">
                    <label><strong>Owner</strong></label>
                    <p>{{ optional($branch->owner)->name ?? '-' }}</p>
                </div>
                <div class="col-md-6 mb-4">
                    <label><strong>Branch Manager</strong></label>
                    <p>{{ optional($branch->manager)->name ?? '-' }}</p>
                </div>
                <div class="col-md-6 mb-4">
                    <label><strong>Phone</strong></label>
                    <p>{{ $branch->phone ?? '-' }}</p>
                </div>
                <div class="col-md-6 mb-4">
                    <label><strong>Email</strong></label>
                    <p>{{ $branch->email ?? '-' }}</p>
                </div>
                <div class="col-md-6 mb-4">
                    <label><strong>GST Number</strong></label>
                    <p>{{ $branch->gst_number ?? '-' }}</p>
                </div>
                <div class="col-md-6 mb-4">
                    <label><strong>FSSAI License</strong></label>
                    <p>{{ $branch->fssai_license ?? '-' }}</p>
                </div>
                <div class="col-md-6 mb-4">
                    <label><strong>Opening Time</strong></label>
                    <p>{{ $branch->opening_time ?? '-' }}</p>
                </div>
                <div class="col-md-6 mb-4">
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
                <div class="col-md-6 mb-4">
                    <label><strong>Postal Code</strong></label>
                    <p>{{ $branch->postal_code ?? '-' }}</p>
                </div>
                <div class="col-md-6 mb-4">
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
                <div class="col-md-6 mb-4">
                    <label><strong>Latitude</strong></label>
                    <p>{{ $branch->latitude ?? '-' }}</p>
                </div>
                <div class="col-md-6 mb-4">
                    <label><strong>Longitude</strong></label>
                    <p>{{ $branch->longitude ?? '-' }}</p>
                </div>
                <div class="col-md-6 mb-4">
                    <label><strong>Created At</strong></label>
                    <p>{{ $branch->created_at?->format('d M Y h:i A') }}</p>
                </div>
                <div class="col-md-6 mb-4">
                    <label><strong>Updated At</strong></label>
                    <p>{{ $branch->updated_at?->format('d M Y h:i A') }}</p>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection