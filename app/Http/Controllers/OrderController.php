<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Category;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Restaurant;
use App\Models\RestaurantTable;
use App\Models\TableAllocation;
use App\Models\TableCategory;
use App\Models\User;
use App\Notifications\NewOrderAssignedNotification;
use App\Notifications\OrderStatusNotification;
use App\Services\InventoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index()
    {
        $restaurant = app('restaurant');
        $user = Auth::user();
        $baseQuery = Order::query()
            ->where('restaurant_id', $restaurant->id);

        switch ($user->role) {

            case 'owner':
                // Owner -> All restaurant orders
                break;

            case 'branch_manager':
            case 'waiter_head':
                $baseQuery->where('branch_id', $user->branch_id);
                break;

            case 'chef':
                $baseQuery->where('branch_id', $user->branch_id);
                break;

            case 'cashier':
                $baseQuery->where('branch_id', $user->branch_id)
                    ->whereIn('status', [
                        'delivered',
                        'completed_waiting_cashier',
                    ]);
                break;

            case 'customer':
                $baseQuery->where('customer_id', $user->id);
                break;

            default:
                $baseQuery->where('branch_id', $user->branch_id);
                break;
        }

        $counts = [
            'all' => (clone $baseQuery)->count(),

            'today' => (clone $baseQuery)
                ->whereDate('created_at', today())
                ->count(),

            'walkin' => (clone $baseQuery)
                ->whereNull('customer_id')
                ->count(),

            'customer' => (clone $baseQuery)
                ->whereNotNull('customer_id')
                ->count(),

            'vip' => (clone $baseQuery)
                ->where('order_type', 'vip')
                ->count(),

            'normal' => (clone $baseQuery)
                ->where('order_type', 'normal')
                ->count(),

            'waiter' => (clone $baseQuery)
                ->whereHas('creator', function ($q) {
                    $q->where('role', 'waiter');
                })
                ->count(),

            'waiter_head' => (clone $baseQuery)
                ->whereHas('creator', function ($q) {
                    $q->where('role', 'waiter_head');
                })
                ->count(),

            'pending' => (clone $baseQuery)
                ->where('status', 'pending')
                ->count(),

            'preparing' => (clone $baseQuery)
                ->whereIn('status', ['preparing', 'prepared'])
                ->count(),

            'completed' => (clone $baseQuery)
                ->where('status', 'completed')
                ->count(),

            'delivered' => (clone $baseQuery)
                ->where('status', 'delivered')
                ->count(),
            'country' => (clone $baseQuery)
                ->whereHas('branch.country')
                ->count(),
        ];
        $query = Order::with([
            'branch',
            'items',
            'chef',
        ])->where('restaurant_id', $restaurant->id);

        switch ($user->role) {

            case 'owner':
                // Owner -> All restaurant orders
                break;

            case 'branch_manager':
            case 'waiter_head':
                $query->where('branch_id', $user->branch_id);
                break;

            case 'chef':
                $query->where('branch_id', $user->branch_id);
                break;

            case 'cashier':
                $query->where('branch_id', $user->branch_id)
                    ->whereIn('status', [
                        'delivered',
                        'completed_waiting_cashier',
                    ]);
                break;

            case 'customer':
                $query->where('customer_id', $user->id);
                break;

            default:
                $query->where('branch_id', $user->branch_id);
                break;
        }

        if (request('filter') == 'today') {

            $query->whereDate('created_at', today());

        } elseif (request('filter') == 'customer') {

            $query->whereNotNull('customer_id');

        } elseif (request('filter') == 'waiter') {

            $query->whereHas('creator', function ($q) {
                $q->where('role', 'waiter');
            });

        } elseif (request('filter') == 'waiter_head') {

            $query->whereHas('creator', function ($q) {
                $q->where('role', 'waiter_head');
            });

        } elseif (request('filter') == 'vip') {

            $query->where('order_type', 'vip');
        }

        if (request('from_date') && request('to_date')) {

            $query->whereBetween('created_at', [
                request('from_date').' 00:00:00',
                request('to_date').' 23:59:59',
            ]);

        } elseif (request('from_date')) {

            $query->whereDate('created_at', '>=', request('from_date'));

        } elseif (request('to_date')) {

            $query->whereDate('created_at', '<=', request('to_date'));
        }

        $orders = $query->latest()->get();

        return view('admin.orders.index', compact(
            'orders',
            'restaurant',
            'counts'
        ));
    }

    public function create()
    {
        $restaurant = app('restaurant');

        $user = Auth::user();
        if ($user->role === 'owner') {
            abort(403, 'Owners are not allowed to create orders.');
        }

        $branchId = $user->branch_id;

        $branch = $branchId
        ? Branch::with('country')->find($branchId)
        : null;

        $categories = Category::query()->where('restaurant_id', $restaurant->id)
            ->where('is_active', 1)
            ->orderBy('name')
            ->get();
        $menuItems = MenuItem::with(['branch', 'category'])
            ->where('restaurant_id', $restaurant->id)
            ->where('branch_id', $branch->id)
            ->where('is_active', 1)
            ->latest()
            ->get();

        $tableCategories = TableCategory::query()->where('restaurant_id', $restaurant->id)
            ->when($branchId, function ($q) use ($branchId) {
                $q->where('branch_id', $branchId);
            })
            ->get();

        return view(
            'admin.orders.create',
            compact(
                'restaurant',
                'branch',
                'categories',
                'tableCategories',
                'menuItems'
            )
        );
    }

    public function menuByCategory($restaurant, $categoryId)
    {
        return response()->json(
            MenuItem::select(
                'id',
                'name',
                'price'
            )
                ->where('category_id', $categoryId)
                ->where('is_active', 1)
                ->get()
        );
    }

    public function getTables($restaurant, $branch, $categoryId)
    {
        $restaurant = app('restaurant');
        $branchId = Auth::user()->branch_id;

        $tables = RestaurantTable::query()
            ->where('restaurant_id', $restaurant->id)
            ->where('branch_id', $branchId)
            ->where('cat_id', $categoryId)
            ->get()
            ->map(function ($table) use ($restaurant, $branchId) {

                $occupied = Order::query()
                    ->where('restaurant_id', $restaurant->id)
                    ->where('branch_id', $branchId)
                    ->where('table_no', $table->table_number)
                    ->latest()
                    ->first();

                $occupied = $occupied && in_array($occupied->status, [
                    'pending',
                    'preparing',
                    'prepared',
                    'delivered',
                ]);

                return [
                    'id' => $table->id,
                    'table_number' => $table->table_number,
                    'occupied' => $occupied,
                ];
            });

        return response()->json($tables);
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => 'required',
            'mobile_number' => 'required',
            'birth_date' => 'nullable',
            'anniversary_date' => 'nullable',
            'menu_item_id' => 'required|array',
            'email' => 'nullable',
            'table_category' => 'required',
            'table_no' => 'required',
            'order_type' => 'nullable|in:normal,vip',   // ← Added validation
        ]);

        $restaurant = app('restaurant');

        // Improved order_type logic
        $orderType = 'normal';

        if (in_array(Auth::user()->role, ['waiter_head', 'branch_manager', 'owner'])) {
            $orderType = $request->order_type === 'vip' ? 'vip' : 'normal';
        }

        $user = Auth::user();

        if ($user->role == 'owner') {
            $branchId = Branch::query()
                ->where('restaurant_id', $restaurant->id)
                ->value('id');
        } elseif ($user->branch_id) {
            $branchId = $user->branch_id;
        } else {
            $branchId = null;
        }

        DB::transaction(function () use ($request, $restaurant, $orderType, $branchId) {

            $chef = User::query()
                ->where('role', 'chef')
                ->where('restaurant_id', $restaurant->id)
                ->where('branch_id', $branchId)
                ->where('status', 'active')
                ->first();

            $token = $this->generateToken($orderType, $restaurant->id, $branchId);

            $branch = Branch::with('country')->find($branchId);
            $timezone = $branch?->country?->timezone ?? config('app.timezone');
            $orderDateTime = now($timezone);

            $order = Order::create([
                'restaurant_id' => $restaurant->id,
                'branch_id' => $branchId,

                'order_datetime' => $orderDateTime,
                'order_timezone' => $timezone,

                'chef_id' => $chef?->id,
                'created_by' => Auth::user()->role === 'customer' ? null : Auth::id(),

                'customer_id' => Auth::user()->role === 'customer' ? Auth::id() : null,
                'customer_name' => $request->customer_name,
                'mobile_number' => $request->mobile_number,
                'token_no' => $token,
                'table_no' => $request->table_no,
                'birth_date' => $request->birth_date,
                'anniversary_date' => $request->anniversary_date,
                'email' => $request->email,
                'order_type' => $orderType,
                'status' => 'pending',
                'subtotal' => 0,
                'tax' => 0,
                'total' => 0,
            ]);

            // Notifications
            if (Auth::user()->role === 'customer') {
                $waiterHead = User::query()
                    ->where('restaurant_id', $restaurant->id)
                    ->where('role', 'waiter_head')
                    ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
                    ->first();

                if ($waiterHead) {
                    $waiterHead->notify(new NewOrderAssignedNotification($order));
                }
            }

            if ($chef) {
                $chef->notify(new NewOrderAssignedNotification($order));
            }

            // Create Order Items
            $total = 0;
            foreach ($request->menu_item_id as $key => $menuId) {
                if (empty($menuId)) {
                    continue;
                }

                $menuItem = MenuItem::findOrFail($menuId);
                $qty = $request->quantity[$key] ?? 1;
                $subtotal = $menuItem->price * $qty;

                OrderItem::create([
                    'order_id' => $order->id,
                    'menu_item_id' => $menuItem->id,
                    'quantity' => $qty,
                    'price' => $menuItem->price,
                    'subtotal' => $subtotal,
                ]);

                $total += $subtotal;
            }

            $order->update([
                'subtotal' => $total,
                'tax' => 0,
                'total' => $total,
            ]);
        });

        // Redirect logic remains same...
        $user = Auth::user();

        if ($user->branch_id) {
            return redirect()
                ->route('branch.orders.index', [
                    'restaurant' => $restaurant->slug,
                    'branch' => $user->branch?->slug,
                ])
                ->with('success', 'Order created successfully.');
        }

        if ($user->restaurant_id) {
            return redirect()
                ->route('restaurant.orders.index', ['restaurant' => $restaurant->slug])
                ->with('success', 'Order created successfully.');
        }

        return redirect()->route('orders.index')->with('success', 'Order created successfully.');
    }

    private function generateToken(
        $type,
        $restaurantId,
        $branchId = null
    ) {
        $prefix = $type === 'vip'
            ? 'VIP-'
            : 'TOK-';

        $lastOrder = Order::query()
            ->where('restaurant_id', $restaurantId)
            ->where('branch_id', $branchId)
            ->where('order_type', $type)
            ->latest('id')
            ->first();

        $lastNumber = 0;

        if ($lastOrder) {

            $lastNumber = (int) preg_replace(
                '/[^0-9]/',
                '',
                $lastOrder->token_no
            );
        }

        return $prefix.str_pad(
            $lastNumber + 1,
            3,
            '0',
            STR_PAD_LEFT
        );
    }

    public function show($restaurant, $branch = null, $order = null)
    {

        if ($order === null && is_numeric($branch)) {
            $order = $branch;
            $branch = null;
        }

        $restaurant = app('restaurant');

        $order = Order::with([
            'branch',
            'items.menuItem',
            'creator',
        ])
            ->where('restaurant_id', $restaurant->id)
            ->findOrFail($order);

        return view(
            'admin.orders.show',
            compact(
                'restaurant',
                'order'
            )
        );
    }

    public function edit($restaurant, $branch, $order)
    {
        $restaurant = app('restaurant');

        $order = Order::with(
            'items.menuItem'
        )->findOrFail($order);

        $categories = Category::query()->where(
            'restaurant_id',
            $restaurant->id
        )
            ->where('is_active', 1)
            ->get();

        return view(
            'admin.orders.edit',
            compact(
                'restaurant',
                'order',
                'categories'
            )
        );
    }

    public function update(Request $request, $restaurant, $order)
    {
        $order = Order::findOrFail($order);
        $request->validate([
            'customer_name' => 'required',
            'mobile_number' => 'required',
            'table_no' => 'required',
            'menu_item_id' => 'required|array',
        ]);
        DB::transaction(function () use (
            $request,
            $order
        ) {
            $chef = User::query()->where('role', 'chef')
                ->where('branch_id', $order->branch_id)
                ->first();

            $newOrderType = $request->order_type
                ?? $order->order_type;

            $tokenNo = $order->token_no;

            if ($newOrderType != $order->order_type) {
                $tokenNo = $this->generateToken(
                    $newOrderType,
                    $order->restaurant_id,
                    $order->branch_id
                );
            }
            $branch = Branch::with('country')->find($order->branch_id);
            $timezone = $branch?->country?->timezone ?? config('app.timezone');
            $orderDateTime = now($timezone);
            $order->update([
                'customer_name' => $request->customer_name,
                'mobile_number' => $request->mobile_number,
                'table_no' => $request->table_no,
                'order_type' => $newOrderType,
                'token_no' => $tokenNo,
                'chef_id' => $chef?->id,
                'order_datetime' => $orderDateTime,
                'order_timezone' => $timezone,
            ]);

            OrderItem::query()->where(
                'order_id',
                $order->id
            )->delete();
            if ($chef) {
                $chef->notify(
                    new NewOrderAssignedNotification($order)
                );
            }
            $total = 0;

            foreach ($request->menu_item_id as $key => $menuId) {
                if (empty($menuId)) {
                    continue;
                }

                $menuItem = MenuItem::findOrFail($menuId);

                $qty = $request->quantity[$key] ?? 1;

                $subtotal = $menuItem->price * $qty;

                OrderItem::create([
                    'order_id' => $order->id,
                    'menu_item_id' => $menuId,
                    'quantity' => $qty,
                    'price' => $menuItem->price,
                    'subtotal' => $subtotal,
                ]);

                $total += $subtotal;
            }

            $order->update([
                'subtotal' => $total,
                'total' => $total,
            ]);
        });

        // Branch Manager
        $user = Auth::user();

        if ($user->role === 'super_admin') {

            return redirect()
                ->route('orders.index')
                ->with('success', 'Order updated successfully.');
        }

        if ($user->branch_id) {

            return redirect()
                ->route('branch.orders.index', [
                    'restaurant' => $user->restaurant?->slug,
                    'branch' => $user->branch?->slug,
                ])
                ->with('success', 'Order updated successfully.');
        }

        if ($user->restaurant_id) {

            return redirect()
                ->route('restaurant.orders.index', [
                    'restaurant' => $user->restaurant?->slug,
                ])
                ->with('success', 'Order updated successfully.');
        }
    }

    public function updateStatus(
        Request $request,
        Order $order
    ) {
        $order->update([
            'status' => $request->status,
        ]);

        return back();
    }

    public function updateKitchenStatus(
        Request $request,
        $restaurant,
        $order
    ) {
        $request->validate([
            'kitchen_status' => 'required',
        ]);

        $order = Order::findOrFail($order);

        $order->update([
            'kitchen_status' => $request->kitchen_status,
        ]);

        return back()->with(
            'success',
            'Kitchen Status Updated Successfully'
        );
    }

    public function checkOrders()
    {
        $chefId = Auth::id();

        $order = Order::query()->where('chef_id', $chefId)
            ->where('status', 'pending')
            ->where('notification_seen', 0)
            ->latest()
            ->first();

        if ($order) {

            $order->update([
                'notification_seen' => 1,
            ]);

            return response()->json([
                'has_new_order' => true,
            ]);
        }

        return response()->json([
            'has_new_order' => false,
        ]);
    }

    public function notifications()
    {
        return response()->json(
            Auth::user()->unreadNotifications()
                ->whereIn('data->type', ['order-notification', 'order-status-notification','low-stock'])
                ->latest()
                ->get()
        );
    }

    public function markPreparing($restaurant, $order)
    {
        $order = Order::findOrFail($order);

        // Security check
        if ($order->status !== 'pending' || $order->chef_id != Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Not authorized or invalid status',
            ], 403);
        }

        $order->load(['items', 'items.menuItem']);

        try {
            app(InventoryService::class)
                ->deductRecipeStock($order);

            $order->status = 'prepared';
            $order->save();

        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }

        // ====================== NEW: NOTIFY TABLE WAITER ======================
        $this->notifyTableWaiter($order);
        // =====================================================================

        // Notify order creator (existing)
        $creator = User::query()->find($order->created_by);
        if ($creator) {
            $creator->notify(
                new OrderStatusNotification($order, 'prepared')
            );
        }

        return back()->with(
            'success',
            'Order marked as Prepared successfully'
        );
    }

    public function markDelivered($restaurant, $order)
    {
        $order = Order::findOrFail($order);

        if ($order->status === 'prepared') {
            $order->status = 'delivered';
            $order->save();
        }

        return back()->with('success', 'Order Delivered');
    }

    public function markCompleted($restaurant, $order)
    {
        $order = Order::findOrFail($order);

        if ($order->status === 'delivered') {
            $order->status = 'completed';
            $order->save();
        }

        return back()->with('success', 'Order Mark As Completed');
    }

    public function statusOrders(Restaurant $restaurant, $status)
    {
        $user = Auth::user();

        $orders = Order::with(['branch', 'chef'])
            ->where('restaurant_id', $restaurant->id)
            ->where('status', $status);

        if (Auth::user()->branch_id) {
            $orders->where('branch_id', Auth::user()->branch_id);
        }
        $baseQuery = Order::query()
            ->where('restaurant_id', $restaurant->id);

        if ($user->branch_id) {
            $baseQuery->where('branch_id', $user->branch_id);
        }

        $counts = [
            'all' => (clone $baseQuery)->count(),

            'today' => (clone $baseQuery)
                ->whereDate('created_at', today())
                ->count(),

            'customer' => (clone $baseQuery)
                ->whereNotNull('customer_id')
                ->count(),

            'waiter' => (clone $baseQuery)
                ->whereHas('creator', function ($q) {
                    $q->where('role', 'waiter');
                })
                ->count(),

            'waiter_head' => (clone $baseQuery)
                ->whereHas('creator', function ($q) {
                    $q->where('role', 'waiter_head');
                })
                ->count(),

            'vip' => (clone $baseQuery)
                ->where('order_type', 'vip')
                ->count(),
        ];

        return view('admin.orders.index', [
            'orders' => $orders->latest()->get(),
            'restaurant' => $restaurant,
            'counts' => $counts,
        ]);
    }

    public function makePayment(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'payment_method' => 'required|in:cash,upi,card',
        ]);

        $order = Order::findOrFail($request->order_id);

        if (in_array($request->payment_method, ['cash', 'card'])) {

            $order->update([
                'payment_method' => $request->payment_method,
                'payment_status' => 'verified',
                'status' => 'completed',
            ]);

            return back()->with(
                'success',
                'Payment completed successfully.'
            );
        }

        // UPI Payment
        $order->update([
            'payment_method' => 'upi',
            'payment_status' => 'pending',
        ]);

        return back()->with(
            'success',
            'Payment submitted successfully. Waiting for cashier verification.'
        );
    }

    public function getTablesByCategory($restaurant, $categoryId)
    {
        $restaurant = app('restaurant');
        $branchId = Auth::user()->branch_id;
        $tables = RestaurantTable::query()->where('cat_id', $categoryId)
            ->where('restaurant_id', $restaurant->id)
            ->where('branch_id', $branchId)
            ->select('id', 'table_number')
            ->get();

        return response()->json($tables);
    }

    public function customerHistory(Request $request)
    {
        $restaurantId = Auth::user()->restaurant_id;
        $branchId = Auth::user()->branch_id;
        $phone = $request->phone;

        $orders = Order::query()
            ->where('mobile_number', $phone)
            ->where('restaurant_id', $restaurantId)
            ->where('branch_id', $branchId)
            ->latest()
            ->get();

        if ($orders->isEmpty()) {
            return response()->json([
                'found' => false,
            ]);
        }

        // Get the customer name from the first order
        $customerName = $orders->first()->customer_name ?? 'Customer';
        $lastOrder = $orders->first();

        return response()->json([
            'found' => true,
            'customer_name' => $customerName,
            'total_visits' => $orders->count(),
            'last_visit' => optional($lastOrder)->created_at?->format('d M Y'),
            'orders' => $orders->map(function ($order) {
                return [
                    'id' => $order->id,
                    'created_at' => $order->created_at,
                    'order_type' => $order->order_type ?? 'Normal',
                    'total' => $order->total ?? 0,
                ];
            }),
        ]);
    }

    public function myOrders()
    {
        $customer = Auth::user();

        $orders = Order::with([
            'items.menuItem',
        ])
            ->where('customer_id', $customer->id)
            ->where('status', 'completed')
            ->where('restaurant_id', $customer->restaurant_id)
            ->where('branch_id', $customer->branch_id)
            ->latest()
            ->get();

        return view(
            'customer.orders.index',
            compact('orders')
        );
    }

    public function verifyPayment(Order $order)
    {
        $order->update([
            'payment_status' => 'verified',
            'status' => 'completed',
        ]);

        return back()->with(
            'success',
            'Payment verified successfully.'
        );
    }

    public function generateBill($restaurant, $branch = null, $order = null)
    {
        if ($order == null && is_numeric($branch)) {
            $order = $branch;
            $branch = null;
        }
        $restaurant = app('restaurant');
        $order = Order::with([
            'branch', 'items.menuItems',
        ])
            ->where('restaurant_id', $restaurant->id)
            ->findOrFail($order);
        if (! $order->bill_generated_at) {
            $billNo = $this->generateBillNumber($order);
            $order->update([
                'bill_no' => $billNo,
                'bill_generated_at' => now(),
            ]);
            $order->refresh();
        }

        return view('admin.orders.bill', compact('order'));
    }

    private function generateBillNumber(Order $order)
    {
        $prefix = 'BILL-'.now()->format('Ymd').'-';

        $lastOrder = Order::query()
            ->whereNotNull('bill_no')
            ->latest('id')
            ->first();

        $lastNumber = 0;

        if ($lastOrder) {

            preg_match('/(\d+)$/', $lastOrder->bill_no, $matches);

            $lastNumber = $matches[1] ?? 0;

        }

        return $prefix.str_pad(
            $lastNumber + 1,
            4,
            '0',
            STR_PAD_LEFT
        );
    }

    /**
     * Notify the waiter assigned to the table when order is prepared
     */
    private function notifyTableWaiter(Order $order)
    {
        if (empty($order->table_no) || empty($order->branch_id)) {
            return;
        }

        $allocation = TableAllocation::with('waiter')
            ->where('restaurant_id', $order->restaurant_id)
            ->where('branch_id', $order->branch_id)
            ->whereHas('table', function ($q) use ($order) {
                $q->where('table_number', $order->table_no);
            })
            ->where('is_active', true)
            ->first();

        if ($allocation && $allocation->waiter) {
            $allocation->waiter->notify(
                new NewOrderAssignedNotification($order)
            );
        } else {
            // Fallback: Agar waiter na mila to Waiter Head ko bhej do
            $waiterHead = User::query()
                ->where('restaurant_id', $order->restaurant_id)
                ->where('role', 'waiter_head')
                ->where('branch_id', $order->branch_id)
                ->first();

            if ($waiterHead) {
                $waiterHead->notify(new NewOrderAssignedNotification($order));
            }
        }
    }
}
