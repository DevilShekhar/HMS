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


Route::get('/', function () {
    return view('welcome');
});

Auth::routes();
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DesignerDashboardController::class, 'index'])->name('dashboard');
    Route::resource('restaurants', RestaurantController::class);
    Route::resource('room_packs', RoomPackController::class);
    Route::resource('users', UserController::class);
    Route::resource('branches', BranchController::class);
});
Route::prefix('{restaurant}')->middleware('restaurant')->group(function () {
    Route::get('/', function () {
        $restaurant = app('restaurant');
        if (!auth()->check()) {
            return redirect()->route('restaurant.login', ['restaurant' => $restaurant->slug]);
        }
        return redirect()->route('restaurant.dashboard', ['restaurant' => $restaurant->slug]);
    });
    Route::get('/login', [LoginController::class, 'showRestaurantLogin'])->name('restaurant.login');
    Route::post('/login', [LoginController::class, 'restaurantLogin'])->name('restaurant.login.submit');
    Route::middleware(['auth'])->group(function () {
        Route::get('/dashboard', function () {
            $restaurant = app('restaurant');
            if (auth()->user()->role == 'super_admin') {
                // dd('welcom');
                // abort(403);
            }
            // return view('admin.restaurants.dashboard', compact('restaurant'));
            return view('admin.dashboard', compact('restaurant'));
        })->name('restaurant.dashboard');

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
        Route::resource('orders', OrderController::class)->names([
            'index'   => 'restaurant.orders.index',
            'create'  => 'restaurant.orders.create',
            'store'   => 'restaurant.orders.store',
            'show'    => 'restaurant.orders.show',
            'edit'    => 'restaurant.orders.edit',
            'update'  => 'restaurant.orders.update',
            'destroy' => 'restaurant.orders.destroy',
        ]);

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
    });
});
