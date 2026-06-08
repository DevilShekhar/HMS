@extends('layouts.app')
@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h4>Edit Order</h4>
        </div>
        <form method="POST" action="{{ route('restaurant.orders.update',[$restaurant->slug,$order->id]) }}">
            @csrf
            @method('PUT')
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <label>Customer Name</label>
                        <input type="text" name="customer_name"  class="form-control" value="{{ $order->customer_name }}" required>
                    </div>
                    <div class="col-md-4">
                        <label>Mobile Number</label>
                        <input type="text" name="mobile_number" class="form-control"  value="{{ $order->mobile_number }}" required>
                    </div>
                    <div class="col-md-4">
                        <label>Table Number</label>
                        <input type="text"  name="table_no"  class="form-control" value="{{ $order->table_no }}" required>
                    </div>
                </div>
                @if(auth()->user()->role == 'waiter_head')
                <div class="row mt-3">
                    <div class="col-md-4">
                        <label>Order Type</label>
                        <select name="order_type" class="form-control">
                            <option value="normal" {{ $order->order_type == 'normal' ? 'selected' : '' }}> Normal </option>
                            <option value="vip" {{ $order->order_type == 'vip' ? 'selected' : '' }}>  VIP</option>
                        </select>
                    </div>
                </div>
                @endif
                <hr>
                <table class="table table-bordered" id="orderTable">
                    <thead>
                        <tr>
                            <th>Category</th>
                            <th>Menu Item</th>
                            <th width="120">Qty</th>
                            <th width="120">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($order->items as $item)
                        <tr>
                            <td>
                                <select class="form-control category">
                                    <option value="">
                                        Select Category
                                    </option>
                                    @foreach($categories as $category)
                                        <option
                                            value="{{ $category->id }}"
                                            {{ $item->menuItem->category_id == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <select name="menu_item_id[]"  class="form-control menuItem">
                                    <option value="{{ $item->menuItem->id }}" selected>
                                        {{ $item->menuItem->name }}
                                        (₹{{ $item->menuItem->price }})
                                    </option>
                            </select>
                            </td>
                            <td>
                                <input type="number"  name="quantity[]" value="{{ $item->quantity }}" min="1" class="form-control">
                            </td>
                            <td>
                                <button type="button"  class="btn btn-danger removeRow">  Remove </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                </table>
                <button type="button" id="addRow" class="btn btn-primary"> Add Item </button>
            </div>
            <div class="card-footer">
                <button type="submit"  class="btn btn-success">  Update Order </button>
                <a href="{{ route('restaurant.orders.index',$restaurant->slug) }}" class="btn btn-secondary">
                    Back
                </a>
            </div>
        </form>
    </div>
</div>
<script>
    document.addEventListener('change', function(e){
        if(e.target.classList.contains('category'))
        {
            let categoryId = e.target.value;
            let menuSelect =
                e.target.closest('tr')
                .querySelector('.menuItem');
            fetch(
                '/{{ $restaurant->slug }}/menu-by-category/' +
                categoryId
            )
            .then(response => response.json())
            .then(data => {
                menuSelect.innerHTML =
                    '<option value="">Select Menu Item</option>';
                data.forEach(item => {
                    menuSelect.innerHTML +=
                        '<option value="'+item.id+'">'+
                        item.name+
                        ' (₹'+item.price+')'+
                        '</option>';
                });
            });
        }
    });
    document.getElementById('addRow')
    .addEventListener('click', function(){
        let html = `
        <tr>
            <td>
                <select class="form-control category">

                    <option value="">
                        Select Category
                    </option>
                    @foreach($categories as $category)

                    <option value="{{ $category->id }}">
                        {{ $category->name }}
                    </option>
                    @endforeach
                </select>
            </td>
            <td>
                <select name="menu_item_id[]" class="form-control menuItem">
                    <option value=""> Select Menu Item  </option>
                </select>
            </td>
            <td>
                <input type="number"  name="quantity[]" value="1"  min="1"  class="form-control">
            </td>
            <td>
                <button type="button"  class="btn btn-danger removeRow">
                    Remove
                </button>
            </td>
        </tr>
        `;
        document.querySelector(
            '#orderTable tbody'
        ).insertAdjacentHTML(
            'beforeend',
            html
        );
    });
    document.addEventListener('click', function(e){
        if(e.target.classList.contains('removeRow'))
        {
            let rows =
                document.querySelectorAll(
                    '#orderTable tbody tr'
                );
            if(rows.length > 1)
            {
                e.target.closest('tr').remove();
            }
        }

    });
</script>
@endsection
