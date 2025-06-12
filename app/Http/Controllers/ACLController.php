<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\User;
use App\Models\Permission;

class ACLController extends Controller
{
    // Show roles and permissions for assignment
    public function index()
    {
        $roles = Role::with('permissions')->get();
        $permissions = Permission::all();

        $rolePermissions = [];
        foreach ($roles as $role) {
            $rolePermissions[$role->id] = $role->permissions->pluck('name')->toArray();
        }

        return view('acl.assign_role_permissions', compact('roles', 'permissions', 'rolePermissions'));
    }

    // Assign permissions to a role
    public function assign(Request $request)
    {
        $request->validate([
            'role_id' => 'required|exists:roles,id',
            'permission_ids' => 'array',
            'permission_ids.*' => 'exists:permissions,id',
        ]);

        $role = Role::findOrFail($request->role_id);
        $role->permissions()->sync($request->permission_ids ?? []);

        return back()->with('success', 'Permissions updated for role');
    }

    public function aclIndex()
    {
        $users = User::with(['role.permissions'])->get();
        $roles = Role::all();
        $permissions = Permission::all();

        return view('acl.index', compact('users', 'roles', 'permissions'));
    }

    // Update permissions for multiple users (sync)
    public function updateUserPermissions(Request $request)
    {
        $request->validate([
            'users' => 'array',
            'users.*' => 'array',
            'users.*.*' => 'exists:permissions,id',
        ]);

        foreach ($request->input('users', []) as $userId => $permissionIds) {
            $user = User::find($userId);
            if ($user) {
                $user->permissions()->sync($permissionIds);
            }
        }

        return back()->with('success', 'Permissions updated successfully!');
    }

    // Update all role permissions in bulk (handles array of role_id => permissions names)
    public function updateAllPermissions(Request $request)
    {
        $request->validate([
            'roles_permissions' => 'array',
        ]);

        foreach ($request->input('roles_permissions', []) as $roleId => $permissions) {
            $role = Role::find($roleId);
            if ($role) {
                $permissionIds = Permission::whereIn('name', $permissions)->pluck('id')->toArray();
                $role->permissions()->sync($permissionIds);
            }
        }

        return back()->with('success', 'All role permissions updated successfully.');
    }

    // Update permissions for a single role
    public function updateRolePermissions(Request $request)
    {
        $request->validate([
            'role_id' => 'required|exists:roles,id',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        $role = Role::findOrFail($request->role_id);
        $permissionIds = Permission::whereIn('name', $request->input('permissions', []))->pluck('id')->toArray();
        $role->permissions()->sync($permissionIds);

        return back()->with('success', 'Permissions updated successfully.');
    }

    // Manage users page — only accessible by superadmin
    public function manageUsers(Request $request)
    {
        $this->authorizeSuperadmin();
        $query = User::with('role');
        if ($request->ajax()) {
        if ($search = $request->get('search')) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->get();

        return response()->json(['users' => $users]);
    }

        $users = $query->get();
        $roles = Role::all();

        return view('acl.manage_users', compact('users', 'roles'));
    }

    public function updateUserRole(Request $request, User $user)
    {
        $this->authorizeSuperadmin();

        $request->validate([
            'role_id' => 'required|exists:roles,id',
        ]);

        if (auth()->id() == $user->id) {
            return back()->with('error', 'You cannot change your own role.');
        }

        $user->role_id = $request->role_id;
        $user->save();

        return back()->with('success', 'User role updated successfully.');
    }

    private function authorizeSuperadmin()
    {
        if (optional(auth()->user()->role)->name !== 'superadmin') {
            abort(403, 'Unauthorized action.');
        }
    }

    public function show($id)
    {
        $this->authorizeSuperadmin();

        $user = User::with('role')->findOrFail($id);

        return response()->json($user);
    }


    public function update(Request $request, $id)
    {
        $this->authorizeSuperadmin();

        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role_id' => 'required|exists:roles,id',
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->role_id = $request->role_id;

        if ($request->filled('password')) {
            $user->password = bcrypt($request->password);
        }

        $user->save();

        return response()->json(['message' => 'User updated successfully']);
    }

    // Soft delete user
    public function destroy($id)
    {
        $this->authorizeSuperadmin();

        $user = User::findOrFail($id);

        if (auth()->id() == $user->id) {
            return response()->json(['message' => 'You cannot delete your own account.'], 403);
        }

        $user->delete();

        return response()->json(['message' => 'User deleted successfully']);
    }

}
