@extends('layouts.app')

@section('title', 'Executive Revenue Forecasting & Projections')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Executive Revenue Forecasting</h1>
            <p class="text-sm text-gray-500 mt-1">Rolling 3-month predictive cash inflows, high-demand talent drivers, and project margin risks</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.reports.finance') }}" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 rounded-lg text-xs font-semibold text-gray-700">Financial Ledger</a>
            <a href="{{ route('admin.reports.projects') }}" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 rounded-lg text-xs font-semibold text-gray-700">Profitability Report</a>
        </div>
    </div>

    <!-- Forecast Methodology Banner -->
    <div class="bg-gradient-to-r from-indigo-700 to-purple-800 rounded-xl p-6 text-white shadow-md">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <span class="px-2.5 py-0.5 rounded text-[11px] font-bold bg-white/20 uppercase tracking-wider">Predictive Algorithm (Blueprint 14.1)</span>
                <h2 class="text-2xl font-bold mt-2">Next Month Projected Revenue: {{ $forecast['next_month_name'] }}</h2>
                <p class="text-sm text-indigo-100 mt-1 max-w-xl">
                    {{ $forecast['methodology'] }}. Calculated using rolling trailing 3-month actual bank payment receipts.
                </p>
            </div>
            <div class="text-right bg-white/10 backdrop-blur-sm p-4 rounded-xl border border-white/20 min-w-[200px]">
                <span class="text-xs text-indigo-200 block uppercase">Projected Inflow</span>
                <span class="text-3xl font-extrabold text-white block mt-1">₹{{ number_format($forecast['forecast_estimate'], 2) }}</span>
                <span class="text-[11px] text-indigo-200 block mt-1">Confidence: High (95% CI)</span>
            </div>
        </div>
    </div>

    <!-- Historical Baseline Inflows Grid -->
    <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm space-y-4">
        <h3 class="text-base font-bold text-gray-900">3-Month Historical Input Sequence</h3>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            @foreach($forecast['months'] as $index => $month)
                <div class="p-4 rounded-xl bg-gray-50 border border-gray-200">
                    <span class="text-xs font-semibold text-gray-500 uppercase">Period R{{ $index + 1 }} ({{ $month }})</span>
                    <p class="text-xl font-bold text-gray-900 mt-1">₹{{ number_format($forecast['historical_revenue'][$index] ?? 0, 2) }}</p>
                    <span class="text-[11px] text-emerald-600 font-semibold">Actual Collected</span>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Drivers & Risks Two-Column Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Top Talent Driving Revenue -->
        <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-base font-bold text-gray-900">Talent Driving Next Quarter</h3>
                    <p class="text-xs text-gray-500">Highest grossing and highest frequency bookings</p>
                </div>
                <a href="{{ route('admin.reports.celebrities') }}" class="text-xs text-indigo-600 hover:underline font-semibold">All Talent &rarr;</a>
            </div>

            <div class="divide-y divide-gray-100 text-xs">
                @foreach(array_slice($celebrityDemand, 0, 5) as $celeb)
                    <div class="py-3 flex items-center justify-between">
                        <div>
                            <span class="font-bold text-gray-900 text-sm block">{{ $celeb['name'] }}</span>
                            <span class="text-gray-400 font-mono">{{ $celeb['code'] }} • {{ $celeb['bookings_count'] }} bookings</span>
                        </div>
                        <div class="text-right">
                            <span class="font-bold text-emerald-600 block">₹{{ number_format($celeb['total_earnings'], 2) }}</span>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $celeb['demand'] === 'High' ? 'bg-emerald-100 text-emerald-800' : 'bg-blue-100 text-blue-800' }}">
                                {{ $celeb['demand'] }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Margin Risks & Vulnerable Campaigns -->
        <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-base font-bold text-gray-900">Campaigns with Margin Risk</h3>
                    <p class="text-xs text-gray-500">Projects operating below 15% net profit threshold</p>
                </div>
                <a href="{{ route('admin.reports.projects') }}" class="text-xs text-indigo-600 hover:underline font-semibold">View Profitability &rarr;</a>
            </div>

            @php
                $riskProjects = array_filter($projectProfitability, fn($p) => $p['health'] === 'Margin Warning');
            @endphp

            <div class="divide-y divide-gray-100 text-xs">
                @forelse(array_slice($riskProjects, 0, 5) as $proj)
                    <div class="py-3 flex items-center justify-between">
                        <div>
                            <a href="{{ route('admin.projects.show', $proj['id']) }}" class="font-bold text-gray-900 hover:text-indigo-600 block">
                                {{ $proj['name'] }}
                            </a>
                            <span class="text-gray-400 font-mono">{{ $proj['code'] }} • Client: {{ $proj['client'] }}</span>
                        </div>
                        <div class="text-right">
                            <span class="font-bold text-amber-600 block text-sm">{{ $proj['margin_percent'] }}% Margin</span>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-800 uppercase">
                                Action Required
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="py-8 text-center text-gray-400">
                        No projects currently below the 15% margin threshold. All campaigns operating profitably!
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

