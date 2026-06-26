@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mt-3 mb-3">
    <h2>Edit Subscription Plan</h2>
    <a href="{{ route('subscription-plans.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back
    </a>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('subscription-plans.update', $subscriptionPlan) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="name" class="form-label">Plan Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror"
                           value="{{ old('name', $subscriptionPlan->name ?? '') }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="max_branches" class="form-label">Max Branches <span class="text-danger">*</span></label>
                    <input type="number" name="max_branches" id="max_branches" class="form-control @error('max_branches') is-invalid @enderror"
                           value="{{ old('max_branches', $subscriptionPlan->max_branches ?? 1) }}" required min="1">
                    @error('max_branches')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-12 mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror"
                              rows="3">{{ old('description', $subscriptionPlan->description ?? '') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-3 mb-3">
                    <label for="monthly_price" class="form-label">Monthly Price ($) <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" name="monthly_price" id="monthly_price"
                           class="form-control @error('monthly_price') is-invalid @enderror"
                           value="{{ old('monthly_price', $subscriptionPlan->monthly_price ?? 0) }}" required>
                    @error('monthly_price')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-3 mb-3">
                    <label for="quarterly_price" class="form-label">Quarterly Price ($)</label>
                    <input type="number" step="0.01" name="quarterly_price" id="quarterly_price"
                           class="form-control @error('quarterly_price') is-invalid @enderror"
                           value="{{ old('quarterly_price', $subscriptionPlan->quarterly_price ?? '') }}">
                    @error('quarterly_price')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-3 mb-3">
                    <label for="half_yearly_price" class="form-label">Half Yearly Price ($)</label>
                    <input type="number" step="0.01" name="half_yearly_price" id="half_yearly_price"
                           class="form-control @error('half_yearly_price') is-invalid @enderror"
                           value="{{ old('half_yearly_price', $subscriptionPlan->half_yearly_price ?? '') }}">
                    @error('half_yearly_price')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-3 mb-3">
                    <label for="yearly_price" class="form-label">Yearly Price ($)</label>
                    <input type="number" step="0.01" name="yearly_price" id="yearly_price"
                           class="form-control @error('yearly_price') is-invalid @enderror"
                           value="{{ old('yearly_price', $subscriptionPlan->yearly_price ?? '') }}">
                    @error('yearly_price')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-12 mb-3">
                    <div class="form-check">
                        <input type="checkbox" name="is_active" id="is_active" class="form-check-input" value="1"
                               {{ old('is_active', $subscriptionPlan->is_active ?? true) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">
                            Active (Plan will be visible to customers)
                        </label>
                    </div>
                </div>
            </div>

            <div class="mt-3">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update Plan
                </button>
                <a href="{{ route('subscription-plans.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
