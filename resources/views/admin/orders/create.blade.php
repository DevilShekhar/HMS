@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h4>Create Order</h4>
            </div>
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <form method="POST" action="{{ route('restaurant.orders.store', $restaurant->slug) }}" id="orderForm">
                @csrf
                <div class="card-body">
                    <!-- Customer & Table Details -->
                    <div class="row">

                        <div class="col-md-3">
                            @if (auth()->user()->role == 'customer')
                                <input type="hidden" name="customer_name" value="{{ auth()->user()->name }}">
                            @else
                                <label>Customer Name</label>

                                <input type="text" name="customer_name" class="form-control" value="{{ old('customer_name') }}"
                                    required>
                            @endif
                        </div>
                        <div class="col-md-3">
                            @if (auth()->user()->role == 'customer')
                                <input type="hidden" name="mobile_number" class="form-control "
                                    value="{{ auth()->user()->phone }}" readonly>
                            @else
                                <label>Mobile Number</label>
                                <input type="text" name="mobile_number" class="form-control" required id="mobile_number"
                                    value="{{ old('mobile_number') }}">
                                    @error('mobile_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            @endif
                        </div>
                        <div class="col-md-3">
                            @if (auth()->user()->role == 'customer')
                                <input type="hidden" name="email" class="form-control " value="{{ auth()->user()->email }}"
                                    readonly>
                            @else
                                <label>Email</label>
                                <input type="text" name="email" class="form-control" value="{{ old('email') }}" required>
                            @endif
                        </div>
                        @if (auth()->user()->role == 'waiter_head')
                            <div class="col-md-3">
                                <label>Order Type</label>
                                <select name="order_type" class="form-control">
                                    <option value="normal"> Normal </option>
                                    <option value="vip"> VIP</option>
                                </select>
                            </div>
                        @endif
                        <div class="col-md-3">
                            <label>Table Area <span class="text-danger">*</span></label>

                            <select id="table_category" class="form-control @error('table_category') is-invalid @enderror"
                                name="table_category" required>
                                <option value="">Select Table Area</option>

                                @foreach ($tableCategories as $category)
                                    <option value="{{ $category->id }}" {{ old('table_category') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>

                            @error('table_category')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3">
                            <label>Table Number <span class="text-danger">*</span></label>

                            <select name="table_no" id="table_no"
                                class="form-control @error('table_no') is-invalid @enderror" required>
                                <option value="">Select Table</option>
                            </select>

                            @error('table_no')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label>Date of Birth</label>
                            <input type="date" name="birth_date" class="form-control" value="{{ old('birth_date') }}">
                        </div>

                        <div class="col-md-4">
                            <label>Anniversary Date</label>
                            <input type="date" name="anniversary_date" class="form-control"
                                value="{{ old('anniversary_date') }}">
                        </div>
                    </div>

                    <hr>

                    <!-- POS Layout - Full Width -->
                    <div class="row">
                        <!-- Left: Menu Items -->
                        <div class="col-md-8">
                            <!-- Category Filter Dropdown & Search -->
                            <div class="row">
                                <div class="col-md-4">
                                    <label class="filter-label">Filter by Category</label>
                                    <select id="categoryFilter" class="form-control">
                                        <option value="all">All Categories</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->name }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-8">
                                    <label class="filter-label">Search</label>
                                    <div class="search-wrapper">
                                        <input type="text" id="menuSearch" class="form-control search-input"
                                            placeholder="Search products by name...">
                                        <span class="search-icon">🔍</span>
                                        <div id="searchResults" class="search-results"></div>
                                    </div>
                                </div>
                            </div>

                            <br>

                            <!-- Menu Items Grid -->
                            <div class="menu-grid" id="menuItemsContainer">
                                @foreach ($menuItems as $item)
                                    <div class="menu-card" data-id="{{ $item->id }}" data-name="{{ $item->name }}"
                                        data-price="{{ $item->price }}"
                                        data-category="{{ $item->category->name ?? 'Uncategorized' }}">
                                        <div class="menu-img">
                                            @if ($item->image)
                                                <img src="{{ asset($item->image) }}" alt="{{ $item->name }}"
                                                    class="menu-img-content">
                                            @else
                                                <span class="menu-placeholder">🍽️</span>
                                            @endif
                                        </div>
                                        <div class="menu-name">{{ $item->name }}</div>
                                        <div class="menu-price">₹{{ number_format($item->price, 2) }}</div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Right: Order Summary -->
                        <div class="col-md-4">
                            <div class="order-panel">
                                <div class="order-header">
                                    <div class="order-header-content">
                                        <h6 class="order-title">Current Order</h6>
                                        <span class="badge badge-secondary" id="itemCountBadge">0 items</span>
                                    </div>
                                </div>

                                <!-- Order Items -->
                                <div id="orderItemsList" class="order-items-list">
                                    <div id="emptyCartMsg" class="empty-cart">
                                        <div class="empty-cart-icon">🛒</div>
                                        <p>No items added yet</p>
                                        <small>Click on menu items to add</small>
                                    </div>
                                    <div id="orderItemsDynamic"></div>
                                </div>

                                <!-- Order Details -->


                                <!-- Price Summary -->
                                <div class="price-summary">
                                    <div class="summary-row total-row">
                                        <span>Grand Total</span>
                                        <span id="grandTotalAmount">₹0.00</span>
                                    </div>
                                </div>

                                <!-- Place Order Button -->
                                <div class="place-order-wrapper">
                                    <button type="button" class="btn btn-primary btn-block place-order-btn"
                                        id="placeOrderBtn">
                                        📝 Place Order
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>





                    <!-- Hidden fields for menu items -->
                    <div id="hiddenItemsContainer"></div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-success" id="submitOrderBtn" style="display:none;">Save
                        Order</button>
                    <a href="{{ route('restaurant.orders.index', $restaurant->slug) }}" class="btn btn-secondary">Back</a>
                </div>

            </form>

        </div>
        <div id="customerHistory" class="card mt-3" style="display:none;">
            <div class="card-header py-2">
                <h6 class="mb-0">
                    <i class="fas fa-user-check text-success"></i>
                    Returning Customer
                </h6>
            </div>

            <div class="card-body py-2">
                <div class="row text-center mb-2">
                    <div class="col-6">
                        <small class="text-muted d-block">Visits</small>
                        <strong id="visitCount">0</strong>
                    </div>
                    <div class="col-6">
                        <small class="text-muted d-block">Last Visit</small>
                        <strong id="lastVisit">-</strong>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-sm table-bordered mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Date</th>
                                <th>Order Type</th>
                                <th width="80">Amount</th>
                            </tr>
                        </thead>
                        <tbody id="historyRows">
                            <!-- JS Data -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        // Order state
        let order = {};
        let selectedPayment = 'cash';
        const menuItemsData = @json($menuItems);
        const cgstPercent = 0;
        const sgstPercent = 0;

        // Category filter dropdown
        document.addEventListener('DOMContentLoaded', function () {
            const categoryFilter = document.getElementById('categoryFilter');
            if (categoryFilter) {
                categoryFilter.addEventListener('change', function () {
                    filterByCategory(this.value);
                });
            }
        });

        function filterByCategory(category) {
            const allCards = document.querySelectorAll('.menu-card');
            if (category === 'all') {
                allCards.forEach(card => card.style.display = '');
            } else {
                allCards.forEach(card => {
                    const cardCat = card.dataset.category;
                    card.style.display = (cardCat === category) ? '' : 'none';
                });
            }
        }

        function updateCardHighlights() {
            document.querySelectorAll('.menu-card').forEach(card => {
                const id = card.dataset.id;
                const qty = order[id]?.qty || 0;
                card.classList.toggle('active', qty > 0);

                const oldBadge = card.querySelector('.menu-card-qty-badge');
                if (oldBadge) oldBadge.remove();

                if (qty > 0) {
                    const badge = document.createElement('div');
                    badge.className = 'menu-card-qty-badge';
                    badge.textContent = qty;
                    card.appendChild(badge);
                }
            });
        }

        function addItemToOrder(id, name, price) {
            if (order[id]) {
                order[id].qty++;
            } else {
                order[id] = {
                    id,
                    name,
                    price: parseFloat(price),
                    qty: 1
                };
            }
            renderOrderSummary();
            updateCardHighlights();
            updateHiddenFields();
        }

        function renderOrderSummary() {
            const container = document.getElementById('orderItemsDynamic');
            const emptyMsg = document.getElementById('emptyCartMsg');
            const itemCountBadge = document.getElementById('itemCountBadge');
            const keys = Object.keys(order);
            const totalQty = keys.reduce((sum, id) => sum + order[id].qty, 0);

            itemCountBadge.textContent = totalQty + ' item' + (totalQty !== 1 ? 's' : '');

            if (keys.length === 0) {
                if (emptyMsg) emptyMsg.style.display = 'block';
                if (container) container.innerHTML = '';
                updateTotals();
                return;
            }

            if (emptyMsg) emptyMsg.style.display = 'none';

            let html = '';
            for (let id in order) {
                const item = order[id];
                const lineTotal = item.price * item.qty;
                html += `
                            <div class="order-item">
                                <div>
                                    <div class="order-item-name">${escapeHtml(item.name)}</div>
                                    <div class="order-item-price">₹${item.price.toFixed(2)} each</div>
                                </div>
                                <div class="order-item-controls">
                                    <div class="qty-control">
                                        <button type="button" class="qty-btn" onclick="changeQty(${id}, -1)">−</button>
                                        <span class="qty-value">${item.qty}</span>
                                        <button type="button" class="qty-btn" onclick="changeQty(${id}, 1)">+</button>
                                    </div>
                                    <div class="order-item-total">₹${lineTotal.toFixed(2)}</div>
                                    <button type="button" class="remove-item-btn" onclick="removeItem(${id})">✕</button>
                                </div>
                            </div>
                        `;
            }
            container.innerHTML = html;
            updateTotals();
        }

        function updateHiddenFields() {
            const container = document.getElementById('hiddenItemsContainer');
            if (!container) return;

            let html = '';
            for (let id in order) {
                const item = order[id];
                html += `
                            <input type="hidden" name="menu_item_id[]" value="${id}">
                            <input type="hidden" name="quantity[]" value="${item.qty}">
                        `;
            }
            container.innerHTML = html;
        }

        function updateTotals() {
            let subtotal = 0;
            for (let id in order) {
                subtotal += order[id].price * order[id].qty;
            }

            const grandTotal = subtotal;
            document.getElementById('grandTotalAmount').textContent = '₹' + grandTotal.toFixed(2);
        }

        function changeQty(id, delta) {
            if (order[id]) {
                order[id].qty += delta;
                if (order[id].qty <= 0) delete order[id];
                renderOrderSummary();
                updateCardHighlights();
                updateHiddenFields();
            }
        }

        function removeItem(id) {
            delete order[id];
            renderOrderSummary();
            updateCardHighlights();
            updateHiddenFields();
        }

        function selectPaymentMethod(element) {
            document.querySelectorAll('.pos-pay-btn').forEach(btn => btn.classList.remove('active', 'btn-primary'));
            document.querySelectorAll('.pos-pay-btn').forEach(btn => btn.classList.add('btn-outline-secondary'));
            element.classList.remove('btn-outline-secondary');
            element.classList.add('active', 'btn-primary');
            selectedPayment = element.dataset.method;
            document.getElementById('paymentMethodInput').value = selectedPayment;
        }

        function escapeHtml(str) {
            return String(str).replace(/[&<>]/g, function (m) {
                if (m === '&') return '&amp;';
                if (m === '<') return '&lt;';
                if (m === '>') return '&gt;';
                return m;
            });
        }

        // Menu card click
        document.addEventListener('click', function (e) {
            const card = e.target.closest('.menu-card');
            if (card) {
                addItemToOrder(card.dataset.id, card.dataset.name, parseFloat(card.dataset.price));
            }
        });

        // Search functionality
        const searchInput = document.getElementById('menuSearch');
        const searchResults = document.getElementById('searchResults');

        if (searchInput) {
            searchInput.addEventListener('input', function () {
                const query = this.value.toLowerCase().trim();

                if (query.length === 0) {
                    searchResults.classList.remove('show');
                    searchResults.style.display = 'none';
                    const categoryFilter = document.getElementById('categoryFilter');
                    if (categoryFilter) {
                        filterByCategory(categoryFilter.value);
                    } else {
                        document.querySelectorAll('.menu-card').forEach(card => card.style.display = '');
                    }
                    return;
                }

                const filtered = menuItemsData.filter(item =>
                    item.name.toLowerCase().includes(query)
                );

                if (filtered.length === 0) {
                    searchResults.innerHTML = '<div class="search-result-item text-muted">No items found</div>';
                    searchResults.style.display = 'block';
                    searchResults.classList.add('show');
                    document.querySelectorAll('.menu-card').forEach(card => card.style.display = 'none');
                    return;
                }

                let html = '';
                filtered.forEach(item => {
                    html += `
                                <div class="search-result-item" data-id="${item.id}" data-name="${escapeHtml(item.name)}" data-price="${item.price}">
                                    <div style="display:flex; justify-content:space-between;">
                                        <strong>${escapeHtml(item.name)}</strong>
                                        <span style="color:#007bff; font-weight:600;">₹${parseFloat(item.price).toFixed(2)}</span>
                                    </div>
                                </div>
                            `;
                });
                searchResults.innerHTML = html;
                searchResults.style.display = 'block';
                searchResults.classList.add('show');

                document.querySelectorAll('.menu-card').forEach(card => card.style.display = 'none');

                document.querySelectorAll('.search-result-item').forEach(result => {
                    result.addEventListener('click', function () {
                        addItemToOrder(this.dataset.id, this.dataset.name, parseFloat(this.dataset
                            .price));
                        searchInput.value = '';
                        searchResults.classList.remove('show');
                        searchResults.style.display = 'none';
                        const categoryFilter = document.getElementById('categoryFilter');
                        if (categoryFilter) {
                            filterByCategory(categoryFilter.value);
                        } else {
                            document.querySelectorAll('.menu-card').forEach(card => card.style
                                .display = '');
                        }
                    });
                });
            });
        }

        document.addEventListener('click', function (e) {
            if (searchInput && !searchInput.contains(e.target) && searchResults && !searchResults.contains(e
                .target)) {
                searchResults.classList.remove('show');
                searchResults.style.display = 'none';
            }
        });

        // Place Order
        document.getElementById('placeOrderBtn')?.addEventListener('click', function () {
            if (Object.keys(order).length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Empty Order',
                    text: 'Please add at least one item to the order!',
                    confirmButtonColor: '#007bff'
                });
                return;
            }

            let subtotal = 0;
            for (let id in order) {
                subtotal += order[id].price * order[id].qty;
            }

            const cgstAmount = (subtotal * cgstPercent) / 100;
            const sgstAmount = (subtotal * sgstPercent) / 100;
            const grandTotal = subtotal + cgstAmount + sgstAmount;

            let taxHtml = '';
            if (cgstPercent > 0) taxHtml +=
                `<p><strong>CGST (${cgstPercent}%):</strong> ₹${cgstAmount.toFixed(2)}</p>`;
            if (sgstPercent > 0) taxHtml +=
                `<p><strong>SGST (${sgstPercent}%):</strong> ₹${sgstAmount.toFixed(2)}</p>`;

            Swal.fire({
                title: 'Confirm Order?',
                html: `
                            <div style="text-align:left;">
                                <p><strong>Subtotal:</strong> ₹${subtotal.toFixed(2)}</p>
                                ${taxHtml}
                                <hr>
                                <p><strong>Grand Total:</strong> ₹${grandTotal.toFixed(2)}</p>
                                <p><strong>Payment:</strong> ${selectedPayment.toUpperCase()}</p>
                            </div>
                        `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#007bff',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, Place Order'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('orderForm').submit();
                }
            });
        });

        // Initialize
        document.addEventListener('DOMContentLoaded', function () {
            updateHiddenFields();
        });

        // Table category and table number selection
        document.addEventListener('DOMContentLoaded', function () {
            const tableCategory = document.getElementById('table_category');
            const tableNo = document.getElementById('table_no');

            if (tableCategory) {
                tableCategory.addEventListener('change', function () {
                    let categoryId = this.value;
                    if (!categoryId) {
                        tableNo.innerHTML = '<option value="">Select Table</option>';
                        return;
                    }
                    @if (auth()->user()->branch_id)
                        let url =
                            "{{ route('branch.orders.tables', ['restaurant' => $restaurant->slug, 'branch' => auth()->user()->branch->slug, 'categoryId' => 'CATID']) }}";
                    @else
                        let url =
                            "{{ route('restaurant.orders.tables', ['restaurant' => $restaurant->slug, 'categoryId' => 'CATID']) }}";
                    @endif
                    url = url.replace('CATID', categoryId);

                    fetch(url)
                        .then(response => response.json())
                        .then(data => {
                            let options = '<option value="">Select Table</option>';
                            data.forEach(table => {

                                let disabled = table.occupied ? '' : '';

                                let text = table.table_number;

                                if (table.occupied) {
                                    text += ' (Occupied)';
                                }

                                options += `
                                                <option
                                                    value="${table.table_number}"
                                                    ${disabled}>
                                                    ${text}
                                                </option>
                                            `;
                            });

                            tableNo.innerHTML = options;
                        })
                        .catch(() => {
                            tableNo.innerHTML = '<option value="">No Tables Found</option>';
                        });
                });
            }
        });
        $('#mobile_number').on('blur', function () {

            let phone = $(this).val();

            if (!phone) {
                return;
            }

            $.get("{{ route('customer.history') }}", {
                phone: phone
            }, function (response) {

                if (!response.found) {
                    $('#customerHistory').hide();
                    return;
                }

                $('#customerHistory').show();

                $('#visitCount').text(response.total_visits);
                $('#lastVisit').text(response.last_visit);

                let html = '';

                response.orders.forEach(order => {

                    html += `
                                <tr>
                                   <td>${new Date(order.created_at).toLocaleDateString('en-GB')}</td>
                                    <td>
                                        <span class="badge badge-${order.order_type === 'vip' ? 'warning' : 'primary'}">
                                            ${order.order_type}
                                        </span>
                                    </td>
                                    <td>₹${order.total}</td>
                                </tr>
                                `;
                });

                $('#historyRows').html(html);
            });
        });
    </script>
@endpush
