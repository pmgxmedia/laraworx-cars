<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminUserController extends Controller
{
    public function index(Request $request): View
    {
        $rows = User::query()
            ->with('roles')
            ->withCount(['purchaseClaims', 'buyerRewards', 'enquiries'])
            ->when($request->filled('role'), fn ($q) => $q->whereHas('roles', fn ($r) => $r->where('name', $request->input('role'))))
            ->when($request->has('is_active') && $request->input('is_active') !== '', fn ($q) => $q->where('is_active', $request->boolean('is_active')))
            ->when($request->filled('q'), fn ($q) => $q->where(fn ($w) => $w
                ->where('name', 'like', "%{$request->input('q')}%")
                ->orWhere('surname', 'like', "%{$request->input('q')}%")
                ->orWhere('email', 'like', "%{$request->input('q')}%")))
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        $roles = Role::orderBy('name')->get();

        return view('admin.users.index', compact('rows', 'roles'));
    }

    public function show(User $user): View
    {
        $user->load(['roles.permissions', 'enquiries', 'purchaseClaims', 'buyerRewards']);

        $roles = Role::orderBy('name')->get();

        return view('admin.users.show', compact('user', 'roles'));
    }

    public function assignRoles(Request $request, User $user): RedirectResponse
    {
        abort_unless(auth()->user()->hasRole([User::ROLE_SUPER_ADMIN, User::ROLE_OPERATIONS]), 403, 'You do not have permission to assign roles.');

        $data = $request->validate([
            'roles' => ['nullable', 'array'],
            'roles.*' => ['integer', 'exists:roles,id'],
        ]);

        $user->roles()->sync($data['roles'] ?? []);

        return back()->with('success', 'Roles updated for '.$user->fullName().'.');
    }

    public function toggleActive(User $user): RedirectResponse
    {
        abort_unless(auth()->user()->hasRole([User::ROLE_SUPER_ADMIN, User::ROLE_OPERATIONS, User::ROLE_SUPPORT]), 403, 'You do not have permission to manage users.');

        if ($user->id === auth()->id()) {
            return back()->with('info', 'You cannot deactivate your own account.');
        }

        $user->update(['is_active' => ! $user->is_active]);

        return back()->with('success', $user->is_active ? 'User account activated.' : 'User account deactivated.');
    }
}