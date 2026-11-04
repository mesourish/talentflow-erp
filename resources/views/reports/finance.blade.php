@extends('layouts.app')

@section('title', 'Financial Ledger & Performance Report')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Financial Reports & Ledger</h1>
            <p class="text-sm text-gray-500 mt-1">Agency collections, direct production expenses, commission retentions, and net profit</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.invoices.index') }}" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 rounded-lg text-xs font-semibold text-gray-700">View Invoices</a>
            <a href="{{ route('admin.commissions.index') }}" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 rounded-lg text-xs font-semibold text-gray-700">Commissions</a>
        </div>
    </div>

    <!-- Financial KPI Summary Cards -->
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <span class="text-xs font-semibold text-gray-400 uppercase">Total Invoiced</span>
            <p class="text-xl font-bold text-gray-900 mt-1">₹{{ number_format($totalInvoiced, 2) }}</p>
            <span class="text-[11px] text-gray-400">Gross client billings</span>
        </div>

        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <span class="text-xs font-semibold text-gray-400 uppercase">Cash Collected</span>
            <p class="text-xl font-bold text-emerald-600 mt-1">₹{{ number_format($totalReceived, 2) }}</p>
            <span class="text-[11px] text-gray-400">Actual bank receipts</span>
        </div>

        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <span class="text-xs font-semibold text-gray-400 uppercase">Direct Expenses</span>
            <p class="text-xl font-bold text-amber-600 mt-1">₹{{ number_format($totalExpenses, 2) }}</p>
            <span class="text-[11px] text-gray-400">Production & Claims</span>
        </div>

        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <span class="text-xs font-semibold text-gray-400 uppercase">Agency Commission</span>
            <p class="text-xl font-bold text-indigo-700 mt-1">₹{{ number_format($totalCommissions, 2) }}</p>
            <span class="text-[11px] text-gray-400">Retained 20% margin</span>
        </div>

        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <span class="text-xs font-semibold text-gray-400 uppercase">Net Agency Income</span>
            <p class="text-xl font-bold text-blue-600 mt-1">₹{{ number_format($netAgencyIncome, 2) }}</p>
            <span class="text-[11px] text-gray-400">Cash receipts minus costs</span>
        </div>
    </div>

    <!-- Chart: 6-Month Collections vs Expenses -->
    <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm space-y-4">
        <h3 class="text-base font-bold text-gray-900">Monthly Cash Inflow vs Expenses (Last 6 Months)</h3>
        <div style="height: 320px;">
            <canvas id="financeMonthlyChart"></canvas>
        </div>
    </div>

    <!-- Monthly Breakdown Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-base font-bold text-gray-900">Monthly Financial Statement</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-xs">
                <thead class="bg-gray-50 text-gray-500 uppercase font-semibold">
                    <tr>
                        <th class="px-4 py-3 text-left">Month</th>
                        <th class="px-4 py-3 text-right">Invoiced (₹)</th>
                        <th class="px-4 py-3 text-right">Collected (₹)</th>
                        <th class="px-4 py-3 text-right">Expenses (₹)</th>
                        <th class="px-4 py-3 text-right font-bold">Net Margin (₹)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($monthlyFinancials as $row)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-bold text-gray-900">{{ $row['month'] }}</td>
                            <td class="px-4 py-3 text-right text-gray-700">₹{{ number_format($row['invoiced'], 2) }}</td>
                            <td class="px-4 py-3 text-right font-semibold text-emerald-600">₹{{ number_format($row['received'], 2) }}</td>
                            <td class="px-4 py-3 text-right font-semibold text-amber-600">₹{{ number_format($row['expenses'], 2) }}</td>
                            <td class="px-4 py-3 text-right font-bold {{ $row['net'] >= 0 ? 'text-indigo-700' : 'text-red-600' }}">
                                ₹{{ number_format($row['net'], 2) }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('financeMonthlyChart').getContext('2d');
        const labels = @json(array_column($monthlyFinancials, 'month'));
        const receivedData = @json(array_column($monthlyFinancials, 'received'));
        const expenseData = @json(array_column($monthlyFinancials, 'expenses'));

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Cash Received (₹)',
                        data: receivedData,
                        backgroundColor: '#10b981',
                        borderRadius: 6
                    },
                    {
                        label: 'Direct Expenses (₹)',
                        data: expenseData,
                        backgroundColor: '#f59e0b',
                        borderRadius: 6
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '₹' + (value / 1000) + 'k';
                            }
                        }
                    }
                }
            }
        });
    });
</script>
@endsection

