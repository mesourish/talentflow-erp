@extends('layouts.app')

@section('title', 'System Settings & Access Control')

@section('content')
<div class="space-y-6" x-data="{ activeTab: 'users', userModal: false }">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">System Settings & RBAC</h1>
            <p class="text-sm text-gray-500 mt-1">Manage users, 8 enterprise role assignments, departments, and security permissions</p>
        </div>
        <div class="flex items-center gap-3">
            <button @click="userModal = true" class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                Add System User
            </button>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="border-b border-gray-200 bg-white rounded-t-xl px-4 pt-2">
        <nav class="-mb-px flex space-x-8">
            <button @click="activeTab = 'users'" :class="activeTab === 'users' ? 'border-indigo-600 text-indigo-600 font-bold' : 'border-transparent text-gray-500 hover:text-gray-700'" class="whitespace-nowrap py-3 px-1 border-b-2 text-sm">
                Staff & User Accounts ({{ $users->count() }})
            </button>
            <button @click="activeTab = 'roles'" :class="activeTab === 'roles' ? 'border-indigo-600 text-indigo-600 font-bold' : 'border-transparent text-gray-500 hover:text-gray-700'" class="whitespace-nowrap py-3 px-1 border-b-2 text-sm">
                Enterprise Roles & RBAC ({{ $roles->count() }})
            </button>
            <button @click="activeTab = 'departments'" :class="activeTab === 'departments' ? 'border-indigo-600 text-indigo-600 font-bold' : 'border-transparent text-gray-500 hover:text-gray-700'" class="whitespace-nowrap py-3 px-1 border-b-2 text-sm">
                Departments ({{ $departments->count() }})
            </button>
        </nav>
    </div>

    <!-- TAB 1: USERS -->
    <div x-show="activeTab === 'users'" x-cloak class="space-y-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50 text-gray-500 uppercase font-semibold text-xs">
                        <tr>
                            <th class="px-4 py-3 text-left">User</th>
                            <th class="px-4 py-3 text-left">Email / Contact</th>
                            <th class="px-4 py-3 text-left">Department</th>
                            <th class="px-4 py-3 text-left">Assigned Role</th>
                            <th class="px-4 py-3 text-center">Status</th>
                            <th class="px-4 py-3 text-right">Registered</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($users as $user)
                            <tr class="hover:bg-gray-50 text-xs">
                                <td class="px-4 py-3 font-bold text-gray-900 flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold text-xs">
                                        {{ substr($user->name, 0, 1) }}
                                    </div>
                                    <span>{{ $user->name }}</span>
                                </td>
                                <td class="px-4 py-3 text-gray-600">
                                    <div>{{ $user->email }}</div>
                                    <div class="text-gray-400 font-mono">{{ $user->phone ?: '-' }}</div>
                                </td>
                                <td class="px-4 py-3 text-gray-700">
                                    {{ $user->department?->name ?? 'General' }}
                                </td>
                                <td class="px-4 py-3">
                                    @foreach($user->roles as $role)
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-purple-50 text-purple-700 border border-purple-200 uppercase">
                                            {{ $role->name }}
                                        </span>
                                    @endforeach
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                        Active
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right text-gray-500">
                                    {{ $user->created_at?->format('d M Y') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- TAB 2: ROLES & RBAC -->
    <div x-show="activeTab === 'roles'" x-cloak class="space-y-4" style="display: none;">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach($roles as $role)
                <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-indigo-50 text-indigo-700 uppercase">Role</span>
                        <span class="text-xs text-gray-500 font-semibold">{{ $role->users_count }} Users</span>
                    </div>
                    <h3 class="font-bold text-gray-900 text-base">{{ $role->name }}</h3>
                    <p class="text-xs text-gray-500">
                        @if($role->name === 'Super Admin')
                            Unrestricted root access to all modules, financial ledgers, and database settings.
                        @elseif($role->name === 'Management')
                            Executive read/write access to financial forecasts, contracts, and talent rosters.
                        @elseif($role->name === 'Finance Manager')
                            Authorized for invoice generation, payment collections, TDS and payouts.
                        @elseif($role->name === 'Booking Coordinator')
                            Manages logistics, calendar conflict resolution, and venue call sheets.
                        @elseif($role->name === 'Project Manager')
                            Controls project milestones, tasks, team allocation, and expense reviews.
                        @elseif($role->name === 'Sales Manager')
                            Manages leads, client relationships, and quotation approvals.
                        @elseif($role->name === 'CRM Executive')
                            Lead generation, client onboarding, and CRM communication logs.
                        @elseif($role->name === 'Document Manager')
                            DMS encryption management, contract archiving, and access logs.
                        @else
                            Standard enterprise role.
                        @endif
                    </p>
                </div>
            @endforeach
        </div>
    </div>

    <!-- TAB 3: DEPARTMENTS -->
    <div x-show="activeTab === 'departments'" x-cloak class="space-y-4" style="display: none;">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @foreach($departments as $dept)
                <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="font-mono text-xs text-gray-400 font-semibold">{{ $dept->code }}</span>
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-gray-100 text-gray-700">{{ $dept->users_count }} Staff</span>
                    </div>
                    <h3 class="font-bold text-gray-900 text-base">{{ $dept->name }}</h3>
                    <p class="text-xs text-gray-500">{{ $dept->description ?: 'Operational business division of TalentFlow.' }}</p>
                </div>
            @endforeach
        </div>
    </div>

    <!-- MODAL: ADD USER -->
    <div x-show="userModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" @click="userModal = false"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full p-6">
                <h3 class="text-lg font-bold text-gray-900">Add Staff User</h3>
                <p class="text-xs text-gray-500 mt-1">Create user credentials and assign enterprise RBAC permissions</p>

                <form method="POST" action="{{ route('admin.settings.users.store') }}" class="mt-4 space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Full Name *</label>
                        <input type="text" name="name" required placeholder="e.g. Rahul Sharma" class="w-full px-3 py-2 border rounded-lg text-sm">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Email Address *</label>
                        <input type="email" name="email" required placeholder="e.g. rahul@talentflow.test" class="w-full px-3 py-2 border rounded-lg text-sm">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Phone Number</label>
                        <input type="text" name="phone" placeholder="+91 98765 43210" class="w-full px-3 py-2 border rounded-lg text-sm">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Department</label>
                            <select name="department_id" class="w-full px-3 py-2 border rounded-lg text-sm">
                                <option value="">None</option>
                                @foreach($departments as $d)
                                    <option value="{{ $d->id }}">{{ $d->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Role *</label>
                            <select name="role" required class="w-full px-3 py-2 border rounded-lg text-sm">
                                @foreach($roles as $r)
                                    <option value="{{ $r->name }}">{{ $r->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Temporary Password *</label>
                        <input type="password" name="password" required value="password" class="w-full px-3 py-2 border rounded-lg text-sm">
                    </div>

                    <div class="flex justify-end gap-2 pt-3 border-t">
                        <button type="button" @click="userModal = false" class="px-4 py-2 border rounded-lg text-xs">Cancel</button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-xs font-bold">Create User</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

