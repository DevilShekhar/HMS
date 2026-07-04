@extends('layouts.app')
@section('content')
    <section class="section premium-dashboard">
        <div class="premium-floating-header">
            <div class="header-content">
                <div class="header-left">
                    <div class="header-icon">
                        <i class="fas fa-tags"></i>
                    </div>
                    <div>
                        <span class="header-badge">
                            Table Categories
                        </span>
                        <h1>Manage Table Categories</h1>
                        <p>View, create, edit, and manage restaurant table categories from one place.</p>
                    </div>
                </div>
                <div class="header-right">
                    @php
                        $restaurantSlug = request()->route('restaurant');
                        $branchSlug = request()->route('branch');
                    @endphp
                    <div class="premium-head-actions">

                        @if (!empty($restaurantSlug) && !empty($branchSlug))
                            <a href="{{ route('branch.table-categories.create', [
                                'restaurant' => $restaurantSlug,
                                'branch' => $branchSlug,
                            ]) }}"
                                class="premium-back-btn">
                                <i class="fas fa-plus"></i>
                                Add Table Category
                            </a>
                        @elseif(!empty($restaurantSlug))
                            <a href="{{ route('restaurant.table-categories.create', [
                                'restaurant' => $restaurantSlug,
                            ]) }}"
                                class="premium-back-btn">
                                <i class="fas fa-plus"></i>
                                Add Table Category
                            </a>
                        @else
                            <a href="{{ route('table-categories.create') }}" class="premium-back-btn">
                                <i class="fas fa-plus"></i>
                                Add Table Category
                            </a>
                        @endif

                    </div>              
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
                    <h4>Table Category List</h4>
                    <p class="header-subtext">
                        View and manage all table categories.
                    </p>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle" id="tableExport">
                        <thead>
                            <tr>
                                <th width="60">#</th>
                                <th>Category Name</th>
                                <th>Branch</th>
                                <th>Status</th>
                                <th>Created By</th>
                                <th>Created Date</th>
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
                                        <strong>{{ $category->name }}</strong>
                                    </td>
                                    <td>
                                        {{ optional($category->branch)->name ?? '-' }}
                                    </td>
                                    <td>
                                        @if ($category->status == 1)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-danger">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        {{ optional($category->creator)->name ?? '-' }}
                                    </td>
                                    <td>
                                        {{ $category->created_at ? $category->created_at->format('d M Y h:i A') : '-' }}
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1">

                                            @php
                                                $restaurantSlug = request()->route('restaurant');
                                                $branchSlug = request()->route('branch');
                                            @endphp

                                            {{-- Edit --}}
                                            @if (auth()->user()->role === 'super_admin')
                                                <a href="{{ route('table-categories.edit', [
                                                    'table_category' => $category->id,
                                                ]) }}"
                                                    class="btn btn-warning btn-md">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            @elseif(!empty($restaurantSlug) && !empty($branchSlug))
                                                <a href="{{ route('branch.table-categories.edit', [
                                                    'restaurant' => $restaurantSlug,
                                                    'branch' => $branchSlug,
                                                    'table_category' => $category->id,
                                                ]) }}"
                                                    class="btn btn-warning btn-md">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            @elseif(!empty($restaurantSlug))
                                                <a href="{{ route('restaurant.table-categories.edit', [
                                                    'restaurant' => $restaurantSlug,
                                                    'table_category' => $category->id,
                                                ]) }}"
                                                    class="btn btn-warning btn-md">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            @endif


                                            {{-- Delete --}}
                                            @if (auth()->user()->role === 'super_admin')
                                                <form
                                                    action="{{ route('table-categories.destroy', [
                                                        'table_category' => $category->id,
                                                    ]) }}"
                                                    method="POST" class="delete-form">
                                                @elseif(!empty($restaurantSlug) && !empty($branchSlug))
                                                    <form
                                                        action="{{ route('branch.table-categories.destroy', [
                                                            'restaurant' => $restaurantSlug,
                                                            'branch' => $branchSlug,
                                                            'table_category' => $category->id,
                                                        ]) }}"
                                                        method="POST" class="delete-form">
                                                    @elseif(!empty($restaurantSlug))
                                                        <form
                                                            action="{{ route('restaurant.table-categories.destroy', [
                                                                'restaurant' => $restaurantSlug,
                                                                'table_category' => $category->id,
                                                            ]) }}"
                                                            method="POST" class="delete-form">
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
                                    <td colspan="6" class="text-center py-4">
                                        <div class="text-muted">
                                            No table categories found.
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>               
            </div>
        </div>
    </section>
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
@endsection
