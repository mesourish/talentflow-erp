@extends('layouts.app')

@section('title', 'Reimbursement Claims')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Reimbursement Claims</h1>
            <p class="text-sm text-gray-500 mt-1">Employee travel, shoot logistics, meals, and project expense claims</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.claims.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                New Expense Claim
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
        <form method="GET" action="{{ route('admin.claims.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-4">
            <div class="sm:col-span-2">
                <label class="block text-xs font-semibold uppercase text-gray-500 mb-1">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by claim # or description..." class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <div class="sm:col-span-2">
                <label class="block text-xs font-semibold uppercase text-gray-500 mb-1">Status</label>
                <div class="flex gap-2">
                    <select name="status" class="w-full py-2 px-3 border border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">All Statuses</option>
                        @foreach($statuses as $st)
                            <option value="{{ $st }}" {{ request('status') === $st ? 'selected' : '' }}>{{ ucfirst($st) }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg">Filter</button>
                    @if(request()->hasAny(['search', 'status']))
                        <a href="{{ route('admin.claims.index') }}" class="px-3 py-2 bg-red-50 hover:bg-red-100 text-red-600 text-sm font-medium rounded-lg flex items-center">Reset</a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <!-- Claims Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50 text-gray-500 uppercase font-semibold text-xs">
                    <tr>
                        <th class="px-4 py-3 text-left">Claim #</th>
                        <th class="px-4 py-3 text-left">Submitted By</th>
                        <th class="px-4 py-3 text-left">Project</th>
                        <th class="px-4 py-3 text-left">Date</th>
                        <th class="px-4 py-3 text-right">Total Amount</th>
                        <th class="px-4 py-3 text-center">Status</th>
                        <th class="px-4 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($claims as $claim)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-mono text-xs font-bold text-indigo-600">
                                <a href="{{ route('admin.claims.show', $claim) }}">{{ $claim->claim_no }}</a>
                            </td>
                            <td class="px-4 py-3 text-xs text-gray-900 font-medium">
                                {{ $claim->submitter?->name ?? 'Employee' }}
                            </td>
                            <td class="px-4 py-3 text-xs text-gray-600">
                                <div>{{ $claim->project?->project_name }}</div>
                                <div class="text-gray-400 font-mono">{{ $claim->project?->project_code }}</div>
                            </td>
                            <td class="px-4 py-3 text-xs text-gray-500">{{ $claim->claim_date?->format('d M Y') }}</td>
                            <td class="px-4 py-3 text-right font-bold text-gray-900">₹{{ number_format($claim->total_amount, 2) }}</td>
                            <td class="px-4 py-3 text-center">
                                @php
                                    $clStatus = [
                                        'approved' => 'bg-emerald-100 text-emerald-800',
                                        'paid' => 'bg-emerald-100 text-emerald-800',
                                        'pending' => 'bg-amber-100 text-amber-800',
                                        'submitted' => 'bg-blue-100 text-blue-800',
                                        'draft' => 'bg-gray-100 text-gray-800',
                                        'rejected' => 'bg-red-100 text-red-800',
                                    ];
                                @endphp
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $clStatus[$claim->status] ?? 'bg-gray-100' }}">
                                    {{ ucfirst($claim->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right space-x-2">
                                <a href="{{ route('admin.claims.show', $claim) }}" class="text-indigo-600 hover:underline text-xs font-semibold">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-gray-500">No reimbursement claims found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-gray-100">
            {{ $claims->links() }}
        </div>
    </div>
</div>
@endsection

