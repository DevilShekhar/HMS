<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\OrderItem;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    private function getBranches()
    {
        $user = Auth::user();
        $restaurant = app('restaurant');
        // branch manager
        if ($user->role == 'branch_manager') {
            return Branch::query()->where('id', $user->branch_id)
                ->get();
        }

        // owner/admin
        return Branch::query()->where(
            'restaurant_id',
            $restaurant->id
        )->get();
    }

    public function revenue(Request $request)
    {
        $user = Auth::user();
        $restaurant = app('restaurant');

        if ($user->role == 'owner') {
            $branches = Branch::query()->where('restaurant_id', $restaurant->id)->get();
        } else {
            $branches = Branch::query()->where('id', $user->branch_id)->get();
        }

        if ($request->branch_id) {
            $branchIds = [$request->branch_id];
        } else {
            $branchIds = $branches->pluck('id')->toArray();
        }

        $reports = $branches
            ->whereIn('id', $branchIds)
            ->map(function ($branch) {
                $orders = Order::query()->where('branch_id', $branch->id);

                return [
                    'branch_name' => $branch->name,

                    'today' => (clone $orders)
                        ->whereDate('created_at', Carbon::today())
                        ->sum('total'),

                    'yesterday' => (clone $orders)
                        ->whereDate('created_at', Carbon::yesterday())
                        ->sum('total'),

                    'weekly' => (clone $orders)
                        ->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
                        ->sum('total'),

                    'monthly' => (clone $orders)
                        ->whereMonth('created_at', Carbon::now()->month)
                        ->whereYear('created_at', Carbon::now()->year)
                        ->sum('total'),

                    'yearly' => (clone $orders)
                        ->whereYear('created_at', Carbon::now()->year)
                        ->sum('total'),

                    'total' => (clone $orders)
                        ->sum('total'),
                ];
            })->values();

        $currencySymbol = '₹'; // Default

        $user = Auth::user();

        if ($user->branch_id) {

            $branch = Branch::with('country')->find($user->branch_id);

            $currencySymbol = $branch?->country?->currency_symbol ?? '₹';

        } elseif ($request->filled('branch_id')) {

            $branch = Branch::with('country')->find($request->branch_id);

            $currencySymbol = $branch?->country?->currency_symbol ?? '₹';
        }

        return view('admin.reports.revenue', compact('reports', 'branches', 'currencySymbol'));
    }

    public function topSelling(Request $request)
    {
        $user = Auth::user();
        $restaurant = app('restaurant');

        if ($user->role == 'owner') {
            $branches = Branch::query()->where('restaurant_id', $restaurant->id)->get();
        } else {
            $branches = Branch::query()->where('id', $user->branch_id)->get();
        }

        $menuItems = MenuItem::query()->where('restaurant_id', $restaurant->id)->orderBy('name')->get();

        if ($request->branch_id) {
            $branchesToReport = $branches->where('id', $request->branch_id);
        } else {
            $branchesToReport = $branches;
        }

        $reports = [];

        // 4. Build reports with applied filter constraints
        foreach ($branchesToReport as $branch) {

            $query = OrderItem::whereHas('order', function ($q) use ($branch, $request) {
                $q->where('branch_id', $branch->id);

                // Date Filtering
                if ($request->from_date) {
                    $q->whereDate('created_at', '>=', Carbon::parse($request->from_date));
                }
                if ($request->to_date) {
                    $q->whereDate('created_at', '<=', Carbon::parse($request->to_date));
                }
            });

            // Menu Item Filtering
            if ($request->menu_item_id) {
                $query->where('menu_item_id', $request->menu_item_id);
            }

            $items = $query->select('menu_item_id')
                ->selectRaw('SUM(quantity) as total_quantity')
                ->groupBy('menu_item_id')
                ->orderByDesc('total_quantity')
                ->with('menuItem')
                ->limit(10)
                ->get();

            $reports[] = [
                'branch' => $branch->name,
                'items' => $items,
            ];
        }

        return view('admin.reports.top-selling-items', compact('reports', 'branches', 'menuItems'));
    }

    public function revenuePdf(Request $request)
    {
        $restaurant = app('restaurant');
        $user = Auth::user();

        $branches = Branch::query()->where('restaurant_id', $restaurant->id);

        if ($user->role === 'branch_manager') {
            $branches->where('id', $user->branch_id);
        } elseif ($request->filled('branch_id')) {
            $branches->where('id', $request->branch_id);
        }

        $branches = $branches->get();

        $reports = [];
        foreach ($branches as $branch) {
            $reports[] = [
                'branch_name' => $branch->name,
                'today' => Order::query()->where('branch_id', $branch->id)
                    ->whereDate('created_at', today())
                    ->sum('total'),
                'monthly' => Order::query()->where('branch_id', $branch->id)
                    ->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)
                    ->sum('total'),
                'yearly' => Order::query()->where('branch_id', $branch->id)
                    ->whereYear('created_at', now()->year)
                    ->sum('total'),
                'total' => Order::query()->where('branch_id', $branch->id)
                    ->sum('total'),
            ];
        }

        $pdf = Pdf::loadView('admin.reports.revenue_pdf', compact('reports'));

        return $pdf->download('Revenue_Report.pdf');
    }

    public function topSellingPdf(Request $request)
    {
        $user = Auth::user();
        $restaurant = app('restaurant');

        // Role-based branch access
        if ($user->role == 'owner') {
            $branches = Branch::query()->where('restaurant_id', $restaurant->id);
        } else {
            $branches = Branch::query()->where('id', $user->branch_id);
        }

        // Apply branch filter if provided
        if ($request->filled('branch_id')) {
            $branches->where('id', $request->branch_id);
        }

        $branches = $branches->get();

        $reports = [];

        foreach ($branches as $branch) {
            $query = OrderItem::whereHas('order', function ($q) use ($branch, $request) {
                $q->where('branch_id', $branch->id);

                if ($request->from_date) {
                    $q->whereDate('created_at', '>=', Carbon::parse($request->from_date));
                }
                if ($request->to_date) {
                    $q->whereDate('created_at', '<=', Carbon::parse($request->to_date));
                }
            });

            if ($request->menu_item_id) {
                $query->where('menu_item_id', $request->menu_item_id);
            }

            $items = $query->select('menu_item_id')
                ->selectRaw('SUM(quantity) as total_quantity')
                ->groupBy('menu_item_id')
                ->orderByDesc('total_quantity')
                ->with('menuItem')
                ->limit(10)
                ->get();

            $reports[] = [
                'branch' => $branch->name,
                'items' => $items,
            ];
        }

        $pdf = Pdf::loadView('admin.reports.top_selling_pdf', compact('reports', 'request'));

        return $pdf->download('Top_Selling_Items_Report.pdf');
    }
}
