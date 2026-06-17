@extends('layouts.app')
@section('content')
    <section class="section premium-dashboard">
        <div class="premium-page-head">
            <div class="premium-page-title">
                <span class="mini-badge">Category Management</span>
                <h2>Categories</h2>
                <p>Manage restaurant categories.</p>
            </div>
            @php
                $restaurantSlug = request()->route('restaurant');
                $branchSlug = request()->route('branch');
            @endphp

            <div class="premium-head-actions">

                @if (auth()->user()->role === 'super_admin')
                    <a href="{{ route('categories.create') }}" class="btn premium-btn">
                        <i class="fas fa-plus"></i>
                        Add Category
                    </a>
                @elseif (!empty($restaurantSlug) && !empty($branchSlug))
                    <a href="{{ route('branch.categories.create', [
                        'restaurant' => $restaurantSlug,
                        'branch' => $branchSlug,
                    ]) }}"
                        class="btn premium-btn">
                        <i class="fas fa-plus"></i>
                        Add Category
                    </a>
                @elseif (!empty($restaurantSlug))
                    <a href="{{ route('restaurant.categories.create', [
                        'restaurant' => $restaurantSlug,
                    ]) }}"
                        class="btn premium-btn">
                        <i class="fas fa-plus"></i>
                        Add Category
                    </a>
                @endif

            </div>
        </div>
    </section>
    <section class="section premium-dashboard pt-0">
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        <div class="card premium-block">
            <div class="card-header premium-card-header">
                <div>
                    <h4>Category List</h4>
                    <p class="header-subtext">
                        View and manage all categories.
                    </p>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Image</th>
                                <th>Category</th>
                                <th>Branch</th>
                                <th>Created By</th>
                                <th>Status</th>
                                <th width="180">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($categories as $category)
                                <tr>
                                    <td>
                                        {{ $loop->iteration + ($categories->currentPage() - 1) * $categories->perPage() }}
                                    </td>
                                    <td>
                                        @if ($category->image)
                                            <img src="{{ asset($category->image) }}" width="50" height="50"
                                                class="rounded">
                                        @else
                                            <span class="badge bg-secondary">
                                                No Image
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <strong>
                                            {{ $category->name }}
                                        </strong>
                                        @if ($category->description)
                                            <br>
                                            <small class="text-muted">
                                                {{ Str::limit($category->description, 50) }}
                                            </small>
                                        @endif
                                    </td>
                                    <td>
                                        {{ optional($category->branch)->name }}
                                    </td>
                                    <td>
                                        {{ optional($category->creator)->name }}
                                    </td>
                                    <td>
                                        @if ($category->is_active)
                                            <span class="badge bg-success">
                                                Active
                                            </span>
                                        @else
                                            <span class="badge bg-danger">
                                                Inactive
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1">

                                            @php
                                                $restaurantSlug = request()->route('restaurant');
                                                $branchSlug = request()->route('branch');
                                            @endphp


                                            {{-- SUPER ADMIN --}}
                                            @if (auth()->user()->role === 'super_admin')
                                                <a href="{{ route('categories.edit', $category->id) }}"
                                                    class="btn btn-warning btn-sm">
                                                    <i class="fas fa-edit"></i>
                                                </a>

                                                <form action="{{ route('categories.destroy', $category->id) }}"
                                                    method="POST" onsubmit="return confirm('Are you sure?')">

                                                    {{-- BRANCH LEVEL --}}
                                                @elseif (!empty($restaurantSlug) && !empty($branchSlug))
                                                    <a href="{{ route('branch.categories.edit', [
                                                        'restaurant' => $restaurantSlug,
                                                        'branch' => $branchSlug,
                                                        'category' => $category->id,
                                                    ]) }}"
                                                        class="btn btn-warning btn-sm">
                                                        <i class="fas fa-edit"></i>
                                                    </a>

                                                    <form
                                                        action="{{ route('branch.categories.destroy', [
                                                            'restaurant' => $restaurantSlug,
                                                            'branch' => $branchSlug,
                                                            'category' => $category->id,
                                                        ]) }}"
                                                        method="POST" onsubmit="return confirm('Are you sure?')">


                                                        {{-- RESTAURANT LEVEL --}}
                                                    @elseif (!empty($restaurantSlug))
                                                        <a href="{{ route('restaurant.categories.edit', [
                                                            'restaurant' => $restaurantSlug,
                                                            'category' => $category->id,
                                                        ]) }}"
                                                            class="btn btn-warning btn-sm">
                                                            <i class="fas fa-edit"></i>
                                                        </a>

                                                        <form
                                                            action="{{ route('restaurant.categories.destroy', [
                                                                'restaurant' => $restaurantSlug,
                                                                'category' => $category->id,
                                                            ]) }}"
                                                            method="POST" onsubmit="return confirm('Are you sure?')">
                                            @endif


                                            @csrf
                                            @method('DELETE')

                                            <button type="submit" class="btn btn-danger btn-sm">
                                                <i class="fas fa-trash"></i>
                                            </button>

                                            </form>

                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">
                                        No categories found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
@endsection
