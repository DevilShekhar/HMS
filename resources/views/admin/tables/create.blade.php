@extends('layouts.app')
@section('content')
    <section class="section premium-dashboard">
        <div class="premium-page-head">
            <div class="premium-page-title">
                <span class="mini-badge">Table Management</span>
                <h2>Create Table</h2>
                <p>Add a new restaurant table.</p>
            </div>
            <div class="premium-head-actions">
                <a href="{{ route('restaurant.tables.index', ['restaurant' => request()->route('restaurant')]) }}"
                    class="btn premium-btn ghost-btn">
                        <i class="fas fa-arrow-left"></i>
                        Back To Tables
                </a>
            </div>
        </div>
    </section>
    <section class="section premium-dashboard pt-0">
        <form action="{{ route('restaurant.tables.store',[ 'restaurant' => request()->route('restaurant')]) }}" method="POST">
            @csrf
            <div class="card premium-block">
                <div class="card-header premium-card-header">
                    <div>
                        <h4>Table Information</h4>
                        <p class="header-subtext">
                            Enter table details.
                        </p>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        @if(auth()->user()->role == 'owner')
                        <div class="col-md-6 mb-4">
                            <label class="form-label"> Branch</label>
                            <select name="branch_id" id="branch_id" class="form-control premium-input">
                                <option value=""> Select Branch</option>
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}">
                                        {{ $branch->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('branch_id')
                                <small class="text-danger">
                                    {{ $message }}
                                </small>
                            @enderror
                        </div>
                        @endif
                        @if(auth()->user()->role == 'branch_manager')
                            <input type="hidden" name="branch_id" value="{{ $branch->id }}">
                            <div class="col-md-6 mb-4">
                                <label class="form-label">  Branch </label>
                                <input type="text"  class="form-control premium-input" value="{{ $branch->name }}" readonly>
                            </div>
                        @endif
                        <div class="col-md-6 mb-4">
                            <label class="form-label"> Table Category </label>
                            <select name="cat_id" id="category_id" class="form-control premium-input">
                                <option value=""> Select Category  </option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" data-branch="{{ $category->branch_id }}"
                                        @if(auth()->user()->role == 'owner') style="display:none;"@endif>{{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('cat_id')
                                <small class="text-danger">
                                    {{ $message }}
                                </small>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="form-label">
                                Table Number
                            </label>
                            <input type="text" name="table_number"  value="{{ old('table_number') }}" class="form-control premium-input" placeholder="Ex: T-01">
                            @error('table_number')
                                <small class="text-danger">
                                    {{ $message }}
                                </small>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="form-label">
                                Capacity
                            </label>
                            <input type="number" name="capacity" value="{{ old('capacity',4) }}" min="1" class="form-control premium-input">
                            @error('capacity')
                                <small class="text-danger">
                                    {{ $message }}
                                </small>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i>
                    Create Table
                </button>
            </div>
        </form>
    </section>
    @if(auth()->user()->role == 'owner')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const branchSelect = document.getElementById('branch_id');
                const categorySelect = document.getElementById('category_id');
                branchSelect.addEventListener('change', function () {
                    let branchId = this.value;
                    categorySelect.value = '';
                    categorySelect.querySelectorAll('option').forEach(function(option){
                        if(option.value === ''){
                            option.style.display = '';
                            return;
                        }
                        if(option.dataset.branch == branchId){
                            option.style.display = '';
                        } else {
                            option.style.display = 'none';
                        }
                    });
                });
            });
        </script>
    @endif
@endsection