@extends('layouts.app')

@section('title', 'Client Invoices & Billing')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Invoices & Billing</h1>
            <p class="text-sm text-gray-500 mt-1">Tax invoices, partial payments tracking, and accounts receivable</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.invoices.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Raise New Invoice
            </a>
        </div>
    </div>

    <!-- Financial KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
        <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
            <span class="text-xs font-semibold text-gray-400 uppercase">Total Invoiced</span>
            <p class="text-2xl font-bold text-gray-900 mt-1">₹{{ number_format($totalInvoiced, 2) }}</p>
            <span class="text-xs text-gray-500">All non-cancelled invoices</span>
        </div>

        <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
            <span class="text-xs font-semibold text-gray-400 uppercase">Total Collected</span>
            <p class="text-2xl font-bold text-emerald-600 mt-1">₹{{ number_format($totalCollected, 2) }}</p>
            <span class="text-xs text-gray-500">{{ $totalInvoiced > 0 ? round(($totalCollected / $totalInvoiced) * 100) : 0 }}% recovery rate</span>
        </div>

        <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
            <span class="text-xs font-semibold text-gray-400 uppercase">Outstanding Receivables</span>
            <p class="text-2xl font-bold text-amber-600 mt-1">₹{{ number_format($totalOutstanding, 2) }}</p>
            <span class="text-xs text-gray-500">Pending client remittances</span>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
        <form method="GET" action="{{ route('admin.invoices.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-4">
            <div class="sm:col-span-2">
                <label class="block text-xs font-semibold uppercase text-gray-500 mb-1">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by invoice # or client name..." class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <div class="sm:col-span-2">
                <label class="block text-xs font-semibold uppercase text-gray-500 mb-1">Status</label>
                <div class="flex gap-2">
                    <select name="status" class="w-full py-2 px-3 border border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">All Statuses</option>
                        @foreach($statuses as $st)
                            <option value="{{ $st }}" {{ request('status') === $st ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $st)) }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg">Filter</button>
                    @if(request()->hasAny(['search', 'status']))
                        <a href="{{ route('admin.invoices.index') }}" class="px-3 py-2 bg-red-50 hover:bg-red-100 text-red-600 text-sm font-medium rounded-lg flex items-center">Reset</a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <!-- Invoices Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50 text-gray-500 uppercase font-semibold text-xs">
                    <tr>
                        <th class="px-4 py-3 text-left">Invoice #</th>
                        <th class="px-4 py-3 text-left">Client & Project</th>
                        <th class="px-4 py-3 text-left">Date & Due</th>
                        <th class="px-4 py-3 text-right">Grand Total</th>
                        <th class="px-4 py-3 text-right">Paid</th>
                        <th class="px-4 py-3 text-right">Balance Due</th>
                        <th class="px-4 py-3 text-center">Status</th>
                        <th class="px-4 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($invoices as $inv)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-mono text-xs font-bold text-indigo-600">
                                <a href="{{ route('admin.invoices.show', $inv) }}">{{ $inv->invoice_no }}</a>
                            </td>
                            <td class="px-4 py-3">
                                <div class="font-bold text-gray-900">{{ $inv->client?->company_name }}</div>
                                <div class="text-xs text-gray-500">
                                    {{ $inv->project ? $inv->project->project_name : 'Direct Billing' }}
                                </div>
                            </td>
                            <td class="px-4 py-3 text-xs text-gray-600 whitespace-nowrap">
                                <div>{{ $inv->invoice_date?->format('d M Y') }}</div>
                                <div class="text-gray-400">Due: {{ $inv->due_date?->format('d M Y') }}</div>
                            </td>
                            <td class="px-4 py-3 text-right font-bold text-gray-900">₹{{ number_format($inv->grand_total, 2) }}</td>
                            <td class="px-4 py-3 text-right font-semibold text-emerald-600">₹{{ number_format($inv->amount_paid, 2) }}</td>
                            <td class="px-4 py-3 text-right font-bold {{ $inv->balance_due > 0 ? 'text-red-600' : 'text-gray-400' }}">
                                ₹{{ number_format($inv->balance_due, 2) }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                @php
                                    $invBadge = [
                                        'paid' => 'bg-emerald-100 text-emerald-800',
                                        'partially_paid' => 'bg-amber-100 text-amber-800',
                                        'issued' => 'bg-blue-100 text-blue-800',
                                        'unpaid' => 'bg-amber-100 text-amber-800',
                                        'overdue' => 'bg-red-100 text-red-800',
                                        'draft' => 'bg-gray-100 text-gray-800',
                                        'cancelled' => 'bg-gray-200 text-gray-700',
                                    ];
                                @endphp
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold uppercase {{ $invBadge[$inv->status] ?? 'bg-gray-100' }}">
                                    {{ str_replace('_', ' ', $inv->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right space-x-2">
                                <a href="{{ route('admin.invoices.show', $inv) }}" class="text-indigo-600 hover:underline text-xs font-semibold">View</a>
                                <a href="{{ route('admin.invoices.pdf', $inv) }}" target="_blank" class="text-gray-600 hover:underline text-xs">PDF</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-8 text-center text-gray-500">No invoices found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-gray-100">
            {{ $invoices->links() }}
        </div>
    </div>
</div>
@endsection

