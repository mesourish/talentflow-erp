@extends('layouts.app')

@section('title', 'Project Profitability & Margin Analysis')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Project Profitability & Margins</h1>
            <p class="text-sm text-gray-500 mt-1">Gross margin tracking, talent costs, production expenses, and &lt;15% margin risk detection</p>
        </div>
        <a href="{{ route('admin.projects.index') }}" class="px-3.5 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg text-xs font-semibold text-gray-700">All Projects</a>
    </div>

    <!-- Alert / Explanation Banner -->
    <div class="p-4 bg-indigo-50 border border-indigo-200 rounded-xl text-xs text-indigo-900 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <svg class="w-5 h-5 text-indigo-600 flex-shrink-0" width="20" height="20" style="width: 20px; height: 20px; min-width: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <div>
                <strong class="font-bold">Agency Profitability Rule:</strong> Projects with profit margins below 15% are flagged with an automated <span class="px-1.5 py-0.5 rounded bg-red-100 text-red-800 font-bold">Margin Warning</span> to prompt scope adjustment or renegotiation.
            </div>
        </div>
    </div>

    <!-- Profitability Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-xs">
                <thead class="bg-gray-50 text-gray-500 uppercase font-semibold">
                    <tr>
                        <th class="px-4 py-3 text-left">Project</th>
                        <th class="px-4 py-3 text-left">Client</th>
                        <th class="px-4 py-3 text-right">Invoiced (₹)</th>
                        <th class="px-4 py-3 text-right">Production Exp (₹)</th>
                        <th class="px-4 py-3 text-right">Talent Fees (₹)</th>
                        <th class="px-4 py-3 text-right">Total Cost (₹)</th>
                        <th class="px-4 py-3 text-right font-bold">Net Profit (₹)</th>
                        <th class="px-4 py-3 text-right font-bold">Margin %</th>
                        <th class="px-4 py-3 text-center">Health</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($projectProfitability as $row)
                        <tr class="hover:bg-gray-50 {{ $row['health'] === 'Margin Warning' ? 'bg-amber-50/20' : '' }}">
                            <td class="px-4 py-3">
                                <a href="{{ route('admin.projects.show', $row['id']) }}" class="font-bold text-gray-900 hover:text-indigo-600 block">
                                    {{ $row['name'] }}
                                </a>
                                <span class="font-mono text-gray-400 text-[11px]">{{ $row['code'] }}</span>
                            </td>
                            <td class="px-4 py-3 text-gray-600">{{ $row['client'] }}</td>
                            <td class="px-4 py-3 text-right font-semibold text-gray-900">₹{{ number_format($row['invoiced'], 2) }}</td>
                            <td class="px-4 py-3 text-right text-amber-600">₹{{ number_format($row['expenses'], 2) }}</td>
                            <td class="px-4 py-3 text-right text-purple-600">₹{{ number_format($row['payouts'], 2) }}</td>
                            <td class="px-4 py-3 text-right text-gray-700">₹{{ number_format($row['total_costs'], 2) }}</td>
                            <td class="px-4 py-3 text-right font-bold {{ $row['profit'] >= 0 ? 'text-emerald-600' : 'text-red-600' }}">
                                ₹{{ number_format($row['profit'], 2) }}
                            </td>
                            <td class="px-4 py-3 text-right font-bold text-sm {{ $row['margin_percent'] < 15 ? 'text-amber-600' : 'text-emerald-700' }}">
                                {{ $row['margin_percent'] }}%
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($row['health'] === 'Healthy')
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                        Healthy
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-300">
                                        Margin Warning (&lt;15%)
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-4 py-8 text-center text-gray-500">No project profitability data available.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

