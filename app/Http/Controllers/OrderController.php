<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Category;
use App\Models\MenuItem;
use App\Models\Restaurant;
use App\Models\Branch;
use App\Models\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
        public function index()
        {

            $restaurant = app('restaurant');
    // dd($restaurant);
            $query = Order::with([
                'branch',
                'items',
                'chef'
            ])->where(
                'restaurant_id',
                $restaurant->id
            );
            // dd($query);

            if (auth()->user()->role == 'owner') {
                // show all orders
            } elseif (auth()->user()->role == 'branch_manager') {
                $query->where(
                    'branch_id',
                    auth()->user()->branch_id
                );
            } elseif (auth()->user()->role == 'waiter_head') {
                $query->where(
                    'created_by',
                    auth()->id()
                );
            } elseif (auth()->user()->role == 'chef') {
                $query->where(
                    'chef_id',
                    auth()->id()
                );
            } else {
                $query->where(
                    'branch_id',
                    auth()->user()->branch_id
                );
            }

            $orders = $query
                ->latest()
                ->get();

            return view(
                'admin.orders.index',
                compact(
                    'orders',
                    'restaurant'
                )
            );
        }



    public function create()
    {
        $restaurant = app('restaurant');

        $categories = Category::where(
            'restaurant_id',
            $restaurant->id
        )
            ->where(
                'is_active',
                1
            )
            ->orderBy('name')
            ->get();

        return view(
            'admin.orders.create',
            compact(
                'restaurant',
                'categories'
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
    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => 'required',
            'mobile_number' => 'required',
            'menu_item_id'  => 'required|array'
        ]);

        $restaurant = app('restaurant');

        $orderType = auth()->user()->role == 'waiter_head'
            ? ($request->order_type ?? 'normal')
            : 'normal';

        if (auth()->user()->role == 'owner') {
            $branchId = Branch::where(
                'restaurant_id',
                $restaurant->id
            )->value('id');
        } elseif (auth()->user()->role == 'branch_manager') {
            $branchId = auth()->user()->managedBranch?->id;
        } else {
            $branchId = auth()->user()->branch_id;
        }

        DB::transaction(function () use (
            $request,
            $restaurant,
            $orderType,
            $branchId
        ) {

            $chef = User::where('role', 'chef')
                ->where('branch_id', $branchId)
                ->first();

            $token = $this->generateToken($orderType);

            $order = Order::create([
                'restaurant_id' => $restaurant->id,
                'branch_id'     => $branchId,
                'chef_id'       => $chef?->id,
                'created_by'    => auth()->id(),
                'customer_name' => $request->customer_name,
                'mobile_number' => $request->mobile_number,
                'token_no'      => $token,
                'order_type'    => $orderType,
                'status'        => 'pending',
                'subtotal'      => 0,
                'tax'           => 0,
                'total'         => 0,
            ]);

            $total = 0;

            foreach ($request->menu_item_id as $key => $menuId) {
                if (empty($menuId)) {
                    continue;
                }

                $menuItem = MenuItem::findOrFail($menuId);

                $qty = $request->quantity[$key] ?? 1;

                $subtotal = $menuItem->price * $qty;

                OrderItem::create([
                    'order_id'     => $order->id,
                    'menu_item_id' => $menuItem->id,
                    'quantity'     => $qty,
                    'price'        => $menuItem->price,
                    'subtotal'     => $subtotal,
                ]);

                $total += $subtotal;
            }

            $order->update([
                'subtotal' => $total,
                'tax'      => 0,
                'total'    => $total,
            ]);
        });

        return redirect()
            ->route(
                'restaurant.orders.index',
                $restaurant->slug
            )
            ->with(
                'success',
                'Order Created Successfully'
            );
    }

    // private function generateToken(
    //     $type
    // ) {
    //     $count =
    //         Order::count() + 1;

    //     return $type == 'vip'
    //         ? 'VIP-' .
    //         str_pad(
    //             $count,
    //             3,
    //             '0',
    //             STR_PAD_LEFT
    //         )
    //         : 'TOK-' .
    //         str_pad(
    //             $count,
    //             3,
    //             '0',
    //             STR_PAD_LEFT
    //         );
    // }
    private function generateToken($type)
    {
        $lastToken = Order::orderBy('id', 'desc')->value('token_no');

        $number = $lastToken
            ? (int) str_replace(['TOK-', 'VIP-'], '', $lastToken)
            : 0;

        $number++;

        $prefix = $type == 'vip' ? 'VIP-' : 'TOK-';

        return $prefix . str_pad($number, 3, '0', STR_PAD_LEFT);
    }

    public function show($restaurant, $order)
    {
        $restaurant = app('restaurant');


        $order = Order::with([
            'branch',
            'items.menuItem',
            'creator'
        ])->findOrFail($order);

        return view(
            'admin.orders.show',
            compact(
                'restaurant',
                'order'
            )
        );
    }


    public function edit($restaurant, $order)
    {
        $restaurant = app('restaurant');

        $order = Order::with(
            'items.menuItem'
        )->findOrFail($order);

        $categories = Category::where(
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
            'table_no'      => 'required',
            'menu_item_id'  => 'required|array'
        ]);
        DB::transaction(function () use (
            $request,
            $order
        ) {
            $chef = User::where('role', 'chef')
                ->where('branch_id', $order->branch_id)
                ->first();

            $newOrderType = $request->order_type
                ?? $order->order_type;

            $tokenNo = $order->token_no;

            if ($newOrderType != $order->order_type) {
                $tokenNo = $this->generateToken(
                    $newOrderType
                );
            }

            $order->update([
                'customer_name' => $request->customer_name,
                'mobile_number' => $request->mobile_number,
                'table_no'      => $request->table_no,
                'order_type'    => $newOrderType,
                'token_no'      => $tokenNo,
                'chef_id'       => $chef?->id,
            ]);

            OrderItem::where(
                'order_id',
                $order->id
            )->delete();

            $total = 0;

            foreach ($request->menu_item_id as $key => $menuId) {
                if (empty($menuId)) {
                    continue;
                }

                $menuItem = MenuItem::findOrFail($menuId);

                $qty = $request->quantity[$key] ?? 1;

                $subtotal = $menuItem->price * $qty;

                OrderItem::create([
                    'order_id'     => $order->id,
                    'menu_item_id' => $menuId,
                    'quantity'     => $qty,
                    'price'        => $menuItem->price,
                    'subtotal'     => $subtotal,
                ]);

                $total += $subtotal;
            }

            $order->update([
                'subtotal' => $total,
                'total'    => $total,
            ]);
        });

        return redirect()
            ->route(
                'restaurant.orders.index',
                $restaurant
            )
            ->with(
                'success',
                'Order Updated Successfully'
            );
    }
    public function destroy(
        $restaurant,
        $order
    ) {
        //
    }

    public function updateStatus(
        Request $request,
        Order $order
    ) {
        $order->update([
            'status' =>
            $request->status
        ]);

        return back();
    }
    public function updateKitchenStatus(
        Request $request,
        $restaurant,
        $order
    ) {
        $request->validate([
            'kitchen_status' => 'required'
        ]);

        $order = Order::findOrFail($order);

        $order->update([
            'kitchen_status' => $request->kitchen_status
        ]);

        return back()->with(
            'success',
            'Kitchen Status Updated Successfully'
        );
    }
}
