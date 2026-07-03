@can('create-customer-offers')
    @extends('layouts.app')

    @section('content')
        <div class="card">
            <div class="card-header">
                <h4>Edit Customer Offer</h4>
            </div>

            <div class="card-body">

                <form method="POST" action="{{ route('customer-offers.update', $offer->id) }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label">
                            Category
                        </label>

                        <select name="category" class="form-control">
                            <option value="">
                                Select Category
                            </option>
                            <option value="birthday" {{ old('category', $offer->category) == 'birthday' ? 'selected' : '' }}>
                                Birthday
                            </option>
                            <option value="anniversary" {{ old('category', $offer->category) == 'anniversary' ? 'selected' : '' }}>
                                Anniversary
                            </option>
                            <option value="other" {{ old('category', $offer->category) == 'other' ? 'selected' : '' }}>
                                Other
                            </option>
                            @error('category')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">
                            Title
                        </label>
                        <input type="text" name="title" class="form-control" value="{{ old('title', $offer->title) }}" >
                        @error('title')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                    </div>



                    <div class="mb-3">
                        <label class="form-label">
                            Description
                        </label>

                        <textarea name="description" id="description"
                            class="form-control">{{ old('description', $offer->description) }}</textarea>
                            @error('description')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">
                            Status
                        </label>

                        <select name="status" class="form-control">
                            <option value="1" {{ old('status', $offer->status) == 1 ? 'selected' : '' }}>
                                Active
                            </option>

                            <option value="0" {{ old('status', $offer->status) == 0 ? 'selected' : '' }}>
                                Inactive
                            </option>
                        </select>
                    </div>

                    <div class="d-flex align-items-center gap-1 mt-3">

                        <button type="submit" class="btn btn-primary">
                            Update Offer
                        </button>

                        @php
                            $restaurantSlug = auth()->user()->restaurant?->slug;
                            $branchSlug = auth()->user()->branch?->slug;
                        @endphp

                        @if ($branchSlug)
                                    <a href="{{ route('branch.customer-offers.index', [
                                'restaurant' => $restaurantSlug,
                                'branch' => $branchSlug,
                            ]) }}" class="btn btn-secondary">
                                        Cancel
                                    </a>
                        @else
                                    <a href="{{ route('restaurant.customer-offers.index', [
                                'restaurant' => $restaurantSlug,
                            ]) }}" class="btn btn-secondary">
                                        Cancel
                                    </a>
                        @endif

                    </div>

                </form>

            </div>
        </div>
    @endsection
@else
    @php
        abort(403);
    @endphp
@endcan
