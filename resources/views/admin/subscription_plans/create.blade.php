@extends('layouts.app')

@section('content')
    <div class="card">
        <div class="card-header">
            <h4>Create Subscription Plan</h4>
        </div>

        <div class="card-body">
            @if (session('status'))
                <div class="alert alert-success" role="alert">
                    {{ session('status') }}
                </div>
            @endif
            <form method="POST" action="{{ route('subscription-plans.store') }}">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Plan Name</label>

                    <input type="text" name="name" class="form-control" value="{{ old('name') }}">
                    @error('name')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">
                        Description <span class="text-danger">*</span>
                    </label>

                    <textarea name="description" id="description">
                            {{ old('description') }}
                        </textarea>

                    @error('description')
                        <div class="text-danger mt-2">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Monthly Price</label>

                        <input type="number" step="0.01" name="monthly_price" class="form-control"
                            value="{{ old('monthly_price') }}">
                        @error('monthly_price')
                            <div class="text-danger mt-2">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="form-label">Quarterly Price</label>

                        <input type="number" step="0.01" name="quarterly_price" class="form-control"
                            value="{{ old('quarterly_price') }}">
                        @error('quarterly_price')
                            <div class="text-danger mt-2">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="form-label">Half Yearly Price</label>

                        <input type="number" step="0.01" name="half_yearly_price" class="form-control"
                            value="{{ old('half_yearly_price') }}">
                        @error('half_yearly_price')
                            <div class="text-danger mt-2">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="form-label">Yearly Price</label>

                        <input type="number" step="0.01" name="yearly_price" class="form-control"
                            value="{{ old('yearly_price') }}">
                        @error('yearly_price')
                            <div class="text-danger mt-2">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>

                <div class="form-check mt-3">
                    <input type="checkbox" name="status" value="1" class="form-check-input" checked>

                    <label class="form-check-label">
                        Active
                    </label>
                </div>

                <button class="btn btn-primary mt-3">
                    Save Plan
                </button>

            </form>

        </div>
    </div>


@endsection
