@extends('layouts.app')

@section('title', 'Create Table Allocation')

@section('content')
            @php
                $restaurant = request()->route('restaurant');
                $branch = request()->route('branch');

                if ($branch) {
                    $route = 'branch.table-allocations.store';

                    $params = [
                        'restaurant' => $restaurant,
                        'branch' => $branch,
                    ];
                } else {
                    $route = 'restaurant.table-allocations.store';

                    $params = [
                        'restaurant' => $restaurant,
                    ];
                }
            @endphp
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">New Table Allocation</h3>
                    </div>
                    <form action="{{ route($route, $params) }}" method="POST">
    @csrf
                        @csrf
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Branch <span class="text-danger">*</span></label>

                                            @if(auth()->user()->role == 'branch_manager')
                                                <input type="text"
                                                    class="form-control"
                                                    value="{{ $branches->first()->name }}"
                                                    readonly>

                                                <input type="hidden"
                                                    name="branch_id"
                                                    value="{{ $branches->first()->id }}">
                                            @else
                                                <select name="branch_id" class="form-control" required>
                                                    <option value="">-- Select Branch --</option>

                                                    @foreach($branches as $branch)
                                                        <option value="{{ $branch->id }}"
                                                            {{ old('branch_id') == $branch->id ? 'selected' : '' }}>
                                                            {{ $branch->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            @endif

                                            @error('branch_id')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Table <span class="text-danger">*</span></label>

                                        <select name="table_id" class="form-control" required>
                                            <option value="">-- Select Table --</option>

                                            @foreach($tables as $table)
                                                <option value="{{ $table->id }}" {{ old('table_id') == $table->id ? 'selected' : '' }}>
                                                    {{ $table->table_number }} ({{ $table->capacity }} Seats)
                                                </option>
                                            @endforeach
                                        </select>

                                        @error('table_id')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Waiter</label>
                                <select name="waiter_id" class="form-control" required>
                                    <option value="">-- Select Waiter --</option>
                                    @foreach($waiters as $waiter)
                                        <option value="{{ $waiter->id }}">{{ $waiter->name }} ({{ $waiter->email }})</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="row">
                                {{-- <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Allocation Date</label>
                                        <input type="date" name="allocation_date" class="form-control" required>
                                    </div>
                                </div> --}}
                                {{-- <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Shift</label>
                                        <select name="shift" class="form-control">
                                            <option value="">-- Select Shift --</option>
                                            <option value="morning">Morning</option>
                                            <option value="evening">Evening</option>
                                            <option value="night">Night</option>
                                        </select>
                                    </div>
                                </div> --}}
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">Create Allocation</button>
                            @php
                                $restaurant = request()->route('restaurant');
                                $branch = request()->route('branch');

                                if ($branch) {
                                    $cancelRoute = 'branch.table-allocations.index';

                                    $cancelParams = [
                                        'restaurant' => $restaurant,
                                        'branch' => $branch,
                                    ];
                                } else {
                                    $cancelRoute = 'restaurant.table-allocations.index';

                                    $cancelParams = [
                                        'restaurant' => $restaurant,
                                    ];
                                }
                            @endphp

                            <a href="{{ route($cancelRoute, $cancelParams) }}" class="btn btn-secondary">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
