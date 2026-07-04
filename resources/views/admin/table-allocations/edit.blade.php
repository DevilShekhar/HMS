@extends('layouts.app')

@section('title', 'Edit Table Allocation')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Edit Table Allocation</h3>
                    </div>
                    @if(request()->routeIs('branch.*'))
                                    <form action="{{ route('branch.table-allocations.update', [
                            'restaurant' => app('restaurant')->slug,
                            'branch' => request()->route('branch'),
                            'table_allocation' => $allocation->id,
                        ]) }}" method="POST">
                    @else
                                            <form action="{{ route('restaurant.table-allocations.update', [
                                'restaurant' => app('restaurant')->slug,
                                'table_allocation' => $allocation->id,
                            ]) }}" method="POST">
                        @endif
                            @csrf
                            @method('PUT')
                            </form>
                            <div class="card-body">
                                <!-- Same fields as create.blade.php -->
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Branch</label>
                                            <select name="branch_id" class="form-control" required>
                                                @foreach($branches as $branch)
                                                    <option value="{{ $branch->id }}" {{ $allocation->branch_id == $branch->id ? 'selected' : '' }}>
                                                        {{ $branch->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Table</label>
                                            <select name="table_id" class="form-control" required>
                                                @foreach($tables as $table)
                                                    <option value="{{ $table->id }}" {{ $allocation->table_id == $table->id ? 'selected' : '' }}>
                                                        {{ $table->table_number }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>Waiter</label>
                                    <select name="waiter_id" class="form-control" required>
                                        @foreach($waiters as $waiter)
                                            <option value="{{ $waiter->id }}" {{ $allocation->waiter_id == $waiter->id ? 'selected' : '' }}>
                                                {{ $waiter->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">Update Allocation</button>
                                
                            </div>
                        </form>
                </div>
            </div>
        </div>
    </div>
@endsection
