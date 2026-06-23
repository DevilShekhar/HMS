<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/orders/create';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['required', 'max:15', 'unique:users,phone'],
            'password' => ['required', 'confirmed', 'min:6'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'password' => Hash::make(Str::random(12)),
            'role' => 'customer',
            'status' => 'active',
        ]);
    }

    public function showBranchRegister(
        Restaurant $restaurant,
        Branch $branch
    ) {
        if ($branch->restaurant_id != $restaurant->id) {
            abort(404);
        }

        return view('auth.register', compact(
            'restaurant',
            'branch'
        ));
    }

    public function branchRegister(Request $request, $restaurant, $branch)
    {

        $restaurantModel = Restaurant::query()->where('slug', $restaurant)
            ->firstOrFail();

        $branchModel = Branch::query()->where('slug', $branch)
            ->where('restaurant_id', $restaurantModel->id)
            ->firstOrFail();

        $this->validator($request->all())->validate();

        // Check existing customer
        $existingCustomer = User::query()->where('phone', $request->phone)
            ->where('restaurant_id', $restaurantModel->id)
            ->where('branch_id', $branchModel->id)
            ->where('role', 'customer')
            ->first();

        if ($existingCustomer) {

            Auth::login($existingCustomer);

            return redirect()->route('restaurant.orders.create', [
                'restaurant' => $restaurantModel->slug,
                'branch' => $branchModel->slug,
            ])->with(
                'success',
                'Welcome back!'
            );
        }

        // New customer create

        $user = User::create([

            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role' => 'customer',
            'status' => 'active',
            'restaurant_id' => $restaurantModel->id,
            'branch_id' => $branchModel->id,
        ]);

        Auth::login($user);

        return redirect()->route('restaurant.orders.create', [
            'restaurant' => $restaurantModel->slug,
            'branch' => $branchModel->slug,
        ]);
    }

    public function showCustomerLogin(Restaurant $restaurant, Branch $branch)
    {

        if ($branch->restaurant_id != $restaurant->id) {
            abort(404);
        }

        return view('auth.customer', compact(
            'restaurant',
            'branch'
        ));
    }

    public function customerLogin(
        Request $request,
        $restaurant,
        $branch
    ) {

        $request->validate([
            'phone' => 'required',
            'password' => 'required',
        ]);

        $restaurantModel = Restaurant::query()->where('slug', $restaurant)
            ->firstOrFail();

        $branchModel = Branch::query()->where('slug', $branch)
            ->where('restaurant_id', $restaurantModel->id)
            ->firstOrFail();

        $customer = User::query()->where('phone', $request->phone)
            ->where('restaurant_id', $restaurantModel->id)
            ->where('branch_id', $branchModel->id)
            ->where('role', 'customer')
            ->first();

        if (! $customer) {

            return back()
                ->withErrors([
                    'phone' => 'Customer not found.',
                ]);

        }

        // Check password

        if (! Hash::check($request->password, $customer->password)) {

            return back()
                ->withErrors([
                    'password' => 'Incorrect password.',
                ]);

        }

        // Login customer

        Auth::login($customer);

        $request->session()->regenerate();

        return redirect()->route('restaurant.orders.create', [
            'restaurant' => $restaurantModel->slug,
            'branch' => $branchModel->slug,
        ]);

    }
}
