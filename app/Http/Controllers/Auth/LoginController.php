<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Restaurant;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /*
    |--------------------------------------------------------------------------
    | Main Admin Login
    |--------------------------------------------------------------------------
    */

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $credentials['role'] = 'super_admin';

        if (Auth::attempt($credentials)) {

            $request->session()->regenerate();

            return redirect('/dashboard');
        }

        return back()->withErrors([
            'email' => 'Please login using your restaurant URL. Example: /jalpari/login',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | After Login Redirect
    |--------------------------------------------------------------------------
    */

    protected function authenticated(Request $request, $user)
    {
        if ($user->role == 'super_admin') {

            return redirect('/dashboard');
        }

        if (session()->has('restaurant_slug')) {

            return redirect(
                '/' . session('restaurant_slug') . '/dashboard'
            );
        }

        return redirect('/');
    }

    /*
    |--------------------------------------------------------------------------
    | Restaurant Login Form
    |--------------------------------------------------------------------------
    */

    public function showRestaurantLogin($restaurant)
    {
        session([
            'restaurant_slug' => $restaurant
        ]);

        return view('auth.login');
    }

    public function showBranchLogin(
        Restaurant $restaurant,
        Branch $branch
    ) {

        if ($branch->restaurant_id != $restaurant->id) {
            abort(404);
        }

        return view('auth.login', compact('restaurant', 'branch'));
    }

    public function branchLogin(Request $request, $restaurant, $branch)
    {
        $restaurantModel = Restaurant::query()->where('slug', $restaurant)->firstOrFail();

        $branchModel = Branch::query()->where('slug', $branch)
            ->where('restaurant_id', $restaurantModel->id)
            ->firstOrFail();
        $user = User::query()->where('email', $request->email)
            ->where('id', $branchModel->branch_manager_id)
            ->first();

        if (!$user) {
            return back()->withErrors([
                'email' => 'You are not assigned to this branch.'
            ]);
        }

        if (Auth::attempt([
            'email' => $request->email,
            'password' => $request->password,
            'status' => 'Active'
        ])) {

            $request->session()->regenerate();

            return redirect()->to(
                $restaurantModel->slug . '/' . $branchModel->slug . '/dashboard'
            );
        }

        return back()->withErrors([
            'email' => 'Invalid credentials.'
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Restaurant Login Submit
    |--------------------------------------------------------------------------
    */

    public function restaurantLogin(Request $request, $restaurant)
    {
        $restaurantModel = Restaurant::query()->where('slug', $restaurant)->first();

        if (!$restaurantModel) {
            abort(404);
        }

        $user = User::query()->where('email', $request->email)
            ->where('restaurant_id', $restaurantModel->id)
            ->first();

        if (!$user) {
            return back()->withErrors([
                'email' => 'You are not allowed to login to this restaurant.'
            ]);
        }
        if ($user->role === 'branch_manager') {
            return back()->withErrors([
                'email' => 'Branch managers must login from their branch login URL.'
            ]);
        }

        if (Auth::attempt([
            'email' => $request->email,
            'password' => $request->password,
            'restaurant_id' => $restaurantModel->id,
            'status' => 'Active'
        ])) {

            $request->session()->regenerate();

            return redirect()->route('restaurant.dashboard', [
                'restaurant' => $restaurantModel->slug
            ]);
        }

        return back()->withErrors([
            'email' => 'Invalid credentials.'
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Logout
    |--------------------------------------------------------------------------
    */

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
