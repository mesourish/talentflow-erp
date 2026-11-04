@extends('layouts.app')

@section('title', 'Reimbursements & Claims Analytics')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Reimbursements & Expenses Analytics</h1>
            <p class="text-sm text-gray-500 mt-1">Employee claim distributions, travel allowances, lodging costs, and approval ratios</p>
        </div>
        <a href="{{ route('admin.claims.index') }}" class="px-3.5 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg text-xs font-semibold text-gray-700">All Claims</a>
    </div>

    <!-- KPIs -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <span class="text-xs font-semibold text-gray-400 uppercase">Total Claims</span>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $totalClaims }}</p>
            <span class="text-[11px] text-gray-400">All submissions</span>
        </div>

        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <span class="text-xs font-semibold text-gray-400 uppercase">Approved Claims</span>
            <p class="text-2xl font-bold text-emerald-600 mt-1">{{ $approvedCount }}</p>
            <span class="text-[11px] text-gray-400">{{ $totalClaims > 0 ? round(($approvedCount / $totalClaims) * 100) : 0 }}% approval rate</span>
        </div>

        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <span class="text-xs font-semibold text-gray-400 uppercase">Gross Claimed</span>
            <p class="text-2xl font-bold text-gray-900 mt-1">₹{{ number_format($totalClaimAmount, 2) }}</p>
            <span class="text-[11px] text-gray-400">Total requested</span>
        </div>

        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <span class="text-xs font-semibold text-gray-400 uppercase">Disbursed / Approved</span>
            <p class="text-2xl font-bold text-blue-600 mt-1">₹{{ number_format($approvedAmount, 2) }}</p>
            <span class="text-[11px] text-gray-400">Posted to project costs</span>
        </div>
    </div>

    <!-- Category Breakdown Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-base font-bold text-gray-900">Expenses by Operational Category</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-xs">
                <thead class="bg-gray-50 text-gray-500 uppercase font-semibold">
                    <tr>
                        <th class="px-4 py-3 text-left">Category</th>
                        <th class="px-4 py-3 text-center">Receipt Count</th>
                        <th class="px-4 py-3 text-right">Total Amount (₹)</th>
                        <th class="px-4 py-3 text-right">Share of Claims</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($byCategory as $cat)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-bold text-gray-900 capitalize">{{ str_replace('_', ' ', $cat->expense_category) }}</td>
                            <td class="px-4 py-3 text-center text-gray-600">{{ $cat->count }}</td>
                            <td class="px-4 py-3 text-right font-bold text-gray-900">₹{{ number_format($cat->total, 2) }}</td>
                            <td class="px-4 py-3 text-right font-semibold text-indigo-700">
                                {{ $totalClaimAmount > 0 ? round(($cat->total / $totalClaimAmount) * 100, 1) : 0 }}%
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-gray-500">No claim items recorded.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

