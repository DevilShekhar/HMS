<?php


namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Spatie\Permission\PermissionRegistrar;

class RoleController extends Controller
{
    public function index()
    {
        if (!Auth::user()?->can('view-role')) {
            abort(403);
        }
        $roles = Role::all();
        $permissions = Permission::all();

        $role = null;
        $rolePermissions = [];

        return view('admin.roles.index', compact(
            'roles',
            'permissions',
            'role',
            'rolePermissions',
        ));
    }

    public function create()
    {
        return view('admin.roles.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:roles,name',
        ]);

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        Role::create([
            'name' => $request->name,
            'guard_name' => 'web',
        ]);

        return redirect()->route('roles.index')
            ->with('success', 'Role Created successfully');
    }

    public function update(Request $request, $id)
    {
        $role = Role::findOrFail($id);

        $request->validate([
            'name' => 'required|unique:roles,name,' . $id,
        ]);

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $role->update([
            'name' => $request->name,
        ]);

        return redirect()->route('roles.index')
            ->with('success', 'Role updated successfully');
    }

    public function destroy($role)
    {
        $role = Role::findOrFail($role);
        $role->delete();

        return redirect()->route('roles.index')
            ->with('success', 'Role Deleted successfully');
    }

    public function managePermissions(Role $role)
    {
        $permissions = Permission::all()
            ->groupBy(function ($permission) {

                $parts = explode('-', $permission->name);

                return ucfirst(end($parts)) . ' Permissions';
            });

        $rolePermissions = $role->permissions()
            ->pluck('name')
            ->toArray();

        return view('admin.roles.permissions', compact(
            'role',
            'permissions',
            'rolePermissions'
        ));
    }


    public function updatePermissions(Request $request, Role $role)
    {
        $role->syncPermissions($request->permissions ?? []);

        return redirect()
            ->route('roles.index')
            ->with('success', 'Permissions updated successfully');
    }
    public function getPermissionsData($role)
    {
        $role = Role::with('permissions')->findOrFail($role);

        return response()->json([
            'role' => $role,
            'permissions' => Permission::all(),
            'rolePermissions' => $role->permissions->pluck('name')->toArray()
        ]);
    }
}
