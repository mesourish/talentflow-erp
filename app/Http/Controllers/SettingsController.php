<?php

/**
 * TalentFlow ERP - Celebrity & Talent Management System
 *
 * @package    App\Http\Controllers
 * @author     Sourish Mondal
 * @version    1.0.0
 * @since      Laravel 12.x / PHP 8.2+
 * @copyright  2026 TalentFlow ERP. All rights reserved.
 * @license    Proprietary / Enterprise Commercial
 */

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

/**
 * Class SettingsController
 *
 * Manages system users, departments, and Spatie RBAC enterprise role permission assignments.
 *
 * @package App\Http\Controllers
 * @author  Sourish Mondal
 * @version 1.0.0
 */
class SettingsController extends Controller
{
    public function index(): View
    {
        $users = User::with(['roles', 'department'])->get();
        $departments = Department::withCount('users')->get();
        $roles = Role::withCount('users')->get();

        return view('settings.index', compact('users', 'departments', 'roles'));
    }

    public function storeUser(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'email' => 'required|email|max:150|unique:users,email',
            'phone' => 'nullable|string|max:30',
            'department_id' => 'nullable|exists:departments,id',
            'role' => 'required|exists:roles,name',
            'password' => 'required|string|min:6',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'department_id' => $validated['department_id'] ?? null,
            'password' => Hash::make($validated['password']),
            'is_active' => true,
        ]);

        $user->assignRole($validated['role']);

        return back()->with('success', "User {$user->name} created and assigned role {$validated['role']}.");
    }
}

