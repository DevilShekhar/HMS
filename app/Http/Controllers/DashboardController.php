<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\BranchSubscription;
use App\Models\InventoryItem;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\Restaurant;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        $restaurant = app()->bound('restaurant')
            ? app('restaurant')
            : null;
        // Restaurant wise branch filtering
        if ($restaurant) {
            if ($user->role === 'branch_manager') {
                $branchIds = [$user->branch_id];
            } else {
                $branchIds = Branch::query()->where('restaurant_id', $restaurant->id)
                    ->pluck('id');
            }
            $orders = Order::whereIn('branch_id', $branchIds);
            $totalOrders = Order::whereIn('branch_id', $branchIds);
        } else {
            // Super Admin - all data
            $branchIds = Branch::pluck('id');
            $orders = Order::whereIn('branch_id', $branchIds);
            $totalOrders = Order::query()->whereIn('branch_id', $branchIds);
        }
        // Super Admin Cards
        $totalRestaurants = Restaurant::count();
        $totalBranches = Branch::count();
        // If subscription relation exists
        $nearExpirySubscriptions = BranchSubscription::whereBetween('end_date', [
            now(),
            now()->addDays(30),
        ])->count();

        $revenue = [
            'today' => [
                'orders' => (clone $orders)
                    ->whereDate('created_at', today())
                    ->count(),
                'amount' => (clone $orders)
                    ->whereDate('created_at', today())
                    ->sum('total'),
            ],

            'yesterday' => [
                'orders' => (clone $orders)
                    ->whereDate('created_at', today()->subDay())
                    ->count(),
                'amount' => (clone $orders)
                    ->whereDate('created_at', today()->subDay())
                    ->sum('total'),
            ],

            'weekly' => [
                'orders' => (clone $orders)
                    ->whereBetween('created_at', [
                        now()->startOfWeek(),
                        now()->endOfWeek(),
                    ])
                    ->count(),
                'amount' => (clone $orders)
                    ->whereBetween('created_at', [
                        now()->startOfWeek(),
                        now()->endOfWeek(),
                    ])
                    ->sum('total'),
            ],

            'monthly' => [
                'orders' => (clone $orders)
                    ->whereMonth('created_at', now()->month)
                    ->count(),
                'amount' => (clone $orders)
                    ->whereMonth('created_at', now()->month)
                    ->sum('total'),
            ],

            'yearly' => [
                'orders' => (clone $orders)
                    ->whereYear('created_at', now()->year)
                    ->count(),

                'amount' => (clone $orders)
                    ->whereYear('created_at', now()->year)
                    ->sum('total'),
            ],

            'total' => [
                'orders' => (clone $totalOrders)->count(),

                'amount' => (clone $totalOrders)->sum('total'),
            ],

        ];

        if ($restaurant) {

            if ($user->role == 'branch_manager') {

                $inventoryStocks = InventoryItem::query()->where('branch_id', $user->branch_id)
                    ->select('name', 'remaining_stock', 'minimum_stock', 'unit')
                    ->orderByRaw('remaining_stock <= minimum_stock DESC')
                    ->orderBy('remaining_stock')
                    ->paginate(10);

            } else {

                $inventoryStocks = InventoryItem::with('branch')
                    ->where('restaurant_id', $restaurant->id)
                    ->select(
                        'branch_id',
                        'name',
                        'remaining_stock',
                        'minimum_stock',
                        'unit'
                    )
                    ->orderBy('branch_id')
                    ->orderByRaw('
                            CASE
                                WHEN remaining_stock <= minimum_stock THEN 0
                                ELSE 1
                            END
                        ')
                    ->orderBy('remaining_stock', 'asc')
                    ->paginate(10);

            }

        } else {

            $inventoryStocks = InventoryItem::with(['branch', 'restaurant'])
                ->select(
                    'restaurant_id',
                    'branch_id',
                    'name',
                    'remaining_stock',
                    'minimum_stock',
                    'unit'
                )
                ->orderByRaw('remaining_stock <= minimum_stock DESC')
                ->orderBy('restaurant_id')
                ->orderBy('branch_id')
                ->orderBy('remaining_stock')
                ->paginate(10);

        }

        $orderStatus = [
            'pending' => (clone $orders)
                ->where('status', 'pending')
                ->count(),

            'preparing' => (clone $orders)
                ->whereIn('status', ['preparing', 'prepared'])
                ->count(),

            'completed' => (clone $orders)
                ->where('status', 'completed')
                ->count(),
        ];
        $preparedOrders = (clone $orders)
            ->whereIn('status', ['prepared', 'pending'])
            ->orderByDesc('id')
            ->take(10)
            ->get();
        $pendingVerification = Order::query()->where('branch_id', $user->branch_id)
            ->where('status', 'delivered')
            ->where('payment_status', 'pending')
            ->count();
        $verifiedToday = Order::query()->where('branch_id', $user->branch_id)
            ->where('payment_status', 'verified')
            ->whereDate('updated_at', today())
            ->count();
        $query = Order::query()
            ->where('restaurant_id', $user->restaurant_id)
            ->where('payment_status', 'verified')
            ->whereDate('updated_at', today());

        if ($user->role == 'cashier') {
            $query->where('branch_id', $user->branch_id);
        }

        $todayCollection = $query->sum('total');
        $topRestaurants = Restaurant::query()
            ->select(
                'restaurants.id',
                'restaurants.name',
                DB::raw('COUNT(DISTINCT branches.id) as total_branches'),
                DB::raw('COUNT(orders.id) as total_orders'),
                DB::raw('COALESCE(SUM(orders.total),0) as total_revenue')
            )
            ->leftJoin('branches', 'branches.restaurant_id', '=', 'restaurants.id')
            ->leftJoin('orders', 'orders.branch_id', '=', 'branches.id')
            ->groupBy('restaurants.id', 'restaurants.name')
            ->orderByDesc('total_revenue')
            ->take(10)
            ->get();
        $subscriptions = BranchSubscription::with([
            'branch.restaurant',
            'plan',
        ])
            ->whereNotNull('branch_id')
            ->whereBetween('end_date', [
                now(),
                now()->addDays(30),
            ])
            ->orderBy('end_date')
            ->get();
        $inventoryAlerts = InventoryItem::with([
            'restaurant',
            'branch',
        ])
            ->whereColumn('remaining_stock', '<=', 'minimum_stock')
            ->orderBy('remaining_stock')
            ->take(15)
            ->get();

        return view('admin.dashboard', compact(
            'revenue',
            'totalRestaurants',
            'nearExpirySubscriptions',
            'totalBranches', 'inventoryStocks', 'orderStatus', 'preparedOrders', 'pendingVerification', 'verifiedToday', 'todayCollection', 'topRestaurants', 'subscriptions', 'inventoryAlerts'
        ));
    }

    public function dashboardInsights()
    {
        $restaurants = Restaurant::query()
            ->orderBy('name')
            ->get(['id', 'name', 'slug']);

        return view('admin.insights', compact('restaurants'));
    }

    public function getBranches(Restaurant $restaurant)
    {
        return $restaurant->branches()
            ->orderBy('name')
            ->get(['id', 'name']);
    }

    public function getInsights(Request $request, Restaurant $restaurant)
    {
        $branchId = $request->get('branch_id');
        $type = $request->get('type');

        $data = [];

        try {
            // MENU
            if (in_array($type, ['menu', null])) {
                $query = MenuItem::with('category')
                    ->where('restaurant_id', $restaurant->id);
                if ($branchId) {
                    $query->where('branch_id', $branchId);
                }

                $data['menu_items'] = $query->select('id', 'name', 'price', 'is_active', 'description')
                    ->latest()->take(25)->get();
            }

            // ORDERS
            if (in_array($type, ['orders', null])) {
                $query = Order::query()->where('restaurant_id', $restaurant->id);
                if ($branchId) {
                    $query->where('branch_id', $branchId);
                }

                $data['orders'] = $query->select('id', 'customer_name', 'total', 'status', 'created_at')
                    ->latest()->take(20)->get();
            }

            if (in_array($type, ['revenue', null])) {
                $revenueQuery = Order::query()->where('restaurant_id', $restaurant->id)
                    ->where('payment_status', 'verified');

                if ($branchId) {
                    $revenueQuery->where('branch_id', $branchId);
                }

                $today = now()->format('Y-m-d');

                $data['revenue'] = [
                    'today' => $revenueQuery->clone()
                        ->whereDate('created_at', $today)
                        ->sum('total'),

                    'this_month' => $revenueQuery->clone()
                        ->whereMonth('created_at', now()->month)
                        ->whereYear('created_at', now()->year)
                        ->sum('total'),

                    'total' => $revenueQuery->sum('total'),
                ];
            }
            // INVENTORY
            if (in_array($type, ['inventory', null])) {
                $query = InventoryItem::query()->where('restaurant_id', $restaurant->id);
                if ($branchId) {
                    $query->where('branch_id', $branchId);
                }

                $data['inventory'] = [
                    'total_items' => $query->count(),
                    'low_stock' => $query->clone()->whereRaw('remaining_stock <= minimum_stock')->count(),
                    'out_of_stock' => $query->clone()->where('remaining_stock', '<=', 0)->count(),
                    'in_stock' => $query->clone()->where('remaining_stock', '>', 0)->count(),
                    'items' => $query->select(
                        'name',
                        'total_stock',
                        'remaining_stock',
                        'minimum_stock',
                        'unit',
                        'is_active'
                    )->latest()->take(20)->get(),
                ];
            }

        } catch (\Exception $e) {
            Log::error('Insights Error: '.$e->getMessage());

            return response()->json(['error' => $e->getMessage()], 500);
        }

        return response()->json($data);
    }

    public function insightsPdf(Request $request)
    {
        $restaurantSlug = $request->restaurant_id ?? $request->get('restaurant_slug');

        if (! $restaurantSlug) {
            $restaurantSlug = session('current_restaurant_slug'); // fallback
        }

        $restaurant = Restaurant::query()->where('slug', $restaurantSlug)->firstOrFail();

        $branchId = $request->branch_id;
        $selectedTypes = $request->types
            ? explode(',', $request->types)
            : ['menu', 'orders', 'revenue', 'inventory'];

        $data = [];

        // MENU
        if (in_array('menu', $selectedTypes)) {
            $query = MenuItem::query()->where('restaurant_id', $restaurant->id);
            if ($branchId) {
                $query->where('branch_id', $branchId);
            }
            $data['menu'] = $query->select('name', 'price', 'is_active')
                ->latest()->take(30)->get();
        }

        // ORDERS
        if (in_array('orders', $selectedTypes)) {
            $query = Order::query()->where('restaurant_id', $restaurant->id);
            if ($branchId) {
                $query->where('branch_id', $branchId);
            }
            $data['orders'] = $query->select('id', 'customer_name', 'total', 'status', 'created_at')
                ->latest()->take(20)->get();
        }

        // REVENUE
        if (in_array('revenue', $selectedTypes)) {
            $query = Order::query()->where('restaurant_id', $restaurant->id)
                ->where('payment_status', 'verified');
            if ($branchId) {
                $query->where('branch_id', $branchId);
            }

            $data['revenue'] = [
                'today' => $query->clone()->whereDate('created_at', today())->sum('total'),
                'this_month' => $query->clone()->whereMonth('created_at', now()->month)->sum('total'),
                'total' => $query->sum('total'),
            ];
        }

        // INVENTORY
        if (in_array('inventory', $selectedTypes)) {
            $query = InventoryItem::query()->where('restaurant_id', $restaurant->id);
            if ($branchId) {
                $query->where('branch_id', $branchId);
            }

            $data['inventory'] = $query->select(
                'name', 'total_stock', 'remaining_stock', 'minimum_stock', 'unit', 'is_active'
            )->latest()->take(20)->get();
        }

        $pdf = Pdf::loadView('admin.insights_pdf', compact('data', 'restaurant', 'branchId'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('Insights_Report_'.now()->format('Y-m-d_His').'.pdf');
    }
}
