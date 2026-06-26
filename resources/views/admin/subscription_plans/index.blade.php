@can('view-subscription')
    @extends('layouts.app')

    @section('content')
        <div class="container-fluid px-4">
            <!-- Header Section -->
            <div class="d-flex justify-content-between align-items-center mt-4 mb-4">
                <div>
                    <h1 class="h2 fw-bold mb-1">Subscription Plans</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item active">Subscription Plans</li>
                        </ol>
                    </nav>
                </div>
                <a href="{{ route('subscription-plans.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i>
                    </i>Create New Plan
                </a>
            </div>

            <!-- Plans Grid -->
            <div class="row g-4">
                @foreach ($plans as $plan)
                    <div class="col-xl-4 col-md-6">
                        <div class="card plan-card h-100 {{ $loop->iteration == 2 ? 'popular-plan' : '' }}">
                            @if ($loop->iteration == 2)
                                <div class="popular-badge">
                                    <i class="fas fa-star me-1"></i> Most Popular
                                </div>
                            @endif

                            <div class="card-body p-4">
                                <!-- Plan Name -->
                                <div class="text-center mb-4">
                                    <h3 class="fw-bold mb-2">{{ $plan->name }}</h3>
                                    <div class="branch-badge">
                                        <i class="fas fa-store me-1"></i> {{ $plan->max_branches }} Branch(es)
                                    </div>
                                </div>

                                <!-- Price -->
                                <div class="text-center mb-4">
                                    <div class="price-wrapper">
                                        <span class="currency">$</span>
                                        <span class="price">{{ number_format($plan->monthly_price, 2) }}</span>
                                        <span class="period">/month</span>
                                    </div>
                                    @if ($plan->yearly_price)
                                        <div class="savings-badge mt-2">
                                            <i class="fas fa-tag me-1"></i> Save 20% with annual billing
                                        </div>
                                    @endif
                                </div>

                                <!-- Description -->
                                <p class="text-muted text-center mb-4">

                                    {!! $plan->description !!}</p>

                                <!-- Features -->


                                <!-- Duration Selector -->
                                <div class="duration-selector mb-4">
                                    <label class="form-label fw-semibold">Billing Cycle</label>
                                    <select class="form-select pricing-select" data-plan-id="{{ $plan->id }}">
                                        <option value="monthly" data-price="{{ $plan->monthly_price }}">
                                            Monthly - ${{ number_format($plan->monthly_price, 2) }}
                                        </option>
                                        @if ($plan->quarterly_price)
                                            <option value="quarterly" data-price="{{ $plan->quarterly_price }}">
                                                Quarterly - ${{ number_format($plan->quarterly_price, 2) }}
                                            </option>
                                        @endif
                                        @if ($plan->half_yearly_price)
                                            <option value="half_yearly" data-price="{{ $plan->half_yearly_price }}">
                                                Half Yearly - ${{ number_format($plan->half_yearly_price, 2) }}
                                            </option>
                                        @endif
                                        @if ($plan->yearly_price)
                                            <option value="yearly" data-price="{{ $plan->yearly_price }}">
                                                Yearly - ${{ number_format($plan->yearly_price, 2) }}
                                                <span class="text-success">(Best Value)</span>
                                            </option>
                                        @endif
                                    </select>
                                </div>
                            </div>

                            <div class="card-footer bg-transparent border-0 pb-4 px-4">
                                <div class="d-grid gap-2">
                                    <a href="{{ route('subscription-plans.edit', $plan) }}" class="btn btn-primary btn-edit">
                                        <i class="fas fa-edit me-2"></i>Edit Plan
                                    </a>
                                    <button class="btn btn-outline-danger btn-delete"
                                        onclick="deletePlan({{ $plan->id }})">
                                        <i class="fas fa-trash-alt me-2"></i>Delete Plan
                                    </button>
                                </div>
                                <form id="delete-form-{{ $plan->id }}"
                                    action="{{ route('subscription-plans.destroy', $plan) }}" method="POST"
                                    style="display: none;">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endsection
@else
    @php
        abort(403);
    @endphp
@endcan
