<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DesignerDashboardController;
use App\Http\Controllers\RoomPackController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RestaurantController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\MenuItemController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\TableAreaController;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {

        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        // super admin
        if ($user->role === 'super_admin') {
            return view('admin.dashboard');
        }

        // branch manager → ALWAYS from DB (NOT SESSION)
        if ($user->role === 'branch_manager') {

            $branch = \App\Models\Branch::query()->find($user->branch_id);

            if (!$branch) {
                abort(403, 'Branch not assigned');
            }

            return redirect()->route('branch.dashboard', [
                'restaurant' => $branch->restaurant->slug,
                'branch' => $branch->slug,
            ]);
        }

        // restaurant user
        $restaurant = \App\Models\Restaurant::query()->find($user->restaurant_id);

        if (!$restaurant) {
            abort(403, 'Restaurant not assigned');
        }

        return redirect()->route('restaurant.dashboard', [
            'restaurant' => $restaurant->slug,
        ]);
    })->name('dashboard');
    Route::resource('restaurants', RestaurantController::class);
    Route::resource('room_packs', RoomPackController::class);
    Route::resource('users', UserController::class);
    Route::resource('branches', BranchController::class);

    Route::resource('roles', RoleController::class);

    Route::resource('permissions', PermissionController::class)->names([
        'index' => 'permissions.index',
        'create' => 'permissions.create',
        'store' => 'permissions.store',
        'edit' => 'permissions.edit',
        'update' => 'permissions.update',
        'destroy' => 'permissions.destroy',
    ]);

    Route::get('roles/{role}/permissions-data', [RoleController::class, 'getPermissionsData'])
        ->name('roles.permissions.data');

    Route::get('roles/{role}/permissions', [RoleController::class, 'managePermissions'])
        ->name('roles.permissions');

    Route::post('roles/{role}/permissions', [RoleController::class, 'updatePermissions'])
        ->name('roles.permissions.update');

    Route::get('/chef/check-orders', [OrderController::class, 'checkOrders'])
        ->name('chef.check-orders');
    Route::get('/chef/notifications', [OrderController::class, 'notifications'])->name('chef.notifications');

    Route::delete('/notifications/delete/{id}', function ($id) {

        $notification = Auth::user()
            ->notifications()
            ->find($id);

        if ($notification) {
            $notification->delete();
        }

        return response()->json([
            'success' => true
        ]);
    })->middleware('auth');
    Route::post('/notifications/read/{id}', function ($id) {

        $notification = Auth::user()
            ->notifications()
            ->find($id);

        if ($notification) {
            $notification->markAsRead();
        }

        return response()->json([
            'success' => true
        ]);
    })->name('notifications.read');
});
Route::post('{restaurant}/orders/{order}/prepare', [OrderController::class, 'markPreparing'])
    ->name('restaurant.orders.prepare');


