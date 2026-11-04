@extends('layouts.app')

@section('title', 'Quotation Pipeline & Conversion Funnel')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Quotation Pipeline & Conversion</h1>
            <p class="text-sm text-gray-500 mt-1">Proposal generation pipeline, approval cycles, and conversion-to-project metrics</p>
        </div>
        <a href="{{ route('admin.quotations.index') }}" class="px-3.5 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg text-xs font-semibold text-gray-700">All Quotations</a>
    </div>

    <!-- Conversion KPIs -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
        <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
            <span class="text-xs font-semibold text-gray-400 uppercase">Conversion Rate</span>
            <p class="text-3xl font-bold text-emerald-600 mt-1">{{ $conversionRate }}%</p>
            <span class="text-xs text-gray-500">{{ $converted }} of {{ $total }} quotations converted to projects</span>
        </div>

        <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
            <span class="text-xs font-semibold text-gray-400 uppercase">Total Pipeline Value</span>
            <p class="text-3xl font-bold text-gray-900 mt-1">₹{{ number_format($totalQuoteValue, 2) }}</p>
            <span class="text-xs text-gray-500">Gross proposed quotation volume</span>
        </div>

        <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
            <span class="text-xs font-semibold text-gray-400 uppercase">Converted Project Value</span>
            <p class="text-3xl font-bold text-indigo-700 mt-1">₹{{ number_format($convertedValue, 2) }}</p>
            <span class="text-xs text-gray-500">Secured project revenue</span>
        </div>
    </div>

    <!-- Funnel Visualization -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-6">
        <h3 class="text-base font-bold text-gray-900">Quotation Funnel Breakdown</h3>

        <div class="space-y-4">
            <!-- Converted -->
            <div>
                <div class="flex justify-between text-xs font-semibold mb-1">
                    <span class="text-emerald-700">Converted to Active Projects</span>
                    <span class="text-gray-900">{{ $converted }} quotes ({{ $total > 0 ? round(($converted/$total)*100) : 0 }}%)</span>
                </div>
                <div class="w-full bg-gray-100 rounded-full h-3">
                    <div class="bg-emerald-500 h-3 rounded-full" style="width: {{ $total > 0 ? ($converted/$total)*100 : 0 }}%"></div>
                </div>
            </div>

            <!-- Approved Awaiting Conversion -->
            <div>
                <div class="flex justify-between text-xs font-semibold mb-1">
                    <span class="text-blue-700">Approved by Client (Awaiting Project Conversion)</span>
                    <span class="text-gray-900">{{ $approved }} quotes ({{ $total > 0 ? round(($approved/$total)*100) : 0 }}%)</span>
                </div>
                <div class="w-full bg-gray-100 rounded-full h-3">
                    <div class="bg-blue-500 h-3 rounded-full" style="width: {{ $total > 0 ? ($approved/$total)*100 : 0 }}%"></div>
                </div>
            </div>

            <!-- Sent to Client -->
            <div>
                <div class="flex justify-between text-xs font-semibold mb-1">
                    <span class="text-amber-700">Sent to Client (In Negotiation)</span>
                    <span class="text-gray-900">{{ $sent }} quotes ({{ $total > 0 ? round(($sent/$total)*100) : 0 }}%)</span>
                </div>
                <div class="w-full bg-gray-100 rounded-full h-3">
                    <div class="bg-amber-500 h-3 rounded-full" style="width: {{ $total > 0 ? ($sent/$total)*100 : 0 }}%"></div>
                </div>
            </div>

            <!-- Drafts -->
            <div>
                <div class="flex justify-between text-xs font-semibold mb-1">
                    <span class="text-gray-600">Drafts (In Preparation)</span>
                    <span class="text-gray-900">{{ $draft }} quotes ({{ $total > 0 ? round(($draft/$total)*100) : 0 }}%)</span>
                </div>
                <div class="w-full bg-gray-100 rounded-full h-3">
                    <div class="bg-gray-400 h-3 rounded-full" style="width: {{ $total > 0 ? ($draft/$total)*100 : 0 }}%"></div>
                </div>
            </div>

            <!-- Rejected -->
            <div>
                <div class="flex justify-between text-xs font-semibold mb-1">
                    <span class="text-red-700">Rejected / Not Proceeding</span>
                    <span class="text-gray-900">{{ $rejected }} quotes ({{ $total > 0 ? round(($rejected/$total)*100) : 0 }}%)</span>
                </div>
                <div class="w-full bg-gray-100 rounded-full h-3">
                    <div class="bg-red-500 h-3 rounded-full" style="width: {{ $total > 0 ? ($rejected/$total)*100 : 0 }}%"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

