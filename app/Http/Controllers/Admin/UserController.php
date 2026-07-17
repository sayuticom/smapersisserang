<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('roles')->orderBy('created_at', 'desc')->get();
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        $roles = Role::active()->ordered()->get();
        return view('admin.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'roles' => 'required|array|min:1',
            'roles.*' => ['exists:roles,id', function ($attribute, $value, $fail) {
                $role = Role::find($value);
                if ($role && !$role->is_active) {
                    $fail("Role \"{$role->display_name}\" tidak aktif dan tidak dapat diberikan.");
                }
            }],
        ]);

        if (! $request->user()->isSuperadmin()) {
            $superadminRole = Role::where('name', 'superadmin')->first();
            if ($superadminRole && in_array($superadminRole->id, $validated['roles'])) {
                abort(403, 'Anda tidak memiliki izin untuk menetapkan role Superadmin.');
            }
        }

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'admin',
        ]);

        $user->roles()->attach($validated['roles']);

        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil ditambahkan.');
    }

    public function edit(User $user)
    {
        $user->load('roles');
        $activeRoles = Role::active()->ordered()->get();
        $inactiveRoles = Role::where('is_active', false)->ordered()->get();
        $roles = $activeRoles->concat($inactiveRoles);
        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'roles' => 'required|array|min:1',
            'roles.*' => ['exists:roles,id', function ($attribute, $value, $fail) use ($user) {
                $role = Role::find($value);
                if ($role && !$role->is_active) {
                    $userAlreadyHasIt = $user->roles()->where('role_id', $value)->exists();
                    if (!$userAlreadyHasIt) {
                        $fail("Role \"{$role->display_name}\" tidak aktif dan tidak dapat diberikan.");
                    }
                }
            }],
        ];

        $validated = $request->validate($rules);

        $currentUser = $request->user();
        $superadminRole = Role::where('name', 'superadmin')->first();
        $requestedRoles = Role::whereIn('id', $validated['roles'])->pluck('name')->toArray();
        $requestedSuperadmin = in_array('superadmin', $requestedRoles);
        $userHasSuperadmin = $user->isSuperadmin();

        if (!$currentUser->isSuperadmin()) {
            abort(403, 'Hanya superadmin yang dapat mengubah role.');
        }

        if ($currentUser->id !== $user->id && $userHasSuperadmin && !$currentUser->isSuperadmin()) {
            abort(403, 'Anda tidak memiliki izin untuk mengubah user Superadmin.');
        }

        if ($currentUser->id === $user->id && !$requestedSuperadmin) {
            abort(403, 'Anda tidak dapat mencabut role Superadmin dari diri sendiri.');
        }

        if ($userHasSuperadmin && !$requestedSuperadmin) {
            $otherSuperadminExists = User::whereHas('roles', fn($q) => $q->where('name', 'superadmin'))
                ->where('id', '!=', $user->id)
                ->exists();
            if (!$otherSuperadminExists) {
                abort(403, 'Tidak dapat mencabut Superadmin. Harus ada setidaknya satu Superadmin.');
            }
        }

        if (!$currentUser->isSuperadmin() && $requestedSuperadmin) {
            abort(403, 'Anda tidak memiliki izin untuk menetapkan role Superadmin.');
        }

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
        ];

        if (filled($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        $user->update($data);
        $user->roles()->sync($validated['roles']);

        $firstRole = Role::whereIn('id', $validated['roles'])->orderBy('name')->first();
        if ($firstRole) {
            $user->updateQuietly(['role' => $firstRole->name]);
        }

        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil diperbarui.');
    }
}
