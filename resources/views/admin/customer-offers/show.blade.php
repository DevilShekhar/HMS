@extends('layouts.app')

@section('content')
    <div class="card">

        <div class="card-header d-flex justify-content-between align-items-center">
            <h4>
                Customer Offer Details
            </h4>

            @php
                        $restaurantSlug = auth()->user()->restaurant?->slug;
                        $branchSlug = auth()->user()->branch?->slug;
                    @endphp

                    @if ($branchSlug)
                        <a href="{{ route('branch.customer-offers.index', [
                            'restaurant' => $restaurantSlug,
                            'branch' => $branchSlug,
                        ]) }}"
                            class="btn btn-secondary">
                            Cancel
                        </a>
                    @else
                        <a href="{{ route('restaurant.customer-offers.index', [
                            'restaurant' => $restaurantSlug,
                        ]) }}"
                            class="btn btn-secondary">
                            Back
                        </a>
                    @endif
        </div>

        <div class="card-body">

            <div class="row mb-3">
                <div class="col-md-3">
                    <strong>Title</strong>
                </div>

                <div class="col-md-9">
                    {{ $offer->title }}
                </div>
            </div>

            <hr>

            <div class="row mb-3">
                <div class="col-md-3">
                    <strong>Category</strong>
                </div>

                <div class="col-md-9">
                    @if ($offer->category == 'birthday')
                        <span class="badge bg-primary">
                            Birthday
                        </span>
                    @elseif($offer->category == 'anniversary')
                        <span class="badge bg-success">
                            Anniversary
                        </span>
                    @else
                        <span class="badge bg-secondary">
                            Other
                        </span>
                    @endif
                </div>
            </div>

            <hr>

            <div class="row mb-3">
                <div class="col-md-3">
                    <strong>Status</strong>
                </div>

                <div class="col-md-9">
                    @if ($offer->status)
                        <span class="badge bg-success">
                            Active
                        </span>
                    @else
                        <span class="badge bg-danger">
                            Inactive
                        </span>
                    @endif
                </div>
            </div>

            <hr>

            <div class="row mb-3">
                <div class="col-md-3">
                    <strong>Description</strong>
                </div>

                <div class="col-md-9">
                    {!! $offer->description !!}
                </div>
            </div>

            <hr>

            <div class="row">
                <div class="col-md-3">
                    <strong>Created At</strong>
                </div>

                <div class="col-md-9">
                    {{ $offer->created_at->format('d M Y') }}
                </div>
            </div>

        </div>

    </div>
@endsection
