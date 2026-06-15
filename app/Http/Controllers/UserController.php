<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * User List
     */
    public function index(Request $request)
    {
        $restaurantSlug = $request->route('restaurant');
        $authUser = Auth::user();

        $query = User::with('restaurant');

        if ($authUser->role === 'super_admin') {

            // Super Admin sees all Owners
            $query->where('role', 'owner');
        } elseif ($authUser->role === 'owner') {

            // Owner sees Branch Managers, Waiter Heads, Waiters, Chefs
            $query->where('restaurant_id', $authUser->restaurant_id)
                ->whereIn('role', [
                    'branch_manager',
                    'waiter_head',
                    'waiter',
                    'chef'
                ]);
        } elseif ($authUser->role === 'branch_manager') {

            // Branch Manager sees Waiter Heads and Waiters
            $query->where('restaurant_id', $authUser->restaurant_id)
                ->whereIn('role', [
                    'waiter_head',
                    'waiter'
                ]);
        } else {

            // Other roles see nothing
            $query->whereRaw('1 = 0');
        }

        $users = $query->latest()->paginate(20);

        return view('admin.users.index', compact('users', 'restaurantSlug'));
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
                    'chef'
                ];
                break;
            case 'branch_manager':
                $roles = [
                    'waiter_head',
                    'waiter',
                    'cashier',
                    'chef'
                ];
                break;
        }
        $restaurants = Restaurant::query()->where('status', 1)->get();
        return view(
            'admin.users.create',
            compact('roles', 'restaurants', 'restaurant')
        );
    }
    /**
     * Store User
     */
    public function store(Request $request, $restaurant = null)
    {
        // dd($request->all());
        $validated = $request->validate([
            'name'          => 'required|max:255',
            'email'         => 'required|email|unique:users,email',
            'phone'         => 'required',
            'gender'        => 'required',
            'birth_date'    => 'required|date',
            'address'       => 'required',
            'role'          => 'required',
            'password'      => 'required|min:6|confirmed',
            'profile_photo' => 'nullable|image',
        ]);

        $profilePhoto = null;

        if ($request->hasFile('profile_photo')) {
            $profilePhoto = $request->file('profile_photo')
                ->store('profiles', 'public');
        }

        $user =  User::create([
            'name'          => $validated['name'],
            'email'         => $validated['email'],
            'phone'         => $validated['phone'],
            'gender'        => $validated['gender'],
            'birth_date'    => $validated['birth_date'],
            'address'       => $validated['address'],
            'role'          => $validated['role'],
            'profile_photo' => $profilePhoto,
            'restaurant_id' => Auth::id() ? Auth::user()->restaurant_id : null,
            'branch_id' => Auth::id() ? Auth::user()->restaurant_id : null,
            'status'        => 'active',
            'password'      => Hash::make($validated['password']),
            'created_by'    => Auth::id(),
        ]);
        // dd($user);
        $user->assignRole($validated['role']);
        // dd($user);
        // Super Admin
        if (Auth::user()?->role === 'super_admin') {
            return redirect()
                ->route('users.index')
                ->with('success', 'User created successfully.');
        }

        // Restaurant Users
        return redirect()
            ->route('restaurant.users.index', [
                'restaurant' => $restaurant
            ])
            ->with('success', 'User created successfully.');
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
        $roles = [
            'owner',
            'branch_manager',
            'waiter_head',
            'waiter',
            'cashier'
        ];
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
            'email' => 'required|email|unique:users,email,' . $userId,
            'phone' => 'required',
            'gender' => 'required',
            'birth_date' => 'required|date',
            'address' => 'required',
            'role' => 'required',
            'status' => 'required',
            'password' => 'nullable|min:6|confirmed',
            'profile_photo' => 'nullable|image',
            'restaurant_id' => 'nullable|exists:restaurants,id',
            'branch_id' => 'nullable|exists:restaurants,id',
        ]);
        if ($request->hasFile('profile_photo')) {
            if (
                $user->profile_photo &&
                Storage::disk('public')->exists($user->profile_photo)
            ) {
                Storage::disk('public')->delete($user->profile_photo);
            }
            $validated['profile_photo'] = $request->file('profile_photo')->store('profiles', 'public');
        }
        if ($request->filled('password')) {
            $validated['password'] = Hash::make($request->password);
        } else {
            unset($validated['password']);
        }
        $validated['updated_by'] = Auth::id();
        // For Owner/Branch Manager keep current restaurant

        if (Auth::user()?->role != 'super_admin') {
            unset($validated['restaurant_id']);
            unset($validated['branch_id']);
        }
        //         dd([
        //     'user_branch' => $user->branch_id,
        //     'auth_branch' => Auth::user()->branch_id ?? null,
        //     'request_branch' => $request->branch_id,
        // ]);
        $user->update($validated);
        if (Auth::user()?->role == 'super_admin') {
            return redirect()->route('users.index')
                ->with(
                    'success',
                    'User updated successfully.'
                );
        }
        return redirect()->route(
            'restaurant.users.index',
            [
                'restaurant' => optional(Auth::user()?->restaurant)->slug
            ]
        )
            ->with(
                'success',
                'User updated successfully.'
            );
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
        if (Auth::user()->role == 'super_admin') {
            return redirect()->route('users.index')
                ->with(
                    'success',
                    'User deactivated successfully.'
                );
        }
        return redirect()
            ->route(
                'restaurant.users.index',
                [
                    'restaurant' => Auth::user()?->restaurant?->slug,
                ]
            )
            ->with(
                'success',
                'User deactivated successfully.'
            );
    }
}
