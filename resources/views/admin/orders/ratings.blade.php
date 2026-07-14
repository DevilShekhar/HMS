@extends('layouts.app')

@section('content')
    <div class="feedback-wrap">
        <p class="fb-eyebrow">Front of house</p>
        <h1 class="fb-title"><i class="fas fa-star me-2"></i>Customer Feedback</h1>
        <p class="fb-sub">What people said after picking up their order &mdash; newest first.</p>

        <!-- Stats board -->
        <div class="fb-board">
            <div class="fb-stat">
                <div class="fb-num">{{ $ratings->total() }}</div>
                <div class="fb-lbl">Feedbacks</div>
            </div>
            <div class="fb-stat">
                <div class="fb-num ember">{{ number_format($ratings->avg('rating') ?? 0, 1) }}</div>
                <div class="fb-lbl">Avg rating</div>
            </div>
            <div class="fb-stat">
                <div class="fb-num sage">{{ $ratings->where('rating', '>=', 4)->count() }}</div>
                <div class="fb-lbl">4&ndash;5 stars</div>
            </div>
            <div class="fb-stat">
                <div class="fb-num rust">{{ $ratings->where('rating', '<=', 2)->count() }}</div>
                <div class="fb-lbl">1&ndash;2 stars</div>
            </div>
        </div>

        <div class="fb-rail-head">
            <h2>Recent Ratings</h2>
            @if($ratings->hasPages())
                <span>Showing {{ $ratings->firstItem() ?? 0 }} to {{ $ratings->lastItem() ?? 0 }} of
                    {{ $ratings->total() }}</span>
            @endif
        </div>

        <!-- Ticket list -->
        <div class="fb-list">
            @forelse($ratings as $rating)
                @php
                    $custName = $rating->customer?->name ?? $rating->order->customer_name;
                    $branch = $rating->order->branch->name ?? $rating->order->restaurant->name ?? 'N/A';

                    // Get currency symbol from branch country
                    $currencySymbol = $rating->order->branch?->country?->currency_symbol ??
                        $rating->order->restaurant?->country?->currency_symbol ??
                        '₹';

                    // Get order items using the 'items' relationship
                    $orderItems = $rating->order->items ?? collect();
                    $menuItems = [];
                    $totalAmount = 0;

                    foreach ($orderItems as $item) {
                        $itemName = $item->menuItem->name ?? $item->name ?? 'Item';
                        $itemPrice = $item->price ?? $item->menuItem->price ?? 0;
                        $itemQty = $item->quantity ?? 1;
                        $itemTotal = $itemPrice * $itemQty;
                        $totalAmount += $itemTotal;

                        $menuItems[] = [
                            'name' => $itemName,
                            'quantity' => $itemQty,
                            'price' => (float) $itemPrice,
                            'total' => (float) $itemTotal,
                            'currency' => $currencySymbol
                        ];
                    }

                    $itemsJson = json_encode($menuItems);
                @endphp
                <div class="fb-ticket"
                    onclick="openOrderDetails('{{ $rating->order->id }}', '{{ $rating->order->token_no }}', '{{ addslashes($custName) }}', '{{ $rating->rating }}', '{{ addslashes($rating->remark ?? '-') }}', '{{ $rating->created_at->format('M d, Y h:i A') }}', '{{ addslashes($branch) }}', '{{ $itemsJson }}', '{{ $currencySymbol }}')">
                    <div class="fb-stub">
                        <div class="fb-tok">#{{ $rating->order->token_no }}</div>
                        <div class="fb-tok-lbl">Order</div>
                    </div>
                    <div class="fb-body">
                        <div class="fb-row-top">
                            <div class="fb-who">
                                <div class="fb-avatar">{{ strtoupper(substr($custName, 0, 1)) }}</div>
                                <div>
                                    <div class="fb-name">{{ $custName }}</div>
                                    <div class="fb-branch">{{ $branch }}</div>
                                </div>
                            </div>
                            <div class="fb-stars">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= $rating->rating)
                                        <span class="on">★</span>
                                    @else
                                        <span class="off">☆</span>
                                    @endif
                                @endfor
                            </div>
                        </div>

                        @if($rating->remark)
                            <div class="fb-remark">{{ $rating->remark }}</div>
                        @else
                            <div class="fb-remark fb-remark-empty">No remark left</div>
                        @endif

                        <div class="fb-row-bottom">
                            @if($rating->rating >= 4)
                                <span class="fb-badge good"><i class="fas fa-circle-check"></i> Positive</span>
                            @elseif($rating->rating <= 2)
                                <span class="fb-badge bad"><i class="fas fa-circle-exclamation"></i> Needs attention</span>
                            @endif
                            <span class="fb-item-count">
                                <i class="fas fa-utensils me-1"></i>
                                {{ count($menuItems) }} items
                            </span>
                            <span class="fb-item-count">
                                <i class="fas fa-money-bill-wave me-1"></i>
                                {{ $currencySymbol }}{{ number_format($totalAmount, 2) }}
                            </span>
                        </div>
                    </div>
                    <button class="fb-view-btn"
                        onclick="event.stopPropagation(); openOrderDetails('{{ $rating->order->id }}', '{{ $rating->order->token_no }}', '{{ addslashes($custName) }}', '{{ $rating->rating }}', '{{ addslashes($rating->remark ?? '-') }}', '{{ $rating->created_at->format('M d, Y h:i A') }}', '{{ addslashes($branch) }}', '{{ $itemsJson }}', '{{ $currencySymbol }}')">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            @empty
                <div class="fb-empty">
                    <i class="fas fa-inbox"></i>
                    <p class="fb-empty-title">No feedback received yet</p>
                    <p>When customers rate their orders, their feedback will appear here</p>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($ratings->hasPages())
            <div class="fb-pagination">
                {{ $ratings->links() }}
            </div>
        @endif
    </div>

    <!-- Order details slide-in panel -->
    <div class="fb-panel-overlay" id="orderDetailsOverlay">
        <div class="fb-panel">
            <button type="button" class="fb-panel-close" onclick="closeOrderDetails()">
                <i class="fas fa-xmark"></i>
            </button>
            <h3 class="fb-panel-eyebrow"><i class="fas fa-receipt me-1"></i> Order</h3>
            <p class="fb-panel-tok" id="orderReference">&mdash;</p>

            <div class="fb-field">
                <div class="fb-field-k">Customer</div>
                <div class="fb-field-v" id="customerName">-</div>
            </div>
            <div class="fb-field">
                <div class="fb-field-k">Order </div>
                <div class="fb-field-v" id="orderNumber">-</div>
            </div>
            <div class="fb-field">
                <div class="fb-field-k">Branch</div>
                <div class="fb-field-v" id="branchName">-</div>
            </div>
            <div class="fb-field">
                <div class="fb-field-k">Submitted</div>
                <div class="fb-field-v" id="submittedAt">-</div>
            </div>
            <div class="fb-field">
                <div class="fb-field-k">Rating</div>
                <div class="fb-field-v" id="orderRating"></div>
            </div>
            <div class="fb-field">
                <div class="fb-field-k">Feedback</div>
                <div class="fb-quote" id="orderRemark">-</div>
            </div>

            <!-- Order Items Section -->
            <div class="fb-divider"></div>
            <h4 class="fb-panel-subtitle">
                <i class="fas fa-utensils me-2"></i>Order Items
            </h4>
            <div id="orderItemsContainer">
                <div class="fb-items-list">
                    <!-- Items will be rendered here by JavaScript -->
                </div>
            </div>
        </div>
    </div>

    <style>
        :root {
            --fb-paper: #FAF6EF;
            --fb-cloth: #F1EADC;
            --fb-ink: #241C15;
            --fb-ink-soft: #5C5346;
            --fb-ember: #D9661C;
            --fb-ember-dark: #A8480F;
            --fb-sage: #5C7A52;
            --fb-sage-bg: #E7EEE2;
            --fb-rust: #A83A22;
            --fb-rust-bg: #F5E4DE;
            --fb-line: #DDD1BC;
            --fb-card: #FFFFFF;
        }

        .feedback-wrap {
            max-width: 100%;
            color: var(--fb-ink);
        }

        .fb-eyebrow {
            font-family: 'IBM Plex Mono', monospace;
            font-size: 12px;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: var(--fb-ember-dark);
            margin: 0 0 6px;
        }

        .fb-title {
            font-family: 'Fraunces', serif;
            font-weight: 600;
            font-size: 28px;
            margin: 0 0 4px;
            color: var(--fb-ink);
        }

        .fb-sub {
            color: var(--fb-ink-soft);
            font-size: 14.5px;
            margin: 0 0 24px;
        }

        .fb-board {
            background: var(--fb-ink);
            border-radius: 14px;
            padding: 20px 24px;
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 18px;
            margin-bottom: 28px;
        }

        .fb-stat {
            border-left: 1px solid rgba(255, 255, 255, .14);
            padding-left: 18px;
        }

        .fb-stat:first-child {
            border-left: none;
            padding-left: 0;
        }

        .fb-num {
            font-family: 'Fraunces', serif;
            font-size: 26px;
            font-weight: 600;
            color: #FDF8EF;
            line-height: 1;
        }

        .fb-num.ember {
            color: #F0A468;
        }

        .fb-num.sage {
            color: #9CC28B;
        }

        .fb-num.rust {
            color: #E08D75;
        }

        .fb-lbl {
            font-family: 'IBM Plex Mono', monospace;
            font-size: 10.5px;
            letter-spacing: .06em;
            text-transform: uppercase;
            color: rgba(255, 255, 255, .55);
            margin-top: 4px;
        }

        .fb-rail-head {
            display: flex;
            justify-content: space-between;
            align-items: baseline;
            margin-bottom: 12px;
        }

        .fb-rail-head h2 {
            font-family: 'Fraunces', serif;
            font-size: 16px;
            font-weight: 600;
            margin: 0;
            color: var(--fb-ink);
        }

        .fb-rail-head span {
            font-size: 12.5px;
            color: var(--fb-ink-soft);
        }

        .fb-ticket {
            background: var(--fb-card);
            border: 1px solid var(--fb-line);
            border-radius: 10px;
            display: flex;
            align-items: stretch;
            margin-bottom: 12px;
            cursor: pointer;
            transition: border-color .15s ease, transform .15s ease;
            overflow: hidden;
        }

        .fb-ticket:hover {
            border-color: var(--fb-ember);
            transform: translateY(-1px);
        }

        .fb-stub {
            width: 96px;
            flex-shrink: 0;
            background: var(--fb-cloth);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 14px 8px;
            border-right: 1px dashed var(--fb-line);
            position: relative;
        }

        .fb-tok {
            font-family: 'IBM Plex Mono', monospace;
            font-size: 13px;
            font-weight: 600;
            color: var(--fb-ink);
        }

        .fb-tok-lbl {
            font-family: 'IBM Plex Mono', monospace;
            font-size: 9.5px;
            color: var(--fb-ink-soft);
            letter-spacing: .06em;
            text-transform: uppercase;
            margin-top: 2px;
        }

        .fb-stub::before,
        .fb-stub::after {
            content: '';
            position: absolute;
            width: 14px;
            height: 14px;
            background: var(--fb-paper);
            border-radius: 50%;
            right: -7px;
        }

        .fb-stub::before {
            top: -7px;
        }

        .fb-stub::after {
            bottom: -7px;
        }

        .fb-body {
            flex: 1;
            padding: 14px 18px;
            display: flex;
            flex-direction: column;
            gap: 6px;
            min-width: 0;
        }

        .fb-row-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
        }

        .fb-who {
            display: flex;
            align-items: center;
            gap: 10px;
            min-width: 0;
        }

        .fb-avatar {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            flex-shrink: 0;
            background: var(--fb-ember);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12.5px;
            font-weight: 600;
        }

        .fb-name {
            font-size: 14px;
            font-weight: 500;
            color: var(--fb-ink);
        }

        .fb-branch {
            font-size: 12px;
            color: var(--fb-ink-soft);
        }

        .fb-stars {
            font-size: 14px;
            letter-spacing: 1px;
            white-space: nowrap;
        }

        .fb-stars .on {
            color: var(--fb-ember);
        }

        .fb-stars .off {
            color: var(--fb-line);
        }

        .fb-remark {
            font-size: 13.5px;
            color: var(--fb-ink-soft);
            display: -webkit-box;
            -webkit-line-clamp: 1;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .fb-remark::before {
            content: '"';
            color: var(--fb-line);
        }

        .fb-remark::after {
            content: '"';
            color: var(--fb-line);
        }

        .fb-remark-empty {
            font-style: italic;
            color: #B3A794;
        }

        .fb-remark-empty::before,
        .fb-remark-empty::after {
            content: '';
        }

        .fb-row-bottom {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 2px;
        }

        .fb-item-count {
            font-size: 11px;
            color: var(--fb-ink-soft);
            font-family: 'IBM Plex Mono', monospace;
            letter-spacing: .04em;
        }

        .fb-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-family: 'IBM Plex Mono', monospace;
            font-size: 10.5px;
            letter-spacing: .04em;
            text-transform: uppercase;
            padding: 3px 9px;
            border-radius: 99px;
            width: fit-content;
        }

        .fb-badge.good {
            background: var(--fb-sage-bg);
            color: var(--fb-sage);
        }

        .fb-badge.bad {
            background: var(--fb-rust-bg);
            color: var(--fb-rust);
        }

        .fb-view-btn {
            flex-shrink: 0;
            align-self: center;
            margin-right: 14px;
            width: 34px;
            height: 34px;
            border-radius: 50%;
            border: 1px solid var(--fb-line);
            background: var(--fb-paper);
            color: var(--fb-ink-soft);
            font-size: 13px;
            cursor: pointer;
            transition: all .15s ease;
        }

        .fb-view-btn:hover {
            background: var(--fb-ember);
            color: #fff;
            border-color: var(--fb-ember);
        }

        .fb-empty {
            text-align: center;
            padding: 60px 20px;
            color: #B3A794;
        }

        .fb-empty i {
            font-size: 32px;
            opacity: .5;
            display: block;
            margin-bottom: 10px;
        }

        .fb-empty p {
            margin: 2px 0;
            font-size: 14px;
        }

        .fb-empty-title {
            font-weight: 600;
            color: var(--fb-ink-soft);
            font-size: 15px;
        }

        .fb-pagination {
            display: flex;
            justify-content: flex-end;
            padding: 12px 4px;
        }

        .fb-pagination .pagination .page-item.active .page-link {
            background: var(--fb-ember);
            border-color: var(--fb-ember);
            color: #fff;
        }

        .fb-pagination .pagination .page-link {
            color: var(--fb-ink);
            border: 1px solid var(--fb-line);
        }

        .fb-pagination .pagination .page-link:hover {
            background: var(--fb-cloth);
            border-color: var(--fb-ember);
            color: var(--fb-ember-dark);
        }

        /* ===== PANEL STYLES ===== */
        .fb-panel-overlay {
            position: fixed;
            inset: 0;
            background: rgba(36, 28, 21, .45);
            display: none;
            align-items: stretch;
            justify-content: flex-end;
            z-index: 1050;
        }

        .fb-panel-overlay.show {
            display: flex;
        }

        .fb-panel {
            width: 420px;
            max-width: 92vw;
            background: var(--fb-paper);
            height: 100%;
            padding: 26px 24px;
            overflow-y: auto;
            box-shadow: -8px 0 30px rgba(0, 0, 0, .12);
            animation: fbSlideIn .25s ease;
        }

        @keyframes fbSlideIn {
            from {
                transform: translateX(30px);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        .fb-panel-close {
            background: none;
            border: 1px solid var(--fb-line);
            border-radius: 99px;
            width: 30px;
            height: 30px;
            color: var(--fb-ink-soft);
            cursor: pointer;
            float: right;
            transition: all .15s ease;
        }

        .fb-panel-close:hover {
            background: var(--fb-ember);
            color: #fff;
            border-color: var(--fb-ember);
        }

        .fb-panel-eyebrow {
            font-family: 'IBM Plex Mono', monospace;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: .06em;
            color: var(--fb-ember-dark);
            margin: 2px 0 2px;
        }

        .fb-panel-tok {
            font-family: 'Fraunces', serif;
            font-size: 22px;
            font-weight: 600;
            margin: 0 0 22px;
            color: var(--fb-ink);
        }

        .fb-field {
            margin-bottom: 16px;
        }

        .fb-field-k {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .05em;
            color: var(--fb-ink-soft);
            font-family: 'IBM Plex Mono', monospace;
            margin-bottom: 3px;
        }

        .fb-field-v {
            font-size: 14.5px;
            color: var(--fb-ink);
        }

        .fb-quote {
            background: var(--fb-card);
            border: 1px dashed var(--fb-line);
            border-radius: 8px;
            padding: 12px 14px;
            font-size: 13.5px;
            color: var(--fb-ink-soft);
            font-style: italic;
            margin-top: 4px;
        }

        .fb-divider {
            height: 1px;
            background: var(--fb-line);
            margin: 20px 0 16px;
            opacity: .4;
        }

        .fb-panel-subtitle {
            font-family: 'Fraunces', serif;
            font-size: 15px;
            font-weight: 600;
            color: var(--fb-ink);
            margin: 0 0 12px;
        }

        .fb-items-list {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .fb-item-row {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 8px 12px;
            background: var(--fb-card);
            border-radius: 8px;
            border: 1px solid var(--fb-line);
            transition: all .15s ease;
        }

        .fb-item-row:hover {
            border-color: var(--fb-ember);
        }

        .fb-item-qty {
            font-family: 'IBM Plex Mono', monospace;
            font-size: 12px;
            font-weight: 600;
            color: var(--fb-ember);
            background: var(--fb-cloth);
            padding: 2px 8px;
            border-radius: 4px;
            min-width: 28px;
            text-align: center;
        }

        .fb-item-name {
            flex: 1;
            font-size: 13.5px;
            color: var(--fb-ink);
        }

        .fb-item-price {
            font-family: 'IBM Plex Mono', monospace;
            font-size: 12.5px;
            color: var(--fb-ink-soft);
            white-space: nowrap;
        }

        .fb-items-empty {
            text-align: center;
            padding: 20px;
            color: #B3A794;
            font-size: 13px;
        }

        .fb-items-empty i {
            display: block;
            font-size: 24px;
            margin-bottom: 6px;
            opacity: .4;
        }

        .fb-item-total {
            border-top: 1px solid var(--fb-line);
            padding-top: 12px;
            margin-top: 8px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .fb-item-total-label {
            font-family: 'IBM Plex Mono', monospace;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .06em;
            color: var(--fb-ink-soft);
        }

        .fb-item-total-amount {
            font-family: 'Fraunces', serif;
            font-size: 20px;
            font-weight: 600;
            color: var(--fb-ember);
        }

        @media (max-width: 600px) {
            .fb-board {
                grid-template-columns: repeat(2, 1fr);
            }

            .fb-stub {
                width: 76px;
            }

            .fb-row-top {
                flex-direction: column;
                align-items: flex-start;
                gap: 6px;
            }

            .fb-view-btn {
                display: none;
            }

            .fb-panel {
                width: 100%;
                max-width: 100%;
                padding: 20px 16px;
            }

            .fb-item-row {
                flex-wrap: wrap;
                gap: 6px;
            }

            .fb-item-name {
                width: 100%;
            }
        }
    </style>

    <script>
        function starsHtml(rating) {
            let html = '';
            for (let i = 1; i <= 5; i++) {
                html += i <= rating ? '<span style="color:#D9661C;">★</span>' : '<span style="color:#DDD1BC;">☆</span>';
            }
            return html;
        }

        function renderOrderItems(items, currencySymbol) {
            const container = document.querySelector('#orderItemsContainer .fb-items-list');
            if (!container) return;

            if (!items || items.length === 0) {
                container.innerHTML = `
                    <div class="fb-items-empty">
                        <i class="fas fa-box-open"></i>
                        No items in this order
                    </div>
                `;
                return;
            }

            let total = 0;
            let html = '';
            const symbol = currencySymbol || '₹'; // Default to ₹ if no currency provided

            items.forEach(item => {
                const itemTotal = (item.price || 0) * (item.quantity || 1);
                total += itemTotal;
                html += `
                    <div class="fb-item-row">
                        <span class="fb-item-qty">${item.quantity || 1}×</span>
                        <span class="fb-item-name">${item.name || 'Item'}</span>
                        <span class="fb-item-price">${symbol}${(item.price || 0).toFixed(2)}</span>
                    </div>
                `;
            });

            html += `
                <div class="fb-item-total">
                    <span class="fb-item-total-label">Total</span>
                    <span class="fb-item-total-amount">${symbol}${total.toFixed(2)}</span>
                </div>
            `;

            container.innerHTML = html;
        }

        function openOrderDetails(orderId, tokenNo, customerName, rating, remark, date, branch, itemsJson, currencySymbol) {
            // Parse items
            let items = [];
            try {
                items = JSON.parse(itemsJson || '[]');
            } catch (e) {
                items = [];
            }

            document.getElementById('orderReference').textContent = '#' + tokenNo;
            document.getElementById('customerName').textContent = customerName || '-';
            document.getElementById('orderNumber').textContent = '#' + (tokenNo || '-');
            document.getElementById('branchName').textContent = branch || 'N/A';
            document.getElementById('submittedAt').textContent = date || '-';

            const ratingContainer = document.getElementById('orderRating');
            ratingContainer.innerHTML = starsHtml(rating) + ' <span style="font-size:12px;color:#5C5346;">(' + rating + '/5)</span>';

            const remarkEl = document.getElementById('orderRemark');
            if (remark && remark !== '-') {
                remarkEl.innerHTML = '"' + remark + '"';
            } else {
                remarkEl.innerHTML = '<em style="color:#B3A794;">No remark provided</em>';
            }

            // Render order items with currency
            renderOrderItems(items, currencySymbol || '₹');

            document.getElementById('orderDetailsOverlay').classList.add('show');
            document.body.style.overflow = 'hidden';
        }

        function closeOrderDetails() {
            document.getElementById('orderDetailsOverlay').classList.remove('show');
            document.body.style.overflow = '';
        }

        // Close panel on overlay click
        document.getElementById('orderDetailsOverlay').addEventListener('click', function (e) {
            if (e.target.id === 'orderDetailsOverlay') closeOrderDetails();
        });

        // Close panel on ESC key
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') closeOrderDetails();
        });
    </script>
@endsection
