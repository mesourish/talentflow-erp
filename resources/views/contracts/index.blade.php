@extends('layouts.app')

@section('title', 'Contracts & Legal Management')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Contracts & Legal</h1>
            <p class="text-sm text-gray-500 mt-1">Manage brand endorsement agreements, talent NDAs, and usage rights licences</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.contracts.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                New Contract
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
        <form method="GET" action="{{ route('admin.contracts.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-4">
            <div class="sm:col-span-2">
                <label class="block text-xs font-semibold uppercase text-gray-500 mb-1">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by title or contract #..." class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase text-gray-500 mb-1">Status</label>
                <select name="status" class="w-full py-2 px-3 border border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">All Statuses</option>
                    @foreach($statuses as $st)
                        <option value="{{ $st }}" {{ request('status') === $st ? 'selected' : '' }}>{{ ucfirst($st) }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase text-gray-500 mb-1">Type</label>
                <div class="flex gap-2">
                    <select name="contract_type" class="w-full py-2 px-3 border border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">All Types</option>
                        @foreach($types as $tp)
                            <option value="{{ $tp }}" {{ request('contract_type') === $tp ? 'selected' : '' }}>{{ $tp }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg">Filter</button>
                    @if(request()->hasAny(['search', 'status', 'contract_type', 'expiring_soon']))
                        <a href="{{ route('admin.contracts.index') }}" class="px-3 py-2 bg-red-50 hover:bg-red-100 text-red-600 text-sm font-medium rounded-lg flex items-center">Reset</a>
                    @endif
                </div>
            </div>

            <div class="sm:col-span-4 pt-1">
                <label class="inline-flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="expiring_soon" value="1" {{ request('expiring_soon') ? 'checked' : '' }} onchange="this.form.submit()" class="rounded text-amber-600 focus:ring-amber-500 h-4 w-4">
                    <span class="text-xs font-semibold text-amber-700">Show only contracts expiring within 30 days (Expiry Alert)</span>
                </label>
            </div>
        </form>
    </div>

    <!-- Contracts Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50 text-gray-500 uppercase font-semibold text-xs">
                    <tr>
                        <th class="px-4 py-3 text-left">Contract #</th>
                        <th class="px-4 py-3 text-left">Title & Type</th>
                        <th class="px-4 py-3 text-left">Parties</th>
                        <th class="px-4 py-3 text-left">Project</th>
                        <th class="px-4 py-3 text-left">Validity Period</th>
                        <th class="px-4 py-3 text-center">Status</th>
                        <th class="px-4 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($contracts as $contract)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-mono text-xs font-bold text-indigo-600">
                                <a href="{{ route('admin.contracts.show', $contract) }}">{{ $contract->contract_no }}</a>
                            </td>
                            <td class="px-4 py-3">
                                <div class="font-bold text-gray-900">{{ $contract->contract_title }}</div>
                                <div class="text-xs text-gray-500 flex items-center gap-2">
                                    <span>{{ $contract->contract_type }}</span>
                                    @if($contract->exclusivity)
                                        <span class="px-1.5 py-0.5 rounded text-[10px] bg-red-50 text-red-700 border border-red-200 font-bold">Exclusive</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-3 text-xs">
                                @if($contract->celebrity)
                                    <div class="font-bold text-purple-700">{{ $contract->celebrity->stage_name }}</div>
                                @endif
                                @if($contract->client)
                                    <div class="text-gray-600">{{ $contract->client->company_name }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-xs text-gray-600">
                                @if($contract->project)
                                    <a href="{{ route('admin.projects.show', $contract->project) }}" class="text-indigo-600 hover:underline">
                                        {{ $contract->project->project_name }}
                                    </a>
                                @else
                                    <span class="text-gray-400">Direct / General</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-xs">
                                <div class="text-gray-700">{{ $contract->start_date?->format('d M Y') }} - {{ $contract->expiry_date?->format('d M Y') ?? 'Perpetual' }}</div>
                                @if($contract->expiry_date && $contract->expiry_date->isFuture() && $contract->expiry_date->diffInDays(now()) <= 30)
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-800">
                                        Expires in {{ round($contract->expiry_date->diffInDays(now())) }}d
                                    </span>
                                @elseif($contract->expiry_date && $contract->expiry_date->isPast())
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-gray-100 text-gray-600">
                                        Expired
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                @php
                                    $cStatus = [
                                        'active' => 'bg-emerald-100 text-emerald-800',
                                        'draft' => 'bg-gray-100 text-gray-800',
                                        'sent' => 'bg-blue-100 text-blue-800',
                                        'expired' => 'bg-amber-100 text-amber-800',
                                        'terminated' => 'bg-red-100 text-red-800',
                                    ];
                                @endphp
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $cStatus[$contract->status] ?? 'bg-gray-100' }}">
                                    {{ ucfirst($contract->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right space-x-2">
                                <a href="{{ route('admin.contracts.show', $contract) }}" class="text-indigo-600 hover:underline text-xs font-semibold">View</a>
                                <a href="{{ route('admin.contracts.edit', $contract) }}" class="text-gray-600 hover:underline text-xs">Edit</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-gray-500">No contracts found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-gray-100">
            {{ $contracts->links() }}
        </div>
    </div>
</div>
@endsection

