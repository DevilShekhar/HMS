@can('view-category')
@extends('layouts.app')
@section('content')
    <section class="section premium-dashboard">
    <div class="premium-floating-header">
        <div class="header-content">
            <div class="header-left">
                <div class="header-icon">
                    <i class="fas fa-clipboard-list"></i>
                </div>
                <div>
                    <span class="header-badge">
                        Category Management
                    </span>
                    <h1>Categories</h1>
                    <p>Manage restaurant categories.</p>
                </div>
            </div>

            @php
                $restaurantSlug = request()->route('restaurant');
                $branchSlug = request()->route('branch');
            @endphp

            <div class="header-right">

                @if (auth()->user()->role === 'super_admin')
                    <a href="{{ route('categories.create') }}" class="premium-back-btn">
                        <i class="fas fa-plus"></i>
                        Add Category
                    </a>

                @elseif (!empty($restaurantSlug) && !empty($branchSlug))
                    <a href="{{ route('branch.categories.create', [
                        'restaurant' => $restaurantSlug,
                        'branch' => $branchSlug,
                    ]) }}" class="premium-back-btn">
                        <i class="fas fa-plus"></i>
                        Add Category
                    </a>

                @elseif (!empty($restaurantSlug))
                    <a href="{{ route('restaurant.categories.create', [
                        'restaurant' => $restaurantSlug,
                    ]) }}" class="premium-back-btn">
                        <i class="fas fa-plus"></i>
                        Add Category
                    </a>
                @endif
            </div>
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
                    <table class="table table-hover align-middle" id="tableExport">
                        <thead>
                            <tr>
                                <th>#</th>
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
                                            <span class="status active">
                                                                <i class="fas fa-circle"></i>Active
                                                            </span>
                                        @else
                                            <span class="status inactive">
                                                                <i class="fas fa-circle"></i>Inactive
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
                                                    class="btn btn-warning btn-md">
                                                    <i class="fas fa-edit"></i>
                                                </a>

                                                <form action="{{ route('categories.destroy', $category->id) }}"
                                                    method="POST"  class="delete-form">

                                                    {{-- BRANCH LEVEL --}}
                                                @elseif (!empty($restaurantSlug) && !empty($branchSlug))
                                                    <a href="{{ route('branch.categories.edit', [
                                                        'restaurant' => $restaurantSlug,
                                                        'branch' => $branchSlug,
                                                        'category' => $category->id,
                                                    ]) }}"
                                                        class="btn btn-warning btn-md">
                                                        <i class="fas fa-edit"></i>
                                                    </a>

                                                    <form
                                                        action="{{ route('branch.categories.destroy', [
                                                            'restaurant' => $restaurantSlug,
                                                            'branch' => $branchSlug,
                                                            'category' => $category->id,
                                                        ]) }}"
                                                        method="POST"  class="delete-form">


                                                        {{-- RESTAURANT LEVEL --}}
                                                    @elseif (!empty($restaurantSlug))
                                                        <a href="{{ route('restaurant.categories.edit', [
                                                            'restaurant' => $restaurantSlug,
                                                            'category' => $category->id,
                                                        ]) }}"
                                                            class="btn btn-warning btn-md">
                                                            <i class="fas fa-edit"></i>
                                                        </a>

                                                        <form
                                                            action="{{ route('restaurant.categories.destroy', [
                                                                'restaurant' => $restaurantSlug,
                                                                'category' => $category->id,
                                                            ]) }}"
                                                            method="POST"  class="delete-form">
                                            @endif


                                            @csrf
                                            @method('DELETE')

                                            <button type="submit" class="btn btn-danger btn-md">
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
@if (session('success'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: "{{ session('success') }}",
                timer: 2000,
                showConfirmButton: false
            });
        });
    </script>
@endif

<script>
    document.addEventListener('DOMContentLoaded', function() {

        document.querySelectorAll('.delete-form').forEach(form => {

            form.addEventListener('submit', function(e) {

                e.preventDefault();

                Swal.fire({
                    title: 'Deactivate Category?',
                    text: 'This action can be reverted later.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes',
                    cancelButtonText: 'Cancel'
                }).then((result) => {

                    if (result.isConfirmed) {
                        form.submit();
                    }

                });

            });

        });

    });
</script>
@else
    @php
        abort(403);
    @endphp
@endcan
