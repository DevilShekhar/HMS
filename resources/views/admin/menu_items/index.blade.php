@extends('layouts.app')
@section('content')
<section class="section premium-dashboard">
    <div class="premium-page-head">
        <div class="premium-page-title">
            <span class="mini-badge">Menu Management</span>
            <h2>Menu Items</h2>
            <p>Manage restaurant menu items.</p>
        </div>
        <div class="premium-head-actions">
            <a href="{{ route('restaurant.menu-items.create',[
                'restaurant'=>request()->route('restaurant')
            ]) }}"
               class="btn btn-primary">
                <i class="fas fa-plus"></i>
                Add Menu Item
            </a>
        </div>
    </div>
</section>
<section class="section premium-dashboard pt-0">
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    <div class="card premium-block">
        <div class="card-header premium-card-header">
            <div>
               <h4>Menu Items List</h4>
                <p class="header-subtext">
                    All menu items for this restaurant.
                </p>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Image</th>
                            <th>Menu Name</th>
                            <th>Branch</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Food Type</th>
                            <th>Status</th>
                            <th width="180">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($menuItems as $key => $item)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>
                                @if($item->image)
                                    <img src="{{ asset('storage/'.$item->image) }}" width="60" class="rounded">
                                @else
                                    <span class="badge bg-secondary"> No Image </span>
                                @endif
                            </td>
                            <td><strong>{{ $item->name }}</strong> </td>
                            <td> {{ $item->branch->name ?? '-' }} </td>
                            <td> {{ $item->category->name ?? '-' }} </td>
                            <td>  ₹{{ number_format($item->price,2) }} </td>
                            <td>
                                @if($item->food_type == 'veg')
                                    <span class="badge bg-success">
                                        Veg
                                    </span>
                                @else
                                    <span class="badge bg-danger">
                                        Non Veg
                                    </span>
                                @endif
                            </td>
                            <td>
                                @if($item->is_active)
                                    <span class="badge bg-success">
                                        Active
                                    </span>
                                @else
                                    <span class="badge bg-danger">
                                        Inactive
                                    </span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('restaurant.menu-items.edit',[
                                            'restaurant' => request()->route('restaurant'),
                                            'menu_item'  => $item->id
                                        ]) }}"
                                        class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i>
                                    </a>
                                    <form id="delete-form-{{ $item->id }}"
                                        method="POST"
                                        action="{{ route('restaurant.menu-items.destroy',[
                                            'restaurant'=>request()->route('restaurant'),
                                            'menu_item'=>$item->id
                                        ]) }}"
                                        style="display:inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button"
                                                onclick="deleteMenuItem({{ $item->id }})"
                                                class="btn btn-sm btn-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center">
                                No Menu Items Found
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
<script>
function deleteMenuItem(id)
{
    Swal.fire({
        title: 'Are you sure?',
        text: "This menu item will be deactivated.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, Delete',
        cancelButtonText: 'Cancel'
    }).then((result) => {

        if(result.isConfirmed)
        {
            document.getElementById(
                'delete-form-' + id
            ).submit();
        }

    });
}
</script>
@endsection