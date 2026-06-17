@extends('layouts.app')
@section('content')
    <section class="section premium-dashboard">
        <div class="premium-page-head">
            <div class="premium-page-title">
                <span class="mini-badge">Table Management</span>
                <h2>Create Table Category</h2>
                <p>Add a new table category for a branch.</p>
            </div>
            @php
                $restaurantSlug = request()->route('restaurant');
                $branchSlug = request()->route('branch');
            @endphp

            <div class="premium-head-actions">

                @if (auth()->user()->role === 'super_admin')
                    <a href="{{ route('table-categories.index') }}" class="btn premium-btn ghost-btn">
                        <i class="fas fa-arrow-left"></i>
                        Back To Table Categories
                    </a>
                @elseif(!empty($restaurantSlug) && !empty($branchSlug))
                    <a href="{{ route('branch.table-categories.index', [
                        'restaurant' => $restaurantSlug,
                        'branch' => $branchSlug,
                    ]) }}"
                        class="btn premium-btn ghost-btn">
                        <i class="fas fa-arrow-left"></i>
                        Back To Table Categories
                    </a>
                @elseif(!empty($restaurantSlug))
                    <a href="{{ route('restaurant.table-categories.index', [
                        'restaurant' => $restaurantSlug,
                    ]) }}"
                        class="btn premium-btn ghost-btn">
                        <i class="fas fa-arrow-left"></i>
                        Back To Table Categories
                    </a>
                @endif

            </div>
        </div>
    </section>
    <section class="section premium-dashboard pt-0">
        <form action="{{ route('restaurant.table-categories.store', ['restaurant' => request()->route('restaurant')]) }}"
            method="POST">
            @csrf
            <div class="row">
                <div class="col-lg-12">
                    <div class="card premium-block">
                        <div class="card-header premium-card-header">
                            <div>
                                <h4>Table Category Information</h4>
                                <p class="header-subtext">
                                    Enter table category details.
                                </p>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                @if (auth()->user()->role == 'owner')
                                    <div class="col-md-6 mb-4">
                                        <label class="form-label"> Branch</label>
                                        <select name="branch_id" class="form-control premium-input">
                                            <option value=""> Select Branch</option>
                                            @foreach ($branches as $branch)
                                                <option value="{{ $branch->id }}"
                                                    {{ old('branch_id') == $branch->id ? 'selected' : '' }}>
                                                    {{ $branch->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('branch_id')
                                            <small class="text-danger">
                                                {{ $message }}
                                            </small>
                                        @enderror
                                    </div>
                                @endif
                                @if (auth()->user()->role == 'branch_manager')
                                    <input type="hidden" name="branch_id" value="{{ $branch->id }}">
                                    <div class="col-md-6 mb-4">
                                        <label class="form-label">Branch</label>
                                        <input type="text" class="form-control premium-input" value="{{ $branch->name }}"
                                            readonly>
                                    </div>
                                @endif
                                <div class="col-md-6 mb-4">
                                    <label class="form-label">
                                        Table Category Name
                                    </label>
                                    <input type="text"name="name" value="{{ old('name') }}"
                                        class="form-control premium-input" placeholder="Enter category name">
                                    @error('name')
                                        <small class="text-danger">
                                            {{ $message }}
                                        </small>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i>
                            Create Table Category
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </section>
@endsection
