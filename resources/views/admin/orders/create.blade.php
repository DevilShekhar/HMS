@extends('layouts.app')

@section('content')


    <div class="de-wrapper">
        <nav class="de-top-nav">

            <a href="#" class="d-flex align-items-center text-decoration-none">

                <div class="de-logo-icon">
                    🍽️
                </div>

                <div class="de-brand">
                    <h2>{{ $restaurant->name }}</h2>

                    <small>
                        @if(isset($branch) && $branch)
                            📍 {{ $branch->name }}
                        @else
                            Restaurant Panel
                        @endif
                    </small>
                </div>

            </a>
            <div class="de-nav-links">
                @if(auth()->user()->branch_id)
                    <a href="{{ route('branch.orders.index', [
                        'restaurant' => $restaurant->slug,
                        'branch' => auth()->user()->branch->slug,
                    ]) }}" class="de-back-btn">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                @else
                    <a href="{{ route('restaurant.orders.index', [
                        'restaurant' => $restaurant->slug,
                    ]) }}" class="de-back-btn">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                @endif
            </div>

        </nav>
        <!-- ============================
                 MAIN CONTENT
                 ============================ -->
        <div class="de-main-grid">
            <!-- LEFT: MENU SECTION -->
            <div>
                <!-- Customer Form -->
                <div class="de-sidebar-form">
                    <div class="de-form-title">👤 Order Details</div>
                    <form method="POST" action="{{ route('restaurant.orders.store', $restaurant->slug) }}" id="orderForm">
                        @csrf
                        <div class="de-form-row">
                            @if (auth()->user()->role == 'customer')
                                <input type="hidden" name="customer_name" value="{{ auth()->user()->name }}">
                                <input type="hidden" name="mobile_number" value="{{ auth()->user()->phone }}">
                                <input type="hidden" name="email" value="{{ auth()->user()->email }}">
                            @else
                                <div class="de-field">
                                    <label>Customer Name <span class="de-required">*</span></label>
                                    <input type="text" name="customer_name" placeholder="John Doe"
                                        value="{{ old('customer_name') }}"
                                        class="{{ $errors->has('customer_name') ? 'de-field-error' : '' }}" required>
                                    @error('customer_name')
                                        <span class="de-error-message">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="de-field">
                                    <label>Mobile Number <span class="de-required">*</span></label>
                                    <input type="text" name="mobile_number" id="mobile_number" placeholder="+91 98765 00000"
                                        value="{{ old('mobile_number') }}"
                                        class="{{ $errors->has('mobile_number') ? 'de-field-error' : '' }}" required>
                                    @error('mobile_number')
                                        <span class="de-error-message">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="de-field">
                                    <label>Email</label>
                                    <input type="email" name="email" placeholder="john@example.com" value="{{ old('email') }}"
                                        class="{{ $errors->has('email') ? 'de-field-error' : '' }}">
                                    @error('email')
                                        <span class="de-error-message">{{ $message }}</span>
                                    @enderror
                                </div>
                            @endif

                            @if (auth()->user()->role == 'waiter_head')
                                <div class="de-field">
                                    <label>Order Type</label>
                                    <select name="order_type" class="{{ $errors->has('order_type') ? 'de-field-error' : '' }}">
                                        <option value="normal">Normal</option>
                                        <option value="vip">⭐ VIP</option>
                                    </select>
                                    @error('order_type')
                                        <span class="de-error-message">{{ $message }}</span>
                                    @enderror
                                </div>
                            @endif

                            <div class="de-field">
                                <label>Table Area <span class="de-required">*</span></label>
                                <select id="table_category" name="table_category"
                                    class="{{ $errors->has('table_category') ? 'de-field-error' : '' }}" required>
                                    <option value="">Select Area</option>
                                    @foreach ($tableCategories as $category)
                                        <option value="{{ $category->id }}" {{ old('table_category') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('table_category')
                                    <span class="de-error-message">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="de-field">
                                <label>Table Number <span class="de-required">*</span></label>
                                <select name="table_no" id="table_no"
                                    class="{{ $errors->has('table_no') ? 'de-field-error' : '' }}" required>
                                    <option value="">Select Table</option>
                                </select>
                                @error('table_no')
                                    <span class="de-error-message">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="de-field">
                                <label>Date of Birth</label>
                                <input type="date" name="birth_date" value="{{ old('birth_date') }}"
                                    class="{{ $errors->has('birth_date') ? 'de-field-error' : '' }}">
                                @error('birth_date')
                                    <span class="de-error-message">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="de-field">
                                <label>Anniversary Date</label>
                                <input type="date" name="anniversary_date" value="{{ old('anniversary_date') }}"
                                    class="{{ $errors->has('anniversary_date') ? 'de-field-error' : '' }}">
                                @error('anniversary_date')
                                    <span class="de-error-message">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <!-- Hidden fields for items -->
                        <div id="hiddenItemsContainer"></div>
                        <div style="display:none;">
                            <button type="submit" id="submitOrderBtn">Save</button>
                        </div>
                    </form>
                </div>

                <!-- Menu Section -->
                <div class="de-menu-section">
                    <div class="de-menu-header">
                        <h3>Popular Menu</h3>
                        <div class="de-menu-tools">
                            <div class="de-search-box">
                               <span class="de-search-icon">
                                    <i class="fas fa-search text-muted"></i>
                                </span>
                                <input type="text" id="menuSearch" placeholder="Search menu...">
                                <div id="searchResults" class="de-search-dropdown"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Category Buttons (Replaces Dropdown) -->
                    <div class="de-categories-wrapper">
                        <span class="de-cat-label">Categories:</span>
                        <button class="de-category-btn de-active-cat" data-cat="all">All</button>
                        @foreach ($categories as $category)
                            <button class="de-category-btn" data-cat="{{ $category->name }}">
                                {{ $category->name }}
                            </button>
                        @endforeach
                    </div>

                   <div class="de-menu-grid" id="menuItemsContainer">
                        @foreach ($menuItems as $item)
                            <div class="de-menu-item"
                                data-id="{{ $item->id }}"
                                data-name="{{ $item->name }}"
                                data-price="{{ $item->price }}"
                                data-currency="{{ $branch->country?->currency_symbol ?? '₹' }}">
                                <div class="de-item-img">
                                    @if ($item->image)
                                        <img src="{{ asset($item->image) }}" alt="{{ $item->name }}">
                                    @else
                                        🍽️
                                    @endif
                                </div>
                                <div class="de-item-name">{{ $item->name }}</div>
                                <div class="de-item-price">
                                    {{ $branch->country?->currency_symbol ?? '₹' }}{{ number_format($item->price, 2) }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- RIGHT: BASKET -->
            <div>
                <div class="de-basket">
                    <div class="de-basket-header">
                        <h4><i class="fas fa-utensils"></i> Your Order</h4>
                        <span class="de-basket-count" id="itemCountBadge">0 items</span>
                    </div>

                    <div class="de-basket-list" id="orderItemsList">
                        <div id="emptyCartMsg" class="de-basket-empty">
                            <span class="de-empty-icon">📋</span>
                            <p>No items selected</p>
                            <small>Click on menu items to add</small>
                        </div>
                        <div id="orderItemsDynamic"></div>
                    </div>

                    <div class="de-basket-footer">
                        <div class="de-total-row">
                            <span class="de-total-label">Subtotal</span>
                            <span class="de-total-amount" id="grandTotalAmount">₹0.00</span>
                        </div>
                        <button type="button" class="de-checkout-btn" id="placeOrderBtn">
                            🛒 Proceed to Checkout
                        </button>
                    </div>
                </div>

                <!-- Customer History -->
                <div id="customerHistory" class="de-history">
                    <div class="de-history-header">
                        <span style="font-size:1.2rem;">🔄</span>
                        <h5>Returning Customer</h5>
                    </div>
                    <div class="de-history-stats">
                        <div class="de-stat">
                            <span class="de-stat-label">Total Visits</span>
                            <span class="de-stat-value" id="visitCount">0</span>
                        </div>
                        <div class="de-stat">
                            <span class="de-stat-label">Last Visit</span>
                            <span class="de-stat-value" id="lastVisit" style="font-size:1rem;">-</span>
                        </div>
                    </div>
                    <table class="de-history-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Type</th>
                                <th class="de-amount">Amount</th>
                            </tr>
                        </thead>
                        <tbody id="historyRows"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // ============================
            // STATE
            // ============================
                const currencySymbol = @json($branch?->country?->currency_symbol ?? '₹');

            let order = {};
            const menuItemsData = @json($menuItems);

            // ============================
            // UTILITY
            // ============================
            function escapeHtml(str) {
                const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
                return String(str).replace(/[&<>"']/g, m => map[m]);
            }

            // function formatCurrency(amount) {
            //     return '₹' + parseFloat(amount).toFixed(2);
            // }
            function formatCurrency(amount) {
    return currencySymbol + parseFloat(amount).toFixed(2);
}

            // ============================
            // ORDER MANAGEMENT
            // ============================
            function addItemToOrder(id, name, price) {
                price = parseFloat(price);
                if (order[id]) {
                    order[id].qty++;
                } else {
                    order[id] = { id, name, price, qty: 1 };
                }
                renderOrderSummary();
                updateCardHighlights();
                updateHiddenFields();
            }

            function changeQty(id, delta) {
                if (order[id]) {
                    order[id].qty += delta;
                    if (order[id].qty <= 0) {
                        delete order[id];
                    }
                    renderOrderSummary();
                    updateCardHighlights();
                    updateHiddenFields();
                }
            }

            function setQty(id, value) {
                const qty = parseInt(value);
                if (isNaN(qty) || qty <= 0) {
                    delete order[id];
                } else {
                    order[id].qty = qty;
                }
                renderOrderSummary();
                updateCardHighlights();
                updateHiddenFields();
            }

            function removeItem(id) {
                delete order[id];
                renderOrderSummary();
                updateCardHighlights();
                updateHiddenFields();
            }

            // ============================
            // RENDER
            // ============================
            function renderOrderSummary() {
                const container = document.getElementById('orderItemsDynamic');
                const emptyMsg = document.getElementById('emptyCartMsg');
                const itemCountBadge = document.getElementById('itemCountBadge');

                const keys = Object.keys(order);
                const totalQty = keys.reduce((sum, id) => sum + order[id].qty, 0);
                itemCountBadge.textContent =
    `${totalQty.toLocaleString('en-IN')} item${totalQty !== 1 ? 's' : ''}`;

                if (keys.length === 0) {
                    emptyMsg.style.display = 'block';
                    container.innerHTML = '';
                    updateTotals();
                    return;
                }

                emptyMsg.style.display = 'none';

                let html = '';
                for (const id in order) {
                    const item = order[id];
                    html += `
                            <div class="de-basket-item">
                                <div class="de-item-details">
                                    <div class="de-item-title">${escapeHtml(item.name)}</div>
                                    <div class="de-item-sub">${formatCurrency(item.price)} each</div>
                                </div>
                                <div class="de-item-actions">
                                    <button class="de-qty-btn" onclick="changeQty(${id}, -1)">−</button>
                                    <input type="number" class="de-qty-input" value="${item.qty}" min="1" onchange="setQty(${id}, this.value)">
                                    <button class="de-qty-btn" onclick="changeQty(${id}, 1)">+</button>
                                    <span class="de-item-total">${currencySymbol}${(item.price * item.qty).toLocaleString('en-IN',{
    minimumFractionDigits:2,
    maximumFractionDigits:2
})}</span>
                                    <button class="de-remove-btn" onclick="removeItem(${id})" title="Remove">✕</button>
                                </div>
                            </div>
                        `;
                }
                container.innerHTML = html;
                updateTotals();
            }

           function updateTotals() {
            let total = 0;

            for (const id in order) {
                total += order[id].price * order[id].qty;
            }

            document.getElementById('grandTotalAmount').textContent =
                currencySymbol + total.toLocaleString('en-IN', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
            }

            function updateCardHighlights() {
                document.querySelectorAll('.de-menu-item').forEach(card => {
                    const id = card.dataset.id;
                    const qty = order[id]?.qty || 0;

                    card.style.borderColor = qty > 0 ? '#ff6b35' : '';
                    card.style.background = qty > 0 ? 'rgba(255, 107, 53, 0.03)' : '';

                    const oldBadge = card.querySelector('.de-item-badge');
                    if (oldBadge) oldBadge.remove();

                    if (qty > 0) {
                        const badge = document.createElement('div');
                        badge.className = 'de-item-badge';
                        badge.textContent = qty;
                        card.appendChild(badge);
                    }
                });
            }

            function updateHiddenFields() {
                const container = document.getElementById('hiddenItemsContainer');
                let html = '';
                for (const id in order) {
                    html += `
                            <input type="hidden" name="menu_item_id[]" value="${id}">
                            <input type="hidden" name="quantity[]" value="${order[id].qty}">
                        `;
                }
                container.innerHTML = html;
            }

            // ============================
            // EVENT: Menu Item Click
            // ============================
            document.addEventListener('click', function (e) {
                const card = e.target.closest('.de-menu-item');
                if (card) {
                    addItemToOrder(card.dataset.id, card.dataset.name, card.dataset.price);
                }
            });

            // ============================
            // EVENT: Category Buttons
            // ============================
            document.querySelectorAll('.de-category-btn').forEach(btn => {
                btn.addEventListener('click', function () {
                    document.querySelectorAll('.de-category-btn').forEach(b => b.classList.remove('de-active-cat'));
                    this.classList.add('de-active-cat');

                    const category = this.dataset.cat;
                    document.querySelectorAll('.de-menu-item').forEach(card => {
                        if (category === 'all') {
                            card.style.display = '';
                        } else {
                            card.style.display = card.dataset.category === category ? '' : 'none';
                        }
                    });
                });
            });

            // ============================
            // SEARCH
            // ============================
            const searchInput = document.getElementById('menuSearch');
            const searchResults = document.getElementById('searchResults');

            searchInput?.addEventListener('input', function () {
                const query = this.value.toLowerCase().trim();

                if (query.length === 0) {
                    searchResults.style.display = 'none';
                    document.querySelectorAll('.de-menu-item').forEach(card => card.style.display = '');
                    return;
                }

                const filtered = menuItemsData.filter(item =>
                    item.name.toLowerCase().includes(query)
                );

                document.querySelectorAll('.de-menu-item').forEach(card => card.style.display = 'none');

                if (filtered.length === 0) {
                    searchResults.innerHTML = `
                            <div class="de-result-item" style="justify-content:center;color:var(--de-text-light);">
                                No items found
                            </div>
                        `;
                    searchResults.style.display = 'block';
                    return;
                }

                let html = '';
                filtered.forEach(item => {
                    html += `
                            <div class="de-result-item"
                                 data-id="${item.id}"
                                 data-name="${escapeHtml(item.name)}"
                                 data-price="${item.price}">
                                <span>${escapeHtml(item.name)}</span>
                                <span class="de-result-price">${formatCurrency(item.price)}</span>
                            </div>
                        `;
                });
                searchResults.innerHTML = html;
                searchResults.style.display = 'block';

                document.querySelectorAll('.de-result-item').forEach(result => {
                    result.addEventListener('click', function () {
                        addItemToOrder(
                            this.dataset.id,
                            this.dataset.name,
                            this.dataset.price
                        );
                        searchInput.value = '';
                        searchResults.style.display = 'none';
                        document.querySelectorAll('.de-menu-item').forEach(card => card.style.display = '');
                    });
                });
            });

            document.addEventListener('click', function (e) {
                const wrapper = searchInput?.closest('.de-search-box');
                if (wrapper && !wrapper.contains(e.target)) {
                    if (searchResults) searchResults.style.display = 'none';
                }
            });

            // ============================
            // PLACE ORDER
            // ============================
            document.getElementById('placeOrderBtn')?.addEventListener('click', function () {
                if (Object.keys(order).length === 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Empty Order',
                        text: 'Please add at least one menu item!',
                        confirmButtonColor: '#ff6b35',
                        confirmButtonText: 'Got it'
                    });
                    return;
                }

                Swal.fire({
                    title: 'Confirm Order',
                    text: 'Are you sure you want to place this order?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#ff6b35',
                    cancelButtonColor: '#6b6b80',
                    confirmButtonText: 'Yes, Place Order',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById('orderForm').submit();
                    }
                });
            });

            // ============================
            // TABLES FETCH
            // ============================
            document.getElementById('table_category')?.addEventListener('change', function () {
                const categoryId = this.value;
                const tableNo = document.getElementById('table_no');

                if (!categoryId) {
                    tableNo.innerHTML = '<option value="">Select Table</option>';
                    return;
                }

                let url = "{{ auth()->user()->branch_id
                    ? route('branch.orders.tables', ['restaurant' => $restaurant->slug, 'branch' => auth()->user()->branch->slug, 'categoryId' => 'CATID'])
                    : route('restaurant.orders.tables', ['restaurant' => $restaurant->slug, 'categoryId' => 'CATID'])
                    }}";
                url = url.replace('CATID', categoryId);

                fetch(url)
                    .then(res => res.json())
                    .then(data => {
                        let options = '<option value="">Select Table</option>';
                        data.forEach(table => {
                            options += `
                                    <option value="${table.table_number}">
                                        Table ${table.table_number} ${table.occupied ? '🔴 Occupied' : '🟢 Available'}
                                    </option>
                                `;

                            // const disabled = table.occupied ? 'disabled' : '';

                            // options += `
                            //     <option value="${table.table_number}" ${disabled}>
                            //         Table ${table.table_number} ${table.occupied ? '🔴 Occupied' : '🟢 Available'}
                            //     </option>
                            // `;
                        });
                        tableNo.innerHTML = options;
                    })
                    .catch(() => {
                        tableNo.innerHTML = '<option value="">Error loading tables</option>';
                    });
                });

            // ============================
            // CUSTOMER HISTORY
            // ============================
            $('#mobile_number').on('blur', function () {
                const phone = $(this).val();
                if (!phone || phone.length < 10) return;

                $.get("{{ route('customer.history') }}", { phone: phone }, function (response) {
                    if (!response.found) {
                        $('#customerHistory').hide();
                        return;
                    }

                    $('#customerHistory').show();
                    $('#visitCount').text(response.total_visits);
                    $('#lastVisit').text(response.last_visit || '-');

                    let html = '';
                    response.orders.slice(0, 5).forEach(o => {
                        const date = new Date(o.created_at).toLocaleDateString('en-GB', {
                            day: '2-digit', month: 'short', year: 'numeric'
                        });
                        html += `
                                <tr>
                                    <td>${date}</td>
                                    <td><span style="background:var(--de-bg);padding:2px 14px;border-radius:50px;font-size:0.7rem;">${escapeHtml(o.order_type)}</span></td>
                                    <td class="de-amount">${formatCurrency(o.total)}</td>
                                </tr>
                            `;
                    });
                    $('#historyRows').html(html);
                });
            });

            // ============================
            // INIT
            // ============================
            renderOrderSummary();
            updateCardHighlights();
        </script>
    @endpush
@endsection
