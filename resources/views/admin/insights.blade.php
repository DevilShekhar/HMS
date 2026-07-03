@extends('layouts.app')

@section('title', 'Insights Platform')

@section('content')
    <div class="container-fluid py-4">


        <div class="dashboard-header">
            <div class="header-content">
                <div class="d-flex flex-wrap align-items-center justify-content-between">
                    <div>
                        <div class="badge-status">
                            <span class="dot"></span>
                            Live Analytics Engine
                        </div>
                        <h1>Metrics &amp; Analytics Dashboard</h1>
                        <p>Select metrics below to filter pipeline aggregates in real-time</p>
                    </div>
                    <div class="mt-3 mt-md-0">
                        <div class="badge-status">
                            Data Stream Active
                        </div>
                    </div>
                </div>

                <div class="header-stats">
                    <div class="stat-item">
                        <span>🔄</span>
                        <span>Last Updated: <strong id="lastUpdated">Just now</strong></span>
                    </div>
                </div>
            </div>
        </div>
        <!-- Button -->
        <button class="btn btn-success btn-sm rounded-pill px-3 m-2" id="downloadPdfBtn">
            <i class="fas fa-file-pdf me-1"></i> Download PDF
        </button>

        <div class="filter-card">
            <div class="row g-4">
                <div class="col-md-6">
                    <label class="form-label">
                        Enterprise Entity <span class="required-star">*</span>
                    </label>
                    <select class="form-select" id="restaurant_id">
                        <option value="">Select Restaurant Group</option>
                        @foreach($restaurants as $restaurant)
                            <option value="{{ $restaurant->slug }}">{{ $restaurant->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">
                        Subsidiary / Branch
                    </label>
                    <select class="form-select" id="branch_id" disabled>
                        <option value="">Awaiting Node Selection</option>
                    </select>
                </div>
            </div>

            <!-- Date Range Filters -->
            <div class="row g-4 mt-2">
                <div class="col-md-4">
                    <label class="form-label">
                        From Date
                    </label>
                    <input type="date" class="form-control" id="date_from"
                        value="{{ now()->subDays(30)->format('Y-m-d') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">
                        To Date
                    </label>
                    <input type="date" class="form-control" id="date_to" value="{{ now()->format('Y-m-d') }}">
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <div class="filter-actions">
                        <button class="btn-filter btn-filter-primary" id="applyDateFilter">
                            <span>🔍</span> Apply Filter
                        </button>
                        <button class="btn-filter btn-filter-secondary" id="resetDateFilter">
                            <span>↺</span> Reset
                        </button>
                    </div>
                </div>
            </div>

            <!-- Active Date Range Display -->
            <div class="row mt-3" id="dateRangeDisplay" style="display:none;">
                <div class="col-12">
                    <div class="d-flex align-items-center gap-2"
                        style="background: var(--primary-bg); padding: 0.5rem 1rem; border-radius: var(--radius); border: 1px solid var(--primary-light);">
                        <span style="font-size: 0.8rem; font-weight: 600; color: var(--primary);">📅 Active Date
                            Range:</span>
                        <span style="font-size: 0.85rem; font-weight: 500; color: var(--dark);" id="dateRangeText"></span>
                        <button class="btn-filter btn-filter-secondary"
                            style="padding: 0.2rem 0.75rem; font-size: 0.7rem; margin-left: auto;" id="clearDateRange">
                            ✕ Clear
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-4" id="insightsContainer" style="display:none;">

            <!-- Metric Selection Cards -->
            <div id="insightNav" style="display:none;">
                <div class="metric-grid">
                    <div class="metric-card" data-type="menu">
                        <div class="metric-icon blue">📋</div>
                        <div class="metric-info">
                            <div class="title">Menu Index</div>
                            <div class="desc">Catalogs &amp; listings validation</div>
                        </div>
                        <div class="checkmark">✓</div>
                    </div>

                    <div class="metric-card" data-type="orders">
                        <div class="metric-icon cyan">📦</div>
                        <div class="metric-info">
                            <div class="title">Order Logs</div>
                            <div class="desc">Active transactional pipeline</div>
                        </div>
                        <div class="checkmark">✓</div>
                    </div>

                    <div class="metric-card" data-type="revenue">
                        <div class="metric-icon green">💰</div>
                        <div class="metric-info">
                            <div class="title">Gross Revenue</div>
                            <div class="desc">Yield analysis reporting</div>
                        </div>
                        <div class="checkmark">✓</div>
                    </div>

                    <div class="metric-card" data-type="inventory">
                        <div class="metric-icon amber">📦</div>
                        <div class="metric-info">
                            <div class="title">Stock Matrix</div>
                            <div class="desc">Inventory limits monitoring</div>
                        </div>
                        <div class="checkmark">✓</div>
                    </div>
                </div>
            </div>

            <!-- Content Panel -->
            <div class="content-panel" id="insightContent">
                <div class="panel-body">
                    <div class="branch-prompt" id="branchPrompt">
                        <span class="prompt-icon">📍</span>
                        <h5>Select a Branch to Begin</h5>
                        <p>Please choose a restaurant group and subsidiary branch above to start exploring metrics.</p>
                    </div>
                    <div class="placeholder-empty" id="placeholder" style="display:none;">
                        <span class="empty-icon">📊</span>
                        <h5>Interactive Telemetry Builder</h5>
                        <p>Toggle checkmark nodes above to load multiple contextual tabular frames concurrently.</p>
                    </div>
                </div>
            </div>

        </div>

    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {

            let currentRestaurantSlug = null;
            let currentBranchId = null;
            let rawDataCache = {};
            const ITEMS_PER_PAGE = 5;
            let currentDateFrom = null;
            let currentDateTo = null;
            //             let currentRestaurantSlug = null;
            // let currentBranchId = null;

            // ==========================================
            // Set default dates
            // ==========================================
            function setDefaultDates() {
                const today = new Date();
                const thirtyDaysAgo = new Date();
                thirtyDaysAgo.setDate(today.getDate() - 30);

                $('#date_from').val(formatDate(thirtyDaysAgo));
                $('#date_to').val(formatDate(today));
            }

            function formatDate(date) {
                const d = new Date(date);
                const month = String(d.getMonth() + 1).padStart(2, '0');
                const day = String(d.getDate()).padStart(2, '0');
                const year = d.getFullYear();
                return `${year}-${month}-${day}`;
            }

            // ==========================================
            // Apply Date Filter
            // ==========================================
            $('#applyDateFilter').click(function () {
                const fromDate = $('#date_from').val();
                const toDate = $('#date_to').val();

                if (!fromDate || !toDate) {
                    alert('Please select both From and To dates.');
                    return;
                }

                if (fromDate > toDate) {
                    alert('From date cannot be greater than To date.');
                    return;
                }

                currentDateFrom = fromDate;
                currentDateTo = toDate;

                // Update date range display
                updateDateRangeDisplay(fromDate, toDate);

                // Refresh data if branch is selected
                if (currentBranchId) {
                    updateSelectedInsights();
                }
            });

            // ==========================================
            // Reset Date Filter
            // ==========================================
            $('#resetDateFilter').click(function () {
                setDefaultDates();
                currentDateFrom = null;
                currentDateTo = null;
                $('#dateRangeDisplay').hide();

                // Refresh data if branch is selected
                if (currentBranchId) {
                    updateSelectedInsights();
                }
            });

            // ==========================================
            // Clear Date Range
            // ==========================================
            $('#clearDateRange').click(function () {
                setDefaultDates();
                currentDateFrom = null;
                currentDateTo = null;
                $('#dateRangeDisplay').hide();

                // Refresh data if branch is selected
                if (currentBranchId) {
                    updateSelectedInsights();
                }
            });

            // ==========================================
            // Update Date Range Display
            // ==========================================
            function updateDateRangeDisplay(fromDate, toDate) {
                const from = new Date(fromDate);
                const to = new Date(toDate);
                const options = { year: 'numeric', month: 'short', day: 'numeric' };
                $('#dateRangeText').text(`${from.toLocaleDateString('en-US', options)} — ${to.toLocaleDateString('en-US', options)}`);
                $('#dateRangeDisplay').show();
            }

            // ==========================================
            // Restaurant Change
            // ==========================================
            $('#restaurant_id').change(function () {
                currentRestaurantSlug = $(this).val();

                if (!currentRestaurantSlug) {
                    $('#branch_id').html('<option value="">Awaiting Node Selection</option>').prop('disabled', true);
                    $('#insightsContainer').hide();
                    $('#insightNav').hide();
                    return;
                }

                $.ajax({
                    url: '/insights/restaurants/' + currentRestaurantSlug + '/branches',
                    method: 'GET',
                    success: function (branches) {
                        let html = '<option value="">All Subsidiary Nodes</option>';
                        $.each(branches, function (i, b) {
                            html += `<option value="${b.id}">${b.name}</option>`;
                        });
                        $('#branch_id').html(html).prop('disabled', false);
                        $('#insightsContainer').show();
                        $('#insightNav').hide();
                        $('#branchPrompt').show();
                        $('#placeholder').hide();
                        resetContentArea();
                        setDefaultDates();
                    }
                });
            });

            // ==========================================
            // Branch Change
            // ==========================================
            $('#branch_id').change(function () {
                currentBranchId = $(this).val();

                if (currentBranchId) {
                    $('#insightNav').show();
                    $('#branchPrompt').hide();
                    $('#placeholder').show();
                    $('.metric-card').removeClass('active');
                    updateSelectedInsights();
                    updateLastUpdated();
                } else {
                    $('#insightNav').hide();
                    $('#branchPrompt').show();
                    $('#placeholder').hide();
                    resetContentArea();
                }
            });

            // ==========================================
            // Metric Card Toggle
            // ==========================================
            $(document).on('click', '.metric-card', function () {
                if (!currentRestaurantSlug || !currentBranchId) {
                    $(this).addClass('active').removeClass('active');
                    return;
                }
                $(this).toggleClass('active');
                updateSelectedInsights();
            });

            // ==========================================
            // Reset Content Area
            // ==========================================
            function resetContentArea() {
                $('#insightContent .panel-body').html(`
                                <div class="branch-prompt" id="branchPrompt">
                                    <span class="prompt-icon">📍</span>
                                    <h5>Select a Branch to Begin</h5>
                                    <p>Please choose a restaurant group and subsidiary branch above to start exploring metrics.</p>
                                </div>
                                <div class="placeholder-empty" id="placeholder" style="display:none;">
                                    <span class="empty-icon">📊</span>
                                    <h5>Assemble Dashboard Elements</h5>
                                    <p>Select metrics above to organize data logs.</p>
                                </div>
                            `);
            }

            // ==========================================
            // Update Selected Insights with Date Filters
            // ==========================================
            function updateSelectedInsights() {
                let selectedTypes = [];
                $('.metric-card.active').each(function () {
                    selectedTypes.push($(this).data('type'));
                });

                if (selectedTypes.length === 0) {
                    $('#insightContent .panel-body').html(`
                                    <div class="placeholder-empty">
                                        <span class="empty-icon">📊</span>
                                        <h5>Interactive Telemetry Builder</h5>
                                        <p>Toggle checkmark nodes above to load multiple contextual tabular frames concurrently.</p>
                                    </div>
                                `);
                    return;
                }

                let containersHtml = '';
                selectedTypes.forEach(type => {
                    containersHtml += `<div id="block-${type}" class="insight-block">
                                    <div class="text-center py-4 text-muted">
                                        <div class="spinner"></div>
                                        Fetching data for <strong>${type}</strong>...
                                    </div>
                                </div>`;
                });
                $('#insightContent .panel-body').html(containersHtml);

                selectedTypes.forEach(type => {
                    // Build request data with date filters
                    let requestData = {
                        branch_id: currentBranchId,
                        type: type
                    };

                    // Add date filters if they exist
                    if (currentDateFrom) {
                        requestData.date_from = currentDateFrom;
                    }
                    if (currentDateTo) {
                        requestData.date_to = currentDateTo;
                    }

                    $.ajax({
                        url: `/insights/restaurants/${currentRestaurantSlug}/data`,
                        method: 'GET',
                        data: requestData,
                        success: function (res) {
                            console.log(`📊 ${type} data received:`, res);

                            // Store the raw response
                            rawDataCache[type] = res;

                            // For menu type, ensure we have the right structure
                            if (type === 'menu') {
                                if (Array.isArray(res)) {
                                    rawDataCache[type] = { menu_items: res };
                                } else if (res && !res.menu_items) {
                                    let found = false;
                                    for (let key in res) {
                                        if (Array.isArray(res[key]) && res[key].length > 0) {
                                            if (res[key][0] && (res[key][0].name || res[key][0].item_name)) {
                                                console.log(`✅ Found menu items in key: ${key}`);
                                                rawDataCache[type] = { menu_items: res[key] };
                                                found = true;
                                                break;
                                            }
                                        }
                                    }
                                    if (!found) {
                                        console.warn('⚠️ No menu items found in response');
                                        rawDataCache[type] = { menu_items: [] };
                                    }
                                }
                            }

                            renderPaginatedBlock(type, 1);
                        },
                        error: function (xhr, status, error) {
                            console.error(`❌ Error fetching ${type}:`, error);
                            console.error('Response:', xhr.responseText);
                            $(`#block-${type}`).html(`
                                            <div class="alert alert-danger" role="alert" style="border-radius:var(--radius);border:1px solid #fca5a5;background:#fef2f2;padding:1rem 1.25rem;">
                                                <strong>!</strong> Failed to fetch data for <strong>${type.toUpperCase()}</strong>
                                                <br><small>Error: ${error || 'Unknown error'}</small>
                                            </div>
                                        `);
                        }
                    });
                });
            }

            // ==========================================
            // Pagination Handler
            // ==========================================
            $(document).on('click', '.pagination .page-link', function (e) {
                e.preventDefault();
                let type = $(this).data('type');
                let targetPage = $(this).data('page');
                if (targetPage) {
                    renderPaginatedBlock(type, targetPage);
                }
            });

            // ==========================================
            // Render Paginated Block
            // ==========================================
            function renderPaginatedBlock(type, page) {
                let data = rawDataCache[type];
                let html = '';

                if (type === 'menu') {
                    console.log('🔄 Rendering menu with data:', data);

                    let items = [];
                    if (data && data.menu_items) {
                        items = data.menu_items;
                    } else if (data && Array.isArray(data)) {
                        items = data;
                    } else {
                        for (let key in data) {
                            if (Array.isArray(data[key])) {
                                items = data[key];
                                break;
                            }
                        }
                    }

                    console.log(`📋 Found ${items.length} menu items`);

                    let totalItems = items.length;
                    let totalPages = Math.ceil(totalItems / ITEMS_PER_PAGE) || 1;
                    let startIndex = (page - 1) * ITEMS_PER_PAGE;
                    let paginatedItems = items.slice(startIndex, startIndex + ITEMS_PER_PAGE);

                    html = `
                                    <div class="block-header">
                                        <span class="block-icon">🍽️</span>
                                        <span class="block-title">Menu Structure Records</span>
                                        <span class="block-badge">${totalItems} items</span>
                                    </div>
                                    <div class="table-wrapper">
                                        <table>
                                            <thead>
                                                <tr>
                                                    <th>Item Identity</th>
                                                    <th>Unit Standard Price</th>
                                                    <th>Category</th>
                                                    <th style="text-align:right;">Operational Node State</th>
                                                </tr>
                                            </thead>
                                            <tbody>`;

                    if (paginatedItems.length > 0) {
                        paginatedItems.forEach(item => {
                            let categoryName = 'N/A';
                            if (item.category) {
                                categoryName = item.category.name || 'N/A';
                            } else if (item.category_name) {
                                categoryName = item.category_name;
                            }

                            html += `
                                            <tr>
                                                <td><strong>${item.name || 'Unnamed Item'}</strong></td>
                                                <td class="price">₹${parseFloat(item.price || 0).toLocaleString('en-IN')}</td>
                                                <td>${categoryName}</td>
                                                <td style="text-align:right;">
                                                    <span class="status-badge ${item.is_active ? 'active' : 'inactive'}">
                                                        <span class="dot"></span>
                                                        ${item.is_active ? 'Active' : 'Inactive'}
                                                    </span>
                                                </td>
                                            </tr>`;
                        });
                    } else {
                        html += `<tr><td colspan="4" style="text-align:center;padding:2rem;color:var(--gray-500);">
                                        No menu records found.
                                    </td></tr>`;
                    }
                    html += `</tbody></table></div>`;

                    if (totalPages > 1) {
                        html += buildPagination(type, page, totalPages);
                    }
                }
                else if (type === 'orders') {
                    let orders = data.orders || [];
                    let totalItems = orders.length;
                    let totalPages = Math.ceil(totalItems / ITEMS_PER_PAGE) || 1;
                    let startIndex = (page - 1) * ITEMS_PER_PAGE;
                    let paginatedOrders = orders.slice(startIndex, startIndex + ITEMS_PER_PAGE);

                    html = `
                                    <div class="block-header">
                                        <span class="block-icon">📦</span>
                                        <span class="block-title">Live Order Execution Logs</span>
                                        <span class="block-badge">${totalItems} orders</span>
                                    </div>
                                    <div class="table-wrapper">
                                        <table>
                                            <thead>
                                                <tr>
                                                    <th>Workflow Reference</th>
                                                    <th>Customer Context</th>
                                                    <th>Gross Invoice Value</th>
                                                    <th>State Status</th>
                                                    <th style="text-align:right;">Date Clocked</th>
                                                </tr>
                                            </thead>
                                            <tbody>`;

                    if (paginatedOrders.length > 0) {
                        paginatedOrders.forEach(order => {
                            let badgeClass = 'processing';
                            if (order.status === 'completed') badgeClass = 'completed';
                            if (order.status === 'pending') badgeClass = 'pending';

                            html += `
                                            <tr>
                                                <td><span class="order-id">#${order.id}</span></td>
                                                <td>${order.customer_name || '<span style="color:var(--gray-400);">Anonymous</span>'}</td>
                                                <td class="price">₹${parseFloat(order.total).toLocaleString('en-IN')}</td>
                                                <td>
                                                    <span class="status-badge ${badgeClass}">
                                                        <span class="dot"></span>
                                                        ${order.status}
                                                    </span>
                                                </td>
                                                <td style="text-align:right;color:var(--gray-500);font-size:0.8rem;">${new Date(order.created_at).toLocaleDateString('en-IN')}</td>
                                            </tr>`;
                        });
                    } else {
                        html += `<tr><td colspan="5" style="text-align:center;padding:2rem;color:var(--gray-500);">No orders found for the selected date range.</td></tr>`;
                    }
                    html += `</tbody></table></div>`;

                    if (totalPages > 1) {
                        html += buildPagination(type, page, totalPages);
                    }
                }
                else if (type === 'revenue') {
                    html = `
                                    <div class="block-header">
                                        <span class="block-icon">💰</span>
                                        <span class="block-title">Asset Financial Balance Deck</span>
                                        ${currentDateFrom && currentDateTo ? `<span class="block-badge">${new Date(currentDateFrom).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })} - ${new Date(currentDateTo).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })}</span>` : ''}
                                    </div>
                                    <div class="revenue-grid">
                                        <div class="revenue-stat">
                                            <div class="stat-label">Todays Revenue</div>
                                            <div class="stat-value">₹${parseFloat(data.revenue.today || 0).toLocaleString('en-IN')}</div>
                                        </div>
                                        <div class="revenue-stat">
                                            <div class="stat-label">Monthly Revenue</div>
                                            <div class="stat-value">₹${parseFloat(data.revenue.this_month || 0).toLocaleString('en-IN')}</div>
                                        </div>
                                        <div class="revenue-stat primary">
                                            <div class="stat-label">LTV Cumulative Value</div>
                                            <div class="stat-value">₹${parseFloat(data.revenue.total || 0).toLocaleString('en-IN')}</div>
                                        </div>
                                    </div>
                                `;
                }
                else if (type === 'inventory') {
                    let items = data.inventory && data.inventory.items ? data.inventory.items : [];
                    let totalItems = items.length;
                    let totalPages = Math.ceil(totalItems / ITEMS_PER_PAGE) || 1;
                    let startIndex = (page - 1) * ITEMS_PER_PAGE;
                    let paginatedItems = items.slice(startIndex, startIndex + ITEMS_PER_PAGE);

                    let summary = data.inventory || {};
                    let totalCount = summary.total_items || 0;
                    let lowStockCount = summary.low_stock || 0;
                    let outOfStockCount = summary.out_of_stock || 0;
                    let inStockCount = summary.in_stock || 0;

                    html = `
                                    <div class="block-header">
                                        <span class="block-icon">📦</span>
                                        <span class="block-title">Supply-Chain Stock Balances</span>
                                        <span class="block-badge">${totalItems} items</span>
                                    </div>

                                    <div class="inventory-summary-grid">
                                        <div class="inventory-summary-card total">
                                            <div class="summary-number">${totalCount}</div>
                                            <div class="summary-label">Total Items</div>
                                        </div>
                                        <div class="inventory-summary-card in-stock">
                                            <div class="summary-number">${inStockCount}</div>
                                            <div class="summary-label">In Stock</div>
                                        </div>
                                        <div class="inventory-summary-card low-stock">
                                            <div class="summary-number">${lowStockCount}</div>
                                            <div class="summary-label">Low Stock</div>
                                        </div>
                                        <div class="inventory-summary-card out-of-stock">
                                            <div class="summary-number">${outOfStockCount}</div>
                                            <div class="summary-label">Out of Stock</div>
                                        </div>
                                    </div>

                                    <div class="table-wrapper">
                                        <table>
                                            <thead>
                                                <tr>
                                                    <th>Item Name</th>
                                                    <th style="text-align:center;">Total Stock</th>
                                                    <th style="text-align:center;">Remaining Stock</th>
                                                    <th style="text-align:center;">Minimum Stock</th>
                                                    <th style="text-align:center;">Unit</th>
                                                    <th style="text-align:center;">Status</th>
                                                    <th style="text-align:center;">Is Active</th>
                                                </tr>
                                            </thead>
                                            <tbody>`;

                    if (paginatedItems.length > 0) {
                        paginatedItems.forEach(item => {
                            let stockStatus = '';
                            let statusClass = '';
                            if (item.remaining_stock <= 0) {
                                stockStatus = 'Out of Stock';
                                statusClass = 'danger';
                            } else if (item.remaining_stock <= item.minimum_stock) {
                                stockStatus = 'Low Stock';
                                statusClass = 'warning';
                            } else {
                                stockStatus = 'In Stock';
                                statusClass = 'success';
                            }

                            html += `
                                            <tr>
                                                <td><strong>${item.name}</strong></td>
                                                <td style="text-align:center;font-weight:600;">${item.total_stock}</td>
                                                <td style="text-align:center;font-weight:600;">${item.remaining_stock}</td>
                                                <td style="text-align:center;font-weight:600;">${item.minimum_stock}</td>
                                                <td style="text-align:center;">${item.unit || 'N/A'}</td>
                                                <td style="text-align:center;">
                                                    <span class="status-badge ${statusClass}">
                                                        <span class="dot"></span>
                                                        ${stockStatus}
                                                    </span>
                                                </td>
                                                <td style="text-align:center;">
                                                    <span class="status-badge ${item.is_active ? 'active' : 'inactive'}">
                                                        <span class="dot"></span>
                                                        ${item.is_active ? 'Yes' : 'No'}
                                                    </span>
                                                </td>
                                            </tr>`;
                        });
                    } else {
                        html += `<tr><td colspan="7" style="text-align:center;padding:2rem;color:var(--gray-500);">No inventory items found.</td></tr>`;
                    }
                    html += `</tbody></table></div>`;

                    if (totalPages > 1) {
                        html += buildPagination(type, page, totalPages);
                    }
                }

                $(`#block-${type}`).html(html);
            }

            // ==========================================
            // Build Pagination
            // ==========================================
            function buildPagination(type, currentPage, totalPages) {
                let paginationHtml = `
                                <div class="pagination-wrapper">
                                    <div class="page-info">
                                        Showing Page <strong>${currentPage}</strong> of <strong>${totalPages}</strong>
                                    </div>
                                    <ul class="pagination">
                                        <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                                            <a class="page-link" href="#" data-type="${type}" data-page="${currentPage - 1}">Previous</a>
                                        </li>`;

                for (let i = 1; i <= totalPages; i++) {
                    paginationHtml += `
                                    <li class="page-item ${currentPage === i ? 'active' : ''}">
                                        <a class="page-link" href="#" data-type="${type}" data-page="${i}">${i}</a>
                                    </li>`;
                }

                paginationHtml += `
                                        <li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
                                            <a class="page-link" href="#" data-type="${type}" data-page="${currentPage + 1}">Next</a>
                                        </li>
                                    </ul>
                                </div>
                            `;

                return paginationHtml;
            }

            // ==========================================
            // Update Last Updated Time
            // ==========================================
            function updateLastUpdated() {
                const now = new Date();
                const timeStr = now.toLocaleTimeString('en-IN', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
                $('#lastUpdated').text(timeStr);
            }

            // Update time every 30 seconds
            setInterval(updateLastUpdated, 30000);

            // ==========================================
            // Initialize
            // ==========================================
            setDefaultDates();
            $('#downloadPdfBtn').click(function () {
                if (!currentRestaurantSlug) {
                    alert('Please select a restaurant first');
                    return;
                }

                const selectedTypes = [];
                $('.metric-card.active').each(function () {
                    selectedTypes.push($(this).data('type'));
                });

                if (selectedTypes.length === 0) {
                    alert('Please select at least one insight');
                    return;
                }

                const url = `{{ route('admin.insights.pdf') }}?types=${selectedTypes.join(',')}&branch_id=${currentBranchId || ''}&restaurant_id=${currentRestaurantSlug}`;

                window.location.href = url;
            });
        });



    </script>
@endpush
