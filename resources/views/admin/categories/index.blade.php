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
            @endphp

            <div class="premium-head-actions">

                @if ($restaurantSlug)
                    <a href="{{ route('restaurant.categories.create', [
                        'restaurant' => $restaurantSlug,
                    ]) }}"
                        class="btn premium-btn">
                        <i class="fas fa-plus"></i>
                        Add Category
                    </a>
                @else
                    <a href="{{ route('categories.create') }}" class="btn premium-btn">
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
                                            <img src="{{ asset('storage/' . $category->image) }}" width="50"
                                                height="50" class="rounded">
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
                                            <a href="{{ route('restaurant.categories.show', [
                                                'restaurant' => request()->route('restaurant'),
                                                'category' => $category->id,
                                            ]) }}"
                                                class="btn btn-info btn-sm">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('restaurant.categories.edit', [
                                                'restaurant' => request()->route('restaurant'),
                                                'category' => $category->id,
                                            ]) }}"
                                                class="btn btn-warning btn-sm">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form
                                                action="{{ route('restaurant.categories.destroy', ['restaurant' => request()->route('restaurant'), 'category' => $category->id]) }}"
                                                method="POST" onsubmit="return confirm('Are you sure?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm"> <i
                                                        class="fas fa-trash"></i></button>
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
