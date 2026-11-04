@extends('layouts.app')

@section('title', 'Celebrity Demand & Performance Metrics')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Celebrity Demand & Payouts</h1>
            <p class="text-sm text-gray-500 mt-1">Talent booking frequencies, gross fee earnings, and artist demand ranking</p>
        </div>
        <a href="{{ route('admin.celebrities.index') }}" class="px-3.5 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg text-xs font-semibold text-gray-700">Talent Roster</a>
    </div>

    <!-- Demand Indicators Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-base font-bold text-gray-900">Celebrity Demand Index</h3>
            <span class="text-xs text-gray-500">Ranked by booking volume and client demand</span>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-xs">
                <thead class="bg-gray-50 text-gray-500 uppercase font-semibold">
                    <tr>
                        <th class="px-4 py-3 text-left">Celebrity / Artist</th>
                        <th class="px-4 py-3 text-center">Confirmed Bookings</th>
                        <th class="px-4 py-3 text-right">Total Earnings (Net ₹)</th>
                        <th class="px-4 py-3 text-center">Demand Ranking</th>
                        <th class="px-4 py-3 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($demandMetrics as $celeb)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">
                                <a href="{{ route('admin.celebrities.show', $celeb['id']) }}" class="font-bold text-gray-900 hover:text-indigo-600 block">
                                    {{ $celeb['name'] }}
                                </a>
                                <span class="font-mono text-gray-400 text-[11px]">{{ $celeb['code'] }}</span>
                            </td>
                            <td class="px-4 py-3 text-center font-bold text-gray-800 text-sm">
                                {{ $celeb['bookings_count'] }}
                            </td>
                            <td class="px-4 py-3 text-right font-bold text-emerald-600 text-sm">
                                ₹{{ number_format($celeb['total_earnings'], 2) }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($celeb['demand'] === 'High')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200 uppercase">
                                        🔥 High Demand
                                    </span>
                                @elseif($celeb['demand'] === 'Stable')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-blue-100 text-blue-800 border border-blue-200 uppercase">
                                        ⚡ Stable
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-gray-100 text-gray-700 uppercase">
                                        Developing
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('admin.celebrities.show', $celeb['id']) }}" class="text-indigo-600 hover:underline font-semibold">View Profile &rarr;</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-gray-500">No celebrity demand metrics found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

