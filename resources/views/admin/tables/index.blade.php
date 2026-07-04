@extends('layouts.app')
@section('content')
    <section class="section premium-dashboard">
        <div class="premium-floating-header">
            <div class="header-content">
                <div class="header-left">
                    <div class="header-icon">
                      <i class="fas fa-th-large"></i>
                    </div>
                    <div>
                        <span class="header-badge">
                            Table Management
                        </span>
                        <h1>Manage Tables</h1>
                        <p>View, create, edit, and manage all restaurant tables from one place.</p>
                    </div>
                </div>
                <div class="header-right">
                    @php
                        $restaurantSlug = request()->route('restaurant');
                        $branchSlug = request()->route('branch');
                    @endphp
                    <div class="premium-head-actions">
                        @if ($branchSlug)
                            <a href="{{ route('branch.tables.create', ['restaurant' => $restaurantSlug,'branch' => $branchSlug,]) }}" class="premium-back-btn"><i class="fas fa-plus"></i> Add Table</a>
                        @else
                            <a href="{{ route('restaurant.tables.create', ['restaurant' => $restaurantSlug,]) }}" class="premium-back-btn"><i class="fas fa-plus"></i>Add Table</a>
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
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle" id="tableExport">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Table No</th>
                                <th>Category</th>
                                <th>Branch</th>
                                <th>Capacity</th>
                                <th>Status</th>
                                <th width="180">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tables as $table)
                                <tr>
                                    <td>
                                        {{ $loop->iteration + ($tables->currentPage() - 1) * $tables->perPage() }}
                                    </td>
                                    <td>
                                        <strong>{{ $table->table_number }}</strong>
                                    </td>
                                    <td>
                                        {{ optional($table->category)->name }}
                                    </td>
                                    <td>
                                        {{ optional($table->branch)->name }}
                                    </td>
                                    <td>
                                        {{ $table->capacity }}
                                    </td>
                                    <td>
                                        @if ($table->status == 1)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-danger">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1">

                                            @php
                                                $restaurantSlug = request()->route('restaurant');
                                                $branchSlug = request()->route('branch');
                                            @endphp

                                            {{-- Edit --}}
                                            @if (!empty($restaurantSlug) && !empty($branchSlug))
                                                <a href="{{ route('branch.tables.edit', [
                                                    'restaurant' => $restaurantSlug,
                                                    'branch' => $branchSlug,
                                                    'table' => $table->id,
                                                ]) }}"
                                                    class="btn btn-warning btn-md">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            @elseif(!empty($restaurantSlug))
                                                <a href="{{ route('restaurant.tables.edit', [
                                                    'restaurant' => $restaurantSlug,
                                                    'table' => $table->id,
                                                ]) }}"
                                                    class="btn btn-warning btn-md">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            @else
                                                <a href="{{ route('tables.edit', [
                                                    'table' => $table->id,
                                                ]) }}"
                                                    class="btn btn-warning btn-md">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            @endif


                                            {{-- Delete --}}
                                            @if (!empty($restaurantSlug) && !empty($branchSlug))
                                                <form
                                                    action="{{ route('branch.tables.destroy', [
                                                        'restaurant' => $restaurantSlug,
                                                        'branch' => $branchSlug,
                                                        'table' => $table->id,
                                                    ]) }}"
                                                    method="POST" class="delete-form">
                                                @elseif(!empty($restaurantSlug))
                                                    <form
                                                        action="{{ route('restaurant.tables.destroy', [
                                                            'restaurant' => $restaurantSlug,
                                                            'table' => $table->id,
                                                        ]) }}"
                                                        method="POST" class="delete-form">
                                                    @else
                                                        <form
                                                            action="{{ route('tables.destroy', [
                                                                'table' => $table->id,
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
                                    <td colspan="6" class="text-center">
                                        No tables found.
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
