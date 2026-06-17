@extends('layouts.app')
@section('content')
    <section class="section premium-dashboard">
        <div class="premium-page-head">
            <div class="premium-page-title">
                <span class="mini-badge">Table Management</span>
                <h2>Restaurant Tables</h2>
                <p>Manage restaurant tables.</p>
            </div>
            @php
                $restaurantSlug = request()->route('restaurant');
                $branchSlug = request()->route('branch');
            @endphp

            <div class="premium-head-actions">

                @if ($branchSlug)
                    <a href="{{ route('branch.tables.create', [
                        'restaurant' => $restaurantSlug,
                        'branch' => $branchSlug,
                    ]) }}"
                        class="btn btn-primary">
                        <i class="fas fa-plus"></i>
                        Add Table
                    </a>
                @else
                    <a href="{{ route('restaurant.tables.create', [
                        'restaurant' => $restaurantSlug,
                    ]) }}"
                        class="btn btn-primary">
                        <i class="fas fa-plus"></i>
                        Add Table
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
                    <h4>Table List</h4>
                    <p class="header-subtext">
                        View and manage all tables.
                    </p>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Table No</th>
                                <th>Category</th>
                                <th>Branch</th>
                                <th>Capacity</th>
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
                                                    class="btn btn-warning btn-sm">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            @elseif(!empty($restaurantSlug))
                                                <a href="{{ route('restaurant.tables.edit', [
                                                    'restaurant' => $restaurantSlug,
                                                    'table' => $table->id,
                                                ]) }}"
                                                    class="btn btn-warning btn-sm">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            @else
                                                <a href="{{ route('tables.edit', [
                                                    'table' => $table->id,
                                                ]) }}"
                                                    class="btn btn-warning btn-sm">
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
                                                    method="POST" onsubmit="return confirm('Delete this table?')">
                                                @elseif(!empty($restaurantSlug))
                                                    <form
                                                        action="{{ route('restaurant.tables.destroy', [
                                                            'restaurant' => $restaurantSlug,
                                                            'table' => $table->id,
                                                        ]) }}"
                                                        method="POST" onsubmit="return confirm('Delete this table?')">
                                                    @else
                                                        <form
                                                            action="{{ route('tables.destroy', [
                                                                'table' => $table->id,
                                                            ]) }}"
                                                            method="POST" onsubmit="return confirm('Delete this table?')">
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
                                    <td colspan="6" class="text-center">
                                        No tables found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    {{ $tables->links() }}
                </div>
            </div>
        </div>
    </section>
@endsection
