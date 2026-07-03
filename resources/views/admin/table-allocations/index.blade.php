@extends('layouts.app')

@section('title', 'Table Allocations')

@section('content')
@php
    if (request()->route('branch')) {
        $route = 'branch.table-allocations.create';

        $params = [
            'restaurant' => request()->route('restaurant'),
            'branch' => request()->route('branch'),
        ];
    } else {
        $route = 'restaurant.table-allocations.create';

        $params = [
            'restaurant' => request()->route('restaurant'),
        ];
    }
@endphp
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Table Allocations</h3>
                        <a href="{{ route($route, $params) }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> New Allocation
                        </a>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Table</th>
                                    <th>Waiter</th>
                                    <th>Branch</th>

                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($allocations as $allocation)
                                                            <tr>
                                                                <td>{{ $allocation->table->table_number }} ({{ $allocation->table->capacity }} pax)</td>
                                                                <td>{{ $allocation->waiter->name }}</td>
                                                                <td>{{ $allocation->branch->name }}</td>
                                                                <td>
                                                                    @if($allocation->is_active)
                                                                        <span class="badge badge-success">Active</span>
                                                                    @else
                                                                        <span class="badge badge-danger">Inactive</span>
                                                                    @endif
                                                                </td>
                                                                @php
                                                                    if (auth()->user()->role == 'owner') {
                                                                        $routePrefix = 'restaurant.table-allocations';

                                                                        $routeParams = [
                                                                            'restaurant' => app('restaurant')->slug,
                                                                        ];
                                                                    } else {
                                                                        $routePrefix = 'branch.table-allocations';

                                                                        $routeParams = [
                                                                            'restaurant' => app('restaurant')->slug,
                                                                            'branch' => auth()->user()->branch?->slug,
                                                                        ];
                                                                    }
                                                                @endphp
                                                                <td>
                                                                    <a href="{{ route($routePrefix . '.edit', array_merge($routeParams, [
                                        'table_allocation' => $allocation->id
                                    ])) }}" class="btn btn-warning btn-sm">
                                                                        <i class="fas fa-edit"></i>
                                                                    </a>

                                                                    <form action="{{ route($routePrefix . '.destroy', array_merge($routeParams, [
                                        'table_allocation' => $allocation->id
                                    ])) }}" method="POST" style="display:inline;">
                                                                        @csrf
                                                                        @method('DELETE')

                                                                        <button type="submit" class="btn btn-danger btn-sm"
                                                                            onclick="return confirm('Deactivate this allocation?')">
                                                                            <i class="fas fa-trash"></i>
                                                                        </button>
                                                                    </form>
                                                                </td>
                                                            </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">No allocations found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer">
                        {{ $allocations->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
