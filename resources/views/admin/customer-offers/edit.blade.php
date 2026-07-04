@can('create-customer-offers')
    @extends('layouts.app')

    @section('content')
    <section class="section premium-dashboard">
        <div class="premium-floating-header">
            <div class="header-content">
                <div class="header-left">
                    <div class="header-icon">
                         <i class="fas fa-bullhorn"></i>
                    </div>
                    <div>
                        <span class="header-badge">
                            Offer Management
                        </span>
                             <h1>Edit Customer Offer</h1>
                            <p>Update the customer offer details and manage its availability.</p>
                    </div>
                </div>
                @php
                    $restaurantSlug = request()->route('restaurant');
                    $branchSlug = request()->route('branch');
                @endphp
                <div class="premium-head-actions">
                    @if (auth()->user()->role === 'super_admin')
                        <a href="{{ route('customer-offers.index') }}" class="premium-back-btn">
                            <i class="fas fa-arrow-left"></i>
                            Back To Offers
                        </a>
                    @elseif(!empty($restaurantSlug) && !empty($branchSlug))
                        <a href="{{ route('branch.customer-offers.index', [
                                'restaurant' => $restaurantSlug,
                                'branch' => $branchSlug,
                            ]) }}" class="premium-back-btn">
                            <i class="fas fa-arrow-left"></i>
                            Back To Offers
                        </a>
                    @elseif(!empty($restaurantSlug))
                        <a href="{{ route('restaurant.customer-offers.index', [
                            'restaurant' => $restaurantSlug,
                            ]) }}" class="premium-back-btn">
                            <i class="fas fa-arrow-left"></i>
                            Back To Offers
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </section>    
        <div class="card">           
            <div class="card-body">
                <form method="POST" action="{{ route('customer-offers.update', $offer->id) }}">
                    @csrf
                    @method('PUT')
                    <div class="row">

                   
                    <div class="col-lg-4 mb-3">
                        <label class="form-label">
                            Category
                        </label>
                        <select name="category" class="premium-input">
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
                    <div class="col-lg-4 mb-3">
                        <label class="form-label">
                            Title
                        </label>
                        <input type="text" name="title" class="premium-input" value="{{ old('title', $offer->title) }}" >
                        @error('title')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                    </div>
                     <div class="col-lg-4 mb-3">
                        <label class="form-label">
                            Status
                        </label>
                        <select name="status" class="premium-input">
                            <option value="1" {{ old('status', $offer->status) == 1 ? 'selected' : '' }}>
                                Active
                            </option>
                            <option value="0" {{ old('status', $offer->status) == 0 ? 'selected' : '' }}>
                                Inactive
                            </option>
                        </select>
                    </div>
                     </div>
                    <div class="col-lg-12 mb-3">
                        <label class="form-label">
                            Description
                        </label>
                        <textarea name="description" id="description"
                            class="premium-input">{{ old('description', $offer->description) }}</textarea>
                            @error('description')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                    </div>
                   
                    <div class="premium-card-footer d-flex align-items-center gap-1 mt-3">                   
                    @php
                            $restaurantSlug = auth()->user()->restaurant?->slug;
                            $branchSlug = auth()->user()->branch?->slug;
                        @endphp
                        @if ($branchSlug)
                            <a href="{{ route('branch.customer-offers.index', [
                                'restaurant' => $restaurantSlug,
                                'branch' => $branchSlug,
                            ]) }}" class="premium-back-btn">
                                        Cancel
                                    </a>
                        @else
                            <a href="{{ route('restaurant.customer-offers.index', [
                                'restaurant' => $restaurantSlug,
                            ]) }}" class="premium-back-btn">
                                        Cancel
                                    </a>
                        @endif
                         <button  class="premium-btn btn-primary"> <i class="fas fa-plus-circle"></i>
                        Update Offer
                    </button>
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
