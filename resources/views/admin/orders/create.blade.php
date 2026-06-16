@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h4>Create Order</h4>
            </div>
            <form method="POST" action="{{ route('restaurant.orders.store', $restaurant->slug) }}">
                @csrf
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <label>Customer Name</label>
                            <input type="text" name="customer_name" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label>Mobile Number</label>
                            <input type="text" name="mobile_number" class="form-control">
                        </div>
                        @if (auth()->user()->role == 'waiter_head')
                            <div class="col-md-4">
                                <label>Order Type</label>
                                <select name="order_type" class="form-control">
                                    <option value="normal"> Normal </option>
                                    <option value="vip"> VIP</option>
                                </select>
                            </div>
                        @endif
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-4">
                            <label>Table Category</label>
                            <select id="table_category" class="form-control">
                                <option value="">Select Category</option>

                                @foreach ($tableCategories as $category)
                                    <option value="{{ $category->id }}">
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label>Table Number</label>
                            <select name="table_no" id="table_no" class="form-control">
                                <option value="">Select Table</option>
                            </select>
                        </div>
                    </div>
                    <hr>
                    <table class="table table-bordered" id="orderTable">
                        <thead>
                            <tr>
                                <th width="35%">Category</th>
                                <th width="35%">Menu Item</th>
                                <th width="15%">Qty</th>
                                <th width="15%">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <select class="form-control category">
                                        <option value="">
                                            Select Category
                                        </option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}">
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <select name="menu_item_id[]" class="form-control menuItem">
                                        <option value=""> Select Menu Item</option>
                                    </select>
                                </td>
                                <td>
                                    <input type="number" name="quantity[]" class="form-control" value="1"
                                        min="1">
                                </td>
                                <td>
                                    <button type="button" class="btn btn-danger removeRow">
                                        Remove
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <button type="button" id="addRow" class="btn btn-primary">
                        Add Item
                    </button>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-success"> Save Order</button>
                    <a href="{{ route('restaurant.orders.index', $restaurant->slug) }}" class="btn btn-secondary">
                        Back
                    </a>
                </div>
            </form>
        </div>
    </div>
    {{-- <script>
$(document).ready(function () {
    $('#table_category').on('change', function () {
        let categoryId = $(this).val();
        $('#table_no').html(
            '<option value="">Loading...</option>'
        );
        let url = "{{ route(
            'restaurant.orders.tables',
            [
                'restaurant' => $restaurant->slug,
                'categoryId' => 'CATID'
            ]
        ) }}";
        url = url.replace('CATID', categoryId);
        console.log(url);
        $.ajax({
            url: url,
            type: 'GET',
            success: function (data) {
                let options =
                    '<option value="">Select Table</option>';
                $.each(data, function (index, table) {
                    options +=
                        '<option value="' +
                        table.table_number +
                        '">' +
                        table.table_number +
                        '</option>';
                });
                $('#table_no').html(options);
            },
            error: function (xhr) {
                console.log(xhr.responseText);
                $('#table_no').html(
                    '<option value="">No Tables Found</option>'
                );
            }
        });
    });
});
    </script> --}}
    <script>
        document.addEventListener('change', function(e) {
            if (e.target.classList.contains('category')) {
                let categoryId = e.target.value;
                let menuSelect =
                    e.target.closest('tr')
                    .querySelector('.menuItem');
                let url =
                    "{{ route('restaurant.orders.menu-by-category', [
                        'restaurant' => $restaurant->slug,
                        'categoryId' => 'CATID',
                    ]) }}";
                url = url.replace(
                    'CATID',
                    categoryId
                );
                fetch(url)
                    .then(response => response.json())
                    .then(data => {
                        menuSelect.innerHTML =
                            '<option value="">Select Menu Item</option>';
                        data.forEach(item => {
                            menuSelect.innerHTML +=
                                '<option value="' + item.id + '">' +
                                item.name +
                                ' (₹' + item.price + ')' +
                                '</option>';
                        });
                    });
            }
        });
        document.getElementById('addRow')
            .addEventListener('click', function() {
                let row =
                    document.querySelector(
                        '#orderTable tbody tr'
                    ).cloneNode(true);
                row.querySelector('.category').value = '';
                row.querySelector('.menuItem').innerHTML =
                    '<option value="">Select Menu Item</option>';
                row.querySelector('input').value = 1;
                document.querySelector(
                    '#orderTable tbody'
                ).appendChild(row);
            });
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('removeRow')) {
                let rows =
                    document.querySelectorAll(
                        '#orderTable tbody tr'
                    );
                if (rows.length > 1) {
                    e.target.closest('tr').remove();
                }
            }
        });
    </script>
@endsection
