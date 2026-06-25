@extends('layouts.app')

@section('content')
    <div class="card">
        <div class="card-header">
            <h4>Create Customer Offer</h4>
        </div>

        <div class="card-body">

            <form method="POST" action="{{ route('customer-offers.store') }}">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Category</label>

                    <select name="category" class="form-control" required>
                        <option value="">Select Category</option>
                        <option value="birthday">Birthday</option>
                        <option value="anniversary">Anniversary</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label>Title</label>

                    <input type="text" name="title" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Description</label>

                    <textarea name="description" id="description" class="form-control">{{ old('description') }}</textarea>
                </div>

                <div class="form-check mt-3">
                    <input type="checkbox" name="status" class="form-check-input" checked>

                    <label class="form-check-label">
                        Active
                    </label>
                </div>

                <button class="btn btn-primary mt-3">
                    Save Offer
                </button>

            </form>

        </div>
    </div>
@endsection
