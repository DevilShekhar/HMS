@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header">
        <h4>Create Subscription Plan</h4>
    </div>

    <div class="card-body">

        <form method="POST" action="{{ route('subscription-plans.store') }}">
            @csrf

            <div class="mb-3">
                <label class="form-label">Plan Name</label>

                <input type="text"
                       name="name"
                       class="form-control"
                       value="{{ old('name') }}"
                       required>
            </div>

            <div class="mb-3">
    <label class="form-label">Description</label>

    <textarea name="description" id="description" class="form-control">
        {{ old('description') }}
    </textarea>
</div>

            <div class="row">
                <div class="col-md-3 mb-3">
                    <label class="form-label">Monthly Price</label>

                    <input type="number"
                           step="0.01"
                           name="monthly_price"
                           class="form-control"
                           value="{{ old('monthly_price') }}">
                </div>

                <div class="col-md-3 mb-3">
                    <label class="form-label">Quarterly Price</label>

                    <input type="number"
                           step="0.01"
                           name="quarterly_price"
                           class="form-control"
                           value="{{ old('quarterly_price') }}">
                </div>

                <div class="col-md-3 mb-3">
                    <label class="form-label">Half Yearly Price</label>

                    <input type="number"
                           step="0.01"
                           name="half_yearly_price"
                           class="form-control"
                           value="{{ old('half_yearly_price') }}">
                </div>

                <div class="col-md-3 mb-3">
                    <label class="form-label">Yearly Price</label>

                    <input type="number"
                           step="0.01"
                           name="yearly_price"
                           class="form-control"
                           value="{{ old('yearly_price') }}">
                </div>
            </div>

            <div class="form-check mt-3">
                <input type="checkbox"
                       name="status"
                       value="1"
                       class="form-check-input"
                       checked>

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