Route::prefix('{restaurant}')->middleware('restaurant')->group(function () {
    Route::get('/', function () {
        $restaurant = app('restaurant');
        if (Auth::check()) {
            return redirect()->route('restaurant.login', ['restaurant' => $restaurant->slug]);
        }
        return redirect()->route('restaurant.dashboard', ['restaurant' => $restaurant->slug]);
    });
    Route::get('/login', [LoginController::class, 'showRestaurantLogin'])->name('restaurant.login');
    Route::post('/login', [LoginController::class, 'restaurantLogin'])->name('restaurant.login.submit');

    Route::get('/{branch}/login', [LoginController::class, 'showBranchLogin'])
        ->name('branch.login');

    Route::post('/{branch}/login', [LoginController::class, 'branchLogin'])
        ->name('branch.login.submit');
    Route::middleware(['auth'])->group(function () {
        Route::get('/dashboard', function () {
            $user = Auth::user();
            if ($user && $user->role == 'super_admin') {
            }
            return view('admin.dashboard');
        })->name('restaurant.dashboard');

        Route::prefix('{branch}')->group(function () {
            Route::get('/dashboard', function () {
                return view('admin.dashboard');
            })->name('branch.dashboard');

            Route::get('users', [UserController::class, 'index'])
                ->name('branch.users.index');

            Route::get('users/create', [UserController::class, 'create'])
                ->name('branch.users.create');
            Route::resource('categories', CategoryController::class)->names([
                'index'   => 'branch.categories.index',
                'create'  => 'branch.categories.create',
                'store'   => 'branch.categories.store',
                'show'    => 'branch.categories.show',
                'edit'    => 'branch.categories.edit',
                'update'  => 'branch.categories.update',
                'destroy' => 'branch.categories.destroy',
            ]);
        });

        /*
            |--------------------------------------------------------------------------
            | Restaurant Users
            |--------------------------------------------------------------------------
            */
        Route::resource('users', UserController::class)->names([
            'index'   => 'restaurant.users.index',
            'create'  => 'restaurant.users.create',
            'store'   => 'restaurant.users.store',
            'show'    => 'restaurant.users.show',
            'edit'    => 'restaurant.users.edit',
            'update'  => 'restaurant.users.update',
            'destroy' => 'restaurant.users.destroy',
        ]);
        Route::resource('branches', BranchController::class)->names([
            'index'   => 'restaurant.branches.index',
            'create'  => 'restaurant.branches.create',
            'store'   => 'restaurant.branches.store',
            'show'    => 'restaurant.branches.show',
            'edit'    => 'restaurant.branches.edit',
            'update'  => 'restaurant.branches.update',
            'destroy' => 'restaurant.branches.destroy',
        ]);
        Route::post('branches/{branch}/assign-manager', [BranchController::class, 'assignManager'])->name('restaurant.branches.assign-manager');
        Route::resource('categories', CategoryController::class)->names([
            'index'   => 'restaurant.categories.index',
            'create'  => 'restaurant.categories.create',
            'store'   => 'restaurant.categories.store',
            'show'    => 'restaurant.categories.show',
            'edit'    => 'restaurant.categories.edit',
            'update'  => 'restaurant.categories.update',
            'destroy' => 'restaurant.categories.destroy',
        ]);
        Route::resource('menu-items', MenuItemController::class)->names([
            'index'   => 'restaurant.menu-items.index',
            'create'  => 'restaurant.menu-items.create',
            'store'   => 'restaurant.menu-items.store',
            'show'    => 'restaurant.menu-items.show',
            'edit'    => 'restaurant.menu-items.edit',
            'update'  => 'restaurant.menu-items.update',
            'destroy' => 'restaurant.menu-items.destroy',
        ]);
        Route::get('categories-by-branch/{branch}', [MenuItemController::class, 'categoriesByBranch'])->name('restaurant.categories.by-branch');
        Route::resource('orders', OrderController::class)->names([
            'index'   => 'restaurant.orders.index',
            'create'  => 'restaurant.orders.create',
            'store'   => 'restaurant.orders.store',
            'show'    => 'restaurant.orders.show',
            'edit'    => 'restaurant.orders.edit',
            'update'  => 'restaurant.orders.update',
            'destroy' => 'restaurant.orders.destroy',
        ]);
        Route::resource('inventory', InventoryController::class)->names([
            'index' => 'restaurant.inventory.index',
            'create' => 'restaurant.inventory.create',
            'store' => 'restaurant.inventory.store',
            'show' => 'restaurant.inventory.show',
            'edit' => 'restaurant.inventory.edit',
            'update' => 'restaurant.inventory.update',
            'destroy' => 'restaurant.inventory.destroy',
        ]);
        Route::get('inventory/{inventory}/stock-in', [InventoryController::class, 'stockInForm'])->name('restaurant.inventory.stock-in');
        Route::post('inventory/{inventory}/stock-in', [InventoryController::class, 'stockInStore'])->name('restaurant.inventory.stock-in.store');
        // Inventory Stock OUT
        Route::get('inventory/{inventory}/stock-out', [InventoryController::class, 'stockOutForm'])->name('restaurant.inventory.stock-out');
        Route::post('inventory/{inventory}/stock-out', [InventoryController::class, 'stockOutStore'])->name('restaurant.inventory.stock-out.store');
        // Transaction History
        Route::get('inventory/{inventory}/transactions', [InventoryController::class, 'transactions'])->name('restaurant.inventory.transactions');
        Route::patch('orders/{order}/status', [OrderController::class, 'updateStatus'])->name('restaurant.orders.status');
        Route::get('menu-by-category/{categoryId}', [OrderController::class, 'menuByCategory'])->name('restaurant.orders.menu-by-category');
        Route::post('orders/{order}/kitchen-status', [OrderController::class, 'updateKitchenStatus'])->name('restaurant.orders.kitchen-status');
        Route::post('/logout', function () {
            Auth::logout();
            request()->session()->invalidate();
            request()->session()->regenerateToken();
            return redirect()->route(
                'restaurant.login',
                [
                    'restaurant' => request()->route('restaurant')
                ]
            );
        })->name('restaurant.logout');
        Route::resource('table-area', TableAreaController::class)->names([
            'index' => 'restaurant.table-area.index',
            'create' => 'restaurant.table-area.create',
            'store' => 'restaurant.table-area.store',
            'edit' => 'restaurant.table-area.edit',
            'update' => 'restaurant.table-area.update',
            'destroy' => 'restaurant.table-area.destroy',
        ]);
    });

    // Route::get('/{branch}/login', [LoginController::class, 'showBranchLogin'])->name('branch.login');
    // Route::post('/{branch}/login', [LoginController::class, 'branchLogin'])->name('branch.login.submit');
});
