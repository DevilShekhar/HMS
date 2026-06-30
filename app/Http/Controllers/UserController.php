<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view-user')->only(['index', 'show']);
        $this->middleware('permission:create-user')->only(['create', 'store']);
        $this->middleware('permission:edit-user')->only(['edit', 'update']);
        $this->middleware('permission:delete-user')->only(['destroy']);
    }

    /**
     * User List
     */
    public function index(Request $request)
    {
        $authUser = Auth::user();

        $query = User::query();

        if ($authUser->role === 'super_admin') {

            $query->where('role', 'owner');
        } elseif ($authUser->role === 'owner') {

            $query->where('restaurant_id', $authUser->restaurant_id)
                ->whereIn('role', [
                    'branch_manager',
                    'user',
                ]);

        } elseif ($authUser->role === 'branch_manager') {

            $query->where('restaurant_id', $authUser->restaurant_id)
                ->where('branch_id', $authUser->branch_id)
                ->whereNotIn('role', [
                    'super_admin',
                    'owner',
                    'branch_manager',
                    'customer',
                ]);
        } else {

            $query->whereRaw('1 = 0', [], 'and');
        }

        $users = $query->latest()->paginate(20);

        $restaurantSlug = $request->route('restaurant');

        return view('admin.users.index', compact(
            'users',
            'restaurantSlug'
        ));
    }

    /**
     * Create Form
     */
    public function create($restaurant = null)
    {
        $roles = [];
        switch (Auth::user()->role) {
            case 'super_admin':
                $roles = ['owner'];
                break;
            case 'owner':
                $roles = [
                    'branch_manager',
                    'waiter_head',
                    'waiter',
                    'cashier',
                    'chef',
                ];
                break;
            case 'branch_manager':
                $roles = [
                    'waiter_head',
                    'waiter',
                    'cashier',
                    'chef',
                ];
                break;
        }
        $restaurants = Restaurant::query()->where('status', 1)->get();
        $branches = [];

        // Owner can select any branch of his restaurant
        if (Auth::user()->role === 'owner') {

            $branches = Branch::query()->where(
                'restaurant_id',
                Auth::user()->restaurant_id
            )->get();
        }

        return view(
            'admin.users.create',
            compact('roles', 'restaurants', 'restaurant', 'branches')
        );
    }

    /**
     * Store User
     */
    public function store(Request $request, $restaurant = null)
    {
        $validated = $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required',
            'gender' => 'required',
            'birth_date' => 'required|date',
            'address' => 'required',
            'role' => 'required',
            'password' => 'required|min:6|confirmed',
            'branch_id' => Auth::user()->role === 'owner'
                ? 'required|exists:branches,id'
                : 'nullable',
            'profile_photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $profilePhoto = null;

        if ($request->hasFile('profile_photo')) {
            $file = $request->file('profile_photo');
            $filename = time().'_'.$file->getClientOriginalName();
            // Create folder if not exists
            if (! file_exists(public_path('uploads/profiles'))) {
                mkdir(public_path('uploads/profiles'), 0777, true);
            }
            $file->move(
                public_path('uploads/profiles'),
                $filename
            );
            $profilePhoto = 'uploads/profiles/'.$filename;
        }

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'gender' => $validated['gender'],
            'birth_date' => $validated['birth_date'],
            'address' => $validated['address'],
            'role' => $validated['role'],
            'profile_photo' => $profilePhoto,
            'restaurant_id' => Auth::user()->restaurant_id,

            'branch_id' => Auth::user()->role === 'owner'
                ? $validated['branch_id']
                : Auth::user()->branch_id,
            'status' => 'active',
            'password' => Hash::make($validated['password']),
            'created_by' => Auth::id(),
        ]);
        $user->assignRole($validated['role']);

        // Super Admin
        if (Auth::user()->role === 'super_admin') {

            return redirect()
                ->route('users.index')
                ->with('success', 'User created successfully.');
        }

        // Branch Manager
        if (Auth::user()->role === 'branch_manager') {

            return redirect()
                ->route('branch.users.index', [
                    'restaurant' => Auth::user()->restaurant?->slug,
                    'branch' => Auth::user()->branch?->slug,
                ])
                ->with('success', 'User created successfully.');
        }

        // Owner
        if (Auth::user()->role === 'owner') {
            return redirect()
                ->route('restaurant.users.index', [
                    'restaurant' => Auth::user()->restaurant?->slug,
                ])
                ->with('success', 'User created successfully.');
        }
    }

    /**
     * Edit Form
     */
    public function edit(Request $request)
    {
        $restaurant = $request->route('restaurant'); // null for super admin
        $userId = $request->route('user');
        $user = User::findOrFail($userId);
        $restaurants = Restaurant::query()->where('status', 1)->get();
        $roles = [];
        switch (Auth::user()->role) {
            case 'super_admin':
                $roles = ['owner'];
                break;
            case 'owner':
                $roles = [
                    'branch_manager',
                    'waiter_head',
                    'waiter',
                    'cashier',
                    'chef',
                ];
                break;
            case 'branch_manager':
                $roles = [
                    'waiter_head',
                    'waiter',
                    'cashier',
                    'chef',
                ];
                break;
        }

        return view(
            'admin.users.edit',
            compact(
                'user',
                'roles',
                'restaurants',
                'restaurant'
            )
        );
    }

    /**
     * Update User
     */
    public function update(Request $request)
    {
        $userId = $request->route('user');
        $user = User::findOrFail($userId);
        $validated = $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|email|unique:users,email,'.$userId,
            'phone' => 'required',
            'gender' => 'required',
            'birth_date' => 'required|date',
            'address' => 'required',
            'role' => 'required',
            'status' => 'required',
            'password' => 'nullable|min:6|confirmed',
            'profile_photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'restaurant_id' => 'nullable|exists:restaurants,id',
            'branch_id' => 'nullable|exists:branches,id',
        ]);
        // Upload Profile Photo
        if ($request->hasFile('profile_photo')) {
            // Delete old image
            if (
                $user->profile_photo &&
                file_exists(public_path($user->profile_photo))
            ) {
                unlink(public_path($user->profile_photo));
            }
            $file = $request->file('profile_photo');
            $filename = time().'_'.$file->getClientOriginalName();
            // Make sure folder exists
            if (! file_exists(public_path('uploads/profiles'))) {
                mkdir(public_path('uploads/profiles'), 0777, true);
            }
            $file->move(
                public_path('uploads/profiles'),
                $filename
            );
            $validated['profile_photo'] =
                'uploads/profiles/'.$filename;
        }
        // Update password only if entered
        if ($request->filled('password')) {

            $validated['password'] =
                Hash::make($request->password);
        } else {
            unset($validated['password']);
        }
        $validated['updated_by'] = Auth::id();
        // Owner / Branch Manager cannot change restaurant & branch
        if (Auth::user()?->role != 'super_admin') {
            unset($validated['restaurant_id']);
            unset($validated['branch_id']);
        }
        $user->update($validated);
        // Super Admin
        if (Auth::user()->role === 'super_admin') {

            return redirect()
                ->route('users.index')
                ->with('success', 'User created successfully.');
        }

        // Branch Manager
        if (Auth::user()->role === 'branch_manager') {

            return redirect()
                ->route('branch.users.index', [
                    'restaurant' => Auth::user()->restaurant?->slug,
                    'branch' => Auth::user()->branch?->slug,
                ])
                ->with('success', 'User created successfully.');
        }

        // Owner
        if (Auth::user()->role === 'owner') {
            return redirect()
                ->route('restaurant.users.index', [
                    'restaurant' => Auth::user()->restaurant?->slug,
                ])
                ->with('success', 'User created successfully.');
        }
    }

    /**
     * Delete User
     */
    public function destroy(Request $request)
    {
        $restaurant = $request->route('restaurant');
        $userId = $request->route('user');
        $user = User::findOrFail($userId);
        $user->update([
            'status' => 'inactive',
            'updated_by' => Auth::id(),
        ]);
        if (Auth::user()->role === 'super_admin') {

            return redirect()
                ->route('users.index')
                ->with('success', 'User created successfully.');
        }

        // Branch Manager
        if (Auth::user()->role === 'branch_manager') {

            return redirect()
                ->route('branch.users.index', [
                    'restaurant' => Auth::user()->restaurant?->slug,
                    'branch' => Auth::user()->branch?->slug,
                ])
                ->with('success', 'User created successfully.');
        }

        // Owner
        if (Auth::user()->role === 'owner') {
            return redirect()
                ->route('restaurant.users.index', [
                    'restaurant' => Auth::user()->restaurant?->slug,
                ])
                ->with('success', 'User created successfully.');
        }
    }
}
