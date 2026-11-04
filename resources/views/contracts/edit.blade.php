@extends('layouts.app')

@section('title', 'Edit Contract: ' . $contract->contract_no)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Edit Contract: {{ $contract->contract_no }}</h1>
            <p class="text-sm text-gray-500 mt-1">Update legal clauses, validity period, and marketing channels</p>
        </div>
        <a href="{{ route('admin.contracts.show', $contract) }}" class="text-sm text-gray-600 hover:text-gray-900 flex items-center gap-1 font-medium">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back to Contract
        </a>
    </div>

    @if ($errors->any())
        <div class="p-4 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm">
            <p class="font-semibold mb-1">Please fix the following errors:</p>
            <ul class="list-disc list-inside space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.contracts.update', $contract) }}" class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 sm:p-8 space-y-6">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Title -->
            <div class="md:col-span-2">
                <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Contract Title *</label>
                <input type="text" name="contract_title" value="{{ old('contract_title', $contract->contract_title) }}" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <!-- Contract Type -->
            <div>
                <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Contract Type *</label>
                <select name="contract_type" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="Endorsement" {{ old('contract_type', $contract->contract_type) == 'Endorsement' ? 'selected' : '' }}>Endorsement Agreement</option>
                    <option value="Appearance" {{ old('contract_type', $contract->contract_type) == 'Appearance' ? 'selected' : '' }}>Live Appearance & Event Agreement</option>
                    <option value="Licence Agreement" {{ old('contract_type', $contract->contract_type) == 'Licence Agreement' ? 'selected' : '' }}>Licence & IP Rights Agreement</option>
                    <option value="NDA & Confidentiality" {{ old('contract_type', $contract->contract_type) == 'NDA & Confidentiality' ? 'selected' : '' }}>NDA & Confidentiality Agreement</option>
                    <option value="Social Media Influencer Agreement" {{ old('contract_type', $contract->contract_type) == 'Social Media Influencer Agreement' ? 'selected' : '' }}>Social Media Influencer Agreement</option>
                </select>
            </div>

            <!-- Status -->
            <div>
                <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Contract Status *</label>
                <select name="status" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="draft" {{ old('status', $contract->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="sent" {{ old('status', $contract->status) == 'sent' ? 'selected' : '' }}>Sent</option>
                    <option value="active" {{ old('status', $contract->status) == 'active' ? 'selected' : '' }}>Active / Executed</option>
                    <option value="expired" {{ old('status', $contract->status) == 'expired' ? 'selected' : '' }}>Expired</option>
                    <option value="terminated" {{ old('status', $contract->status) == 'terminated' ? 'selected' : '' }}>Terminated</option>
                </select>
            </div>

            <!-- Start Date & Expiry Date -->
            <div>
                <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Start Date *</label>
                <input type="date" name="start_date" value="{{ old('start_date', $contract->start_date?->format('Y-m-d')) }}" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Expiry Date</label>
                <input type="date" name="expiry_date" value="{{ old('expiry_date', $contract->expiry_date?->format('Y-m-d')) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <!-- Territory -->
            <div class="md:col-span-2">
                <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Territory</label>
                <input type="text" name="territory" value="{{ old('territory', $contract->territory) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <!-- Permitted Channels -->
            <div class="md:col-span-2">
                <label class="block text-xs font-semibold uppercase text-gray-600 mb-2">Permitted Marketing Channels</label>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                    @php
                        $channelOptions = ['Digital & Social Media', 'Television Commercials (TVC)', 'Print & Billboards (OOH)', 'OTT & Streaming', 'In-Store POSM', 'Public Relations & Events'];
                        $currentChannels = (array) ($contract->permitted_channels ?? []);
                    @endphp
                    @foreach($channelOptions as $ch)
                        <label class="flex items-center gap-2 text-xs text-gray-700 cursor-pointer">
                            <input type="checkbox" name="permitted_channels[]" value="{{ $ch }}" class="rounded text-indigo-600 focus:ring-indigo-500 h-4 w-4" {{ in_array($ch, $currentChannels) ? 'checked' : '' }}>
                            {{ $ch }}
                        </label>
                    @endforeach
                </div>
            </div>

            <!-- Exclusivity -->
            <div class="md:col-span-2 bg-gray-50 p-4 rounded-xl border border-gray-200">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="exclusivity" value="1" {{ old('exclusivity', $contract->exclusivity) ? 'checked' : '' }} class="rounded text-indigo-600 focus:ring-indigo-500 h-4 w-4">
                    <span class="text-xs font-bold text-gray-900">Category Exclusivity Clause Applies</span>
                </label>
            </div>

            <!-- Usage Rights Text -->
            <div class="md:col-span-2">
                <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Usage Rights & Specific Covenants</label>
                <textarea name="usage_rights" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">{{ old('usage_rights', $contract->usage_rights) }}</textarea>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-100">
            <a href="{{ route('admin.contracts.show', $contract) }}" class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50">Cancel</a>
            <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium shadow-sm transition">Save Changes</button>
        </div>
    </form>
</div>
@endsection

