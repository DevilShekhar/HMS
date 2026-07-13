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
    <section class="section premium-dashboard">
        <div class="premium-floating-header">
            <div class="header-content">
                <div class="header-left">
                    <div class="header-icon">
                      <i class="fas fa-th-large"></i>
                    </div>
                    <div>
                        <span class="header-badge">
                            Table Allocations Management
                        </span>
                        <h1>Manage Table Allocations</h1>
                        <p>View, create, edit, and manage all Restaurant Tables Allocations  from one place.</p>
                    </div>
                </div>
                <div class="header-right">
                    <a href="{{ route($route, $params) }}" class="premium-back-btn">
                        <i class="fas fa-plus"></i> New Allocation
                    </a>
                </div>
            </div>
        </div>
    </section>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <table class="table table-bordered table-hover" id="tableExport">
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
                                        <td>{{ $allocation->table->table_number }} ({{ $allocation->table->capacity }} Seater)</td>
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
                                                $routeParams = ['restaurant' => app('restaurant')->slug,];
                                            } else {
                                                $routePrefix = 'branch.table-allocations';
                                                $routeParams = ['restaurant' => app('restaurant')->slug,'branch' => auth()->user()->branch?->slug,];
                                            }
                                        @endphp
                                        <td>
                                            <a href="{{ route($routePrefix . '.edit', array_merge($routeParams, ['table_allocation' => $allocation->id])) }}" class="btn btn-warning btn-md">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route($routePrefix . '.destroy', array_merge($routeParams, ['table_allocation' => $allocation->id])) }}" method="POST" style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-md"
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
                </div>
            </div>
        </div>
    </div>
@endsection
