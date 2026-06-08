@extends('layouts.app')
@section('content')
<section class="section premium-dashboard">
    <div class="premium-page-head">
        <div class="premium-page-title">
            <span class="mini-badge">Menu Management</span>
            <h2>Edit Menu Item</h2>
            <p>Update menu item details.</p>
        </div>
        <div class="premium-head-actions">
            <a href="{{ route('restaurant.menu-items.index',[
                'restaurant'=>request()->route('restaurant')
            ]) }}"
               class="btn premium-btn ghost-btn">
                <i class="fas fa-arrow-left"></i>
                Back To Menu Items
            </a>
        </div>
    </div>
</section>
<section class="section premium-dashboard pt-0">
    <form action="{{ route('restaurant.menu-items.update',['restaurant'=>request()->route('restaurant'),'menu_item'=>$menuItem->id]) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="card premium-block">
            <div class="card-header premium-card-header">
                <div>
                    <h4>Menu Information</h4>
                    <p class="header-subtext">
                        Update menu item information.
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
                                {{ $menuItem->branch_id == $branch->id ? 'selected' : '' }}>
                                {{ $branch->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    @endif
                    <div class="col-md-6 mb-4">
                        <label>Category</label>
                        <select name="category_id"  class="form-control premium-input">
                            @foreach($categories as $category)
                            <option value="{{ $category->id }}"
                                {{ $menuItem->category_id == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-4">
                        <label>Menu Name</label>
                        <input type="text" name="name"  value="{{ $menuItem->name }}"  class="form-control premium-input">
                    </div>
                    <div class="col-md-6 mb-4">
                        <label>Price</label>
                        <input type="number" step="0.01"  name="price"  value="{{ $menuItem->price }}" class="form-control premium-input">
                    </div>
                    <div class="col-md-6 mb-4">
                        <label>Food Type</label>
                        <select name="food_type"class="form-control premium-input">
                            <option value="veg" {{ $menuItem->food_type == 'veg' ? 'selected' : '' }}>  Veg </option>
                            <option value="non_veg"  {{ $menuItem->food_type == 'non_veg' ? 'selected' : '' }}>  Non Veg</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-4">
                        <label>Available</label>
                        <select name="is_available" class="form-control premium-input">
                            <option value="1"  {{ $menuItem->is_available ? 'selected' : '' }}>  Yes </option>
                            <option value="0" {{ !$menuItem->is_available ? 'selected' : '' }}> No </option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-4">
                        <label>Status</label>
                        <select name="is_active"  class="form-control premium-input">
                            <option value="1" {{ $menuItem->is_active == 1 ? 'selected' : '' }}>  Active </option>
                            <option value="0"  {{ $menuItem->is_active == 0 ? 'selected' : '' }}> Inactive</option>
                        </select>
                    </div>
                    <div class="col-md-12 mb-4">
                        <label>Description</label>
                        <textarea name="description" rows="4" class="form-control premium-input">{{ $menuItem->description }}</textarea>
                    </div>
                    <div class="col-md-6 mb-4">
                        <label>Image</label>
                        <input type="file" name="image" class="form-control premium-input">
                    </div>
                    @if($menuItem->image)
                    <div class="col-md-6 mb-4">
                        <label>Current Image</label>
                        <br>
                        <img src="{{ asset('storage/'.$menuItem->image) }}" width="100" class="rounded">
                    </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="mt-4">
            <button type="submit" class="btn btn-primary"> Update Menu Item </button>
        </div>
    </form>
</section>
@endsection