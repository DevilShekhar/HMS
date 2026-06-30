<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\BranchSubscription;
use App\Models\InventoryItem;
use App\Models\Order;
use App\Models\Restaurant;
use Illuminate\Support\Facades\Auth;

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

        return view('admin.dashboard', compact(
            'revenue',
            'totalRestaurants',
            'nearExpirySubscriptions',
            'totalBranches', 'inventoryStocks', 'orderStatus', 'preparedOrders'
        ));
    }
}
