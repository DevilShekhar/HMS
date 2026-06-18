<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class PermissionController extends Controller
{
    public function index()
    {
        if (!Auth::user()?->can('manage-permission')) {
            abort(403);
        }
        $permissions = Permission::with('roles')->latest()->get();
        $roles = Role::query()->get();

        return view('admin.permissions.index', compact(
            'permissions',
            'roles',
        ));
    }

    public function create()
    {
        $roles = Role::all();

        return view('admin.permissions.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:permissions,name',
            'role_id' => 'nullable|exists:roles,id',
        ]);

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permission = Permission::create([
            'name' => $request->name,
            'guard_name' => 'web',
            'status' => 1,
        ]);



        return redirect()->route('permissions.index')
            ->with('success', 'Permission Created successfully');
    }

    public function edit($id)
    {

        $permission = Permission::with('roles')->findOrFail($id);

        $roles = Role::all();

        $selectedRole = $permission->roles->first()?->id;

        return view('admin.permissions.edit', compact(
            'permission',
            'roles',
            'selectedRole',
        ));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|unique:permissions,name,' . $id,
            'role_id' => 'nullable|exists:roles,id',
        ]);

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permission = Permission::findById($id);

        $permission->update([
            'name' => $request->name,
            'guard_name' => 'web',
        ]);

        return redirect()->route('permissions.index')
            ->with('success', 'Permisison Updated successfully');
    }

    public function destroy($id)
    {
        $permission = Permission::findOrFail($id);

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permission->update([
            'status' => 0
        ]);

        return back()->with('success', 'Permission deleted successfully');
    }
}
