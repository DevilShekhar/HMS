@extends('layouts.app')
@section('content')
    <section class="section premium-dashboard">
        <div class="premium-page-head">
            <div class="premium-page-title">
                <span class="mini-badge">Table Management</span>
                <h2>Edit Table</h2>
                <p>Update restaurant table.</p>
            </div>
            <div class="premium-head-actions">
                <a href="{{ route('restaurant.tables.index',['restaurant' => request()->route('restaurant')]) }}" class="btn premium-btn ghost-btn">
                    <i class="fas fa-arrow-left"></i>
                    Back To Tables
                </a>
            </div>
        </div>
    </section>
    <section class="section premium-dashboard pt-0">
        <form action="{{ route('restaurant.tables.update',['restaurant' => request()->route('restaurant'),'table' => $table->id]) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="card premium-block">
                <div class="card-header premium-card-header">
                    <div>
                        <h4>Table Information</h4>
                        <p class="header-subtext">
                            Update table details.
                        </p>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        @if(auth()->user()->role == 'owner')
                        <div class="col-md-6 mb-4">
                            <label>Branch</label>
                            <select name="branch_id" class="form-control premium-input">
                                @foreach($branches as $branch)
                                <option value="{{ $branch->id }}"
                                    {{ $table->branch_id == $branch->id ? 'selected' : '' }}>
                                    {{ $branch->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        @endif
                        @if(auth()->user()->role == 'branch_manager')
                        <input type="hidden" name="branch_id" value="{{ $branch->id }}">
                        <div class="col-md-6 mb-4">
                            <label>Branch</label>
                            <input type="text" class="form-control premium-input" value="{{ $branch->name }}" readonly>
                        </div>
                        @endif
                        <div class="col-md-6 mb-4">
                            <label>Table Category</label>
                            <select name="cat_id" class="form-control premium-input">
                                @foreach($categories as $category)
                                <option value="{{ $category->id }}"
                                    {{ $table->cat_id == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-4">
                            <label>Table Number</label>
                            <input type="text" name="table_number" class="form-control premium-input" value="{{ $table->table_number }}">
                        </div>
                        <div class="col-md-6 mb-4">
                            <label>Capacity</label>
                            <input type="number" name="capacity" class="form-control premium-input" value="{{ $table->capacity }}">
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-4">
                <button type="submit" class="btn btn-primary">
                    Update Table
                </button>
            </div>
        </form>
    </section>
@endsection