<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
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
                '/'.session('restaurant_slug').'/dashboard'
            );
        }
        if ($user->role === 'customer') {

            $restaurant = $user->restaurant?->slug;
            $branch = $user->branch?->slug;

            return redirect("/{$restaurant}/{$branch}/dashboard");
        }

        return redirect('/dashboard');

    }

    /*
    |--------------------------------------------------------------------------
    | Restaurant Login Form
    |--------------------------------------------------------------------------
    */

    public function showRestaurantLogin($restaurant)
    {
        session([
            'restaurant_slug' => $restaurant,
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
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $restaurantModel = Restaurant::query()
            ->where('slug', $restaurant)
            ->firstOrFail();

        $branchModel = Branch::query()
            ->where('slug', $branch)
            ->where('restaurant_id', $restaurantModel->id)
            ->firstOrFail();

        // Check if user belongs to this branch
        $user = User::query()
            ->where('email', $request->email)
            ->where('restaurant_id', $restaurantModel->id)
            ->where('branch_id', $branchModel->id)
            ->first();

        if (! $user) {
            return back()
                ->withInput()
                ->withErrors([
                    'email' => 'You are not assigned to this branch.',
                ]);
        }

        // Login
        if (Auth::attempt([
            'email' => $request->email,
            'password' => $request->password,
            'status' => 'Active',
        ])) {

            $request->session()->regenerate();

            return redirect()->route('branch.dashboard', [
                'restaurant' => $restaurantModel->slug,
                'branch' => $branchModel->slug,
            ]);
        }

        return back()
            ->withInput()
            ->withErrors([
                'email' => 'Invalid credentials.',
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

        if (! $restaurantModel) {
            abort(404);
        }

        $user = User::query()->where('email', $request->email)
            ->where('restaurant_id', $restaurantModel->id)
            ->first();

        if (! $user) {
            return back()->withErrors([
                'email' => 'You are not allowed to login to this restaurant.',
            ]);
        }
        if ($user->branch_id) {
            return back()->withErrors([
                'email' => 'Please login using your branch login URL.',
            ]);
        }

        if (Auth::attempt([
            'email' => $request->email,
            'password' => $request->password,
            'restaurant_id' => $restaurantModel->id,
            'status' => 'Active',
        ])) {

            $request->session()->regenerate();

            // Branch user
            if ($user->branch_id) {

                $branch = Branch::query()->find($user->branch_id);

                return redirect()->route('branch.dashboard', [
                    'restaurant' => $restaurantModel->slug,
                    'branch' => $branch->slug,
                ]);
            }

            // Restaurant-level user
            return redirect()->route('restaurant.dashboard', [
                'restaurant' => $restaurantModel->slug,
            ]);
        }

        return back()->withErrors([
            'email' => 'Invalid credentials.',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Logout
    |--------------------------------------------------------------------------
    */

    public function logout(Request $request)
    {
        $user = Auth::user();

        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        // Customer logout redirect
        if ($user && $user->role === 'customer') {

            return redirect()->route('branch.register', [
                'restaurant' => optional($user->restaurant)->slug,
                'branch' => optional($user->branch)->slug,
            ]);

        }

        // Other users
        return redirect('/');
    }

}
