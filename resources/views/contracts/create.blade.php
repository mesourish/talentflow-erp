@extends('layouts.app')

@section('title', 'Register New Contract')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Register New Contract</h1>
            <p class="text-sm text-gray-500 mt-1">Register legal agreements, talent endorsements, NDAs, and licensing rights</p>
        </div>
        <a href="{{ route('admin.contracts.index') }}" class="text-sm text-gray-600 hover:text-gray-900 flex items-center gap-1 font-medium">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back to Contracts
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

    <form method="POST" action="{{ route('admin.contracts.store') }}" enctype="multipart/form-data" class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 sm:p-8 space-y-6">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Contract Number -->
            <div>
                <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Contract Number *</label>
                <input type="text" name="contract_no" value="{{ old('contract_no', $suggestedNo) }}" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm font-mono bg-gray-50 focus:bg-white focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <!-- Contract Type -->
            <div>
                <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Contract Type *</label>
                <select name="contract_type" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="Endorsement" {{ old('contract_type') == 'Endorsement' ? 'selected' : '' }}>Endorsement Agreement</option>
                    <option value="Appearance" {{ old('contract_type') == 'Appearance' ? 'selected' : '' }}>Live Appearance & Event Agreement</option>
                    <option value="Licence Agreement" {{ old('contract_type') == 'Licence Agreement' ? 'selected' : '' }}>Licence & IP Rights Agreement</option>
                    <option value="NDA & Confidentiality" {{ old('contract_type') == 'NDA & Confidentiality' ? 'selected' : '' }}>NDA & Confidentiality Agreement</option>
                    <option value="Social Media Influencer Agreement" {{ old('contract_type') == 'Social Media Influencer Agreement' ? 'selected' : '' }}>Social Media Influencer Agreement</option>
                </select>
            </div>

            <!-- Title -->
            <div class="md:col-span-2">
                <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Contract Title *</label>
                <input type="text" name="contract_title" value="{{ old('contract_title') }}" placeholder="e.g. Aanya Sen x ABC Fashion Brand Ambassador Agreement 2026" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <!-- Project Relation -->
            <div class="md:col-span-2">
                <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Associated Project</label>
                <select name="project_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">None / Standalone Contract</option>
                    @foreach($projects as $proj)
                        <option value="{{ $proj->id }}" {{ (old('project_id') == $proj->id || request('project_id') == $proj->id) ? 'selected' : '' }}>
                            {{ $proj->project_name }} ({{ $proj->project_code }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Client & Celebrity Parties -->
            <div>
                <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Client / Brand</label>
                <select name="client_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Select Client...</option>
                    @foreach($clients as $client)
                        <option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>
                            {{ $client->company_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Celebrity / Artist</label>
                <select name="celebrity_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Select Celebrity...</option>
                    @foreach($celebrities as $celeb)
                        <option value="{{ $celeb->id }}" {{ old('celebrity_id') == $celeb->id ? 'selected' : '' }}>
                            {{ $celeb->stage_name }} ({{ $celeb->celebrity_code }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Start Date & Expiry Date -->
            <div>
                <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Start Date *</label>
                <input type="date" name="start_date" value="{{ old('start_date', date('Y-m-d')) }}" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Expiry Date (Leave empty if perpetual)</label>
                <input type="date" name="expiry_date" value="{{ old('expiry_date', date('Y-m-d', strtotime('+1 year'))) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <!-- Territory & Status -->
            <div>
                <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Territory</label>
                <input type="text" name="territory" value="{{ old('territory', 'India (PAN-India)') }}" placeholder="e.g. India, Worldwide, PAN-Asia" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Contract Status *</label>
                <select name="status" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="sent" {{ old('status') == 'sent' ? 'selected' : '' }}>Sent to Signatories</option>
                    <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Active / Executed</option>
                </select>
            </div>

            <!-- Permitted Channels -->
            <div class="md:col-span-2">
                <label class="block text-xs font-semibold uppercase text-gray-600 mb-2">Permitted Marketing Channels</label>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                    @php
                        $channelOptions = ['Digital & Social Media', 'Television Commercials (TVC)', 'Print & Billboards (OOH)', 'OTT & Streaming', 'In-Store POSM', 'Public Relations & Events'];
                    @endphp
                    @foreach($channelOptions as $ch)
                        <label class="flex items-center gap-2 text-xs text-gray-700 cursor-pointer">
                            <input type="checkbox" name="permitted_channels[]" value="{{ $ch }}" class="rounded text-indigo-600 focus:ring-indigo-500 h-4 w-4" checked>
                            {{ $ch }}
                        </label>
                    @endforeach
                </div>
            </div>

            <!-- Exclusivity -->
            <div class="md:col-span-2 bg-gray-50 p-4 rounded-xl border border-gray-200">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="exclusivity" value="1" {{ old('exclusivity') ? 'checked' : '' }} class="rounded text-indigo-600 focus:ring-indigo-500 h-4 w-4">
                    <span class="text-xs font-bold text-gray-900">Category Exclusivity Clause Applies</span>
                </label>
                <p class="text-[11px] text-gray-500 mt-1 ml-6">Talent cannot endorse or represent competing brands in the same product category during the contract period.</p>
            </div>

            <!-- Usage Rights Text -->
            <div class="md:col-span-2">
                <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Usage Rights & Specific Covenants</label>
                <textarea name="usage_rights" rows="3" placeholder="Key clauses, deliverable counts, social media post quotas, approval rights..." class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">{{ old('usage_rights') }}</textarea>
            </div>

            <!-- File Upload -->
            <div class="md:col-span-2">
                <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Executed Contract PDF / Agreement Scan</label>
                <input type="file" name="file" accept=".pdf,.doc,.docx" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                <p class="text-[11px] text-gray-400 mt-1">Supported formats: PDF, DOCX (Max 20MB). Encrypted & logged securely in DMS.</p>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-100">
            <a href="{{ route('admin.contracts.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50">Cancel</a>
            <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium shadow-sm transition">Register Contract</button>
        </div>
    </form>
</div>
@endsection

