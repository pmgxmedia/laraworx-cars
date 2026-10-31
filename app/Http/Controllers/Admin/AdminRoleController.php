<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminRoleController extends Controller
{
    public function index(): View
    {
        abort_unless(auth()->user()->hasRole([User::ROLE_SUPER_ADMIN]), 403, 'You do not have permission to manage roles.');

        $roles = Role::withCount(['users', 'permissions'])->with('permissions')->orderBy('name')->get();
        $permissions = Permission::orderBy('group')->orderBy('label')->get()->groupBy('group');
        $managed = Role::whereNotIn('name', [Role::DEALERSHIP, Role::PRIVATE_SELLER, Role::CUSTOMER])->get();

        return view('admin.roles.index', compact('roles', 'permissions', 'managed'));
    }

    public function update(Request $request, Role $role): RedirectResponse
    {
        abort_unless(auth()->user()->hasRole([User::ROLE_SUPER_ADMIN]), 403, 'You do not have permission to manage roles.');

        $data = $request->validate([
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['integer', 'exists:permissions,id'],
        ]);

        $role->permissions()->sync($data['permissions'] ?? []);

        return back()->with('success', 'Permissions updated for role '.($role->label ?? $role->name).'.');
    }
}