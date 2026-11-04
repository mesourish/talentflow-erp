@extends('layouts.app')

@section('title', 'Contract: ' . $contract->contract_no)

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-xl shadow-sm border border-gray-200">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 rounded text-xs font-mono font-bold bg-gray-100 text-gray-800">{{ $contract->contract_no }}</span>
                @php
                    $cStatus = [
                        'active' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                        'draft' => 'bg-gray-100 text-gray-800 border-gray-200',
                        'sent' => 'bg-blue-100 text-blue-800 border-blue-200',
                        'expired' => 'bg-amber-100 text-amber-800 border-amber-200',
                        'terminated' => 'bg-red-100 text-red-800 border-red-200',
                    ];
                @endphp
                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold border {{ $cStatus[$contract->status] ?? 'bg-gray-100' }}">
                    {{ ucfirst($contract->status) }}
                </span>
                <span class="px-2 py-0.5 rounded text-xs font-semibold bg-indigo-50 text-indigo-700 uppercase">
                    {{ $contract->contract_type }}
                </span>
            </div>
            <h1 class="text-2xl font-bold text-gray-900 mt-2">{{ $contract->contract_title }}</h1>
            <p class="text-xs text-gray-500 mt-1">
                Registered on {{ $contract->created_at?->format('d M Y') }} by {{ $contract->creator?->name ?? 'System' }}
            </p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('admin.contracts.edit', $contract) }}" class="px-3.5 py-2 border border-gray-300 rounded-lg text-xs font-medium text-gray-700 hover:bg-gray-50 flex items-center gap-1.5">
                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                Edit Contract
            </a>
            <a href="{{ route('admin.contracts.index') }}" class="px-3.5 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg text-xs font-medium text-gray-700">Back</a>
        </div>
    </div>

    <!-- Expiring Soon Banner if <= 30 days -->
    @if($contract->expiry_date && $contract->expiry_date->isFuture() && $contract->expiry_date->diffInDays(now()) <= 30)
        <div class="p-4 bg-amber-50 border border-amber-300 rounded-xl flex items-center gap-3">
            <svg class="w-6 h-6 text-amber-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            <div>
                <p class="text-sm font-bold text-amber-800">Contract Expiry Warning</p>
                <p class="text-xs text-amber-700">This agreement will expire in {{ round($contract->expiry_date->diffInDays(now())) }} days (on {{ $contract->expiry_date->format('d M Y') }}). Please initiate renewal discussions or renegotiate usage extension terms.</p>
            </div>
        </div>
    @endif

    <!-- Parties & Project Link -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Client Party -->
        <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
            <span class="text-xs font-semibold uppercase text-gray-400 block mb-2">First Party (Client / Brand)</span>
            @if($contract->client)
                <p class="font-bold text-gray-900 text-base">{{ $contract->client->company_name }}</p>
                <p class="text-xs text-gray-500 font-mono mt-0.5">{{ $contract->client->client_code }} • {{ ucfirst($contract->client->client_type) }}</p>
                <div class="mt-3">
                    <a href="{{ route('admin.clients.show', $contract->client) }}" class="text-xs text-indigo-600 hover:underline font-semibold">Client 360 &rarr;</a>
                </div>
            @else
                <p class="text-sm text-gray-500 italic">No specific client mapped</p>
            @endif
        </div>

        <!-- Talent Party -->
        <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
            <span class="text-xs font-semibold uppercase text-gray-400 block mb-2">Second Party (Celebrity / Artist)</span>
            @if($contract->celebrity)
                <p class="font-bold text-purple-700 text-base">{{ $contract->celebrity->stage_name }}</p>
                <p class="text-xs text-gray-500 font-mono mt-0.5">{{ $contract->celebrity->celebrity_code }} • {{ $contract->celebrity->talentCategory?->name }}</p>
                <div class="mt-3">
                    <a href="{{ route('admin.celebrities.show', $contract->celebrity) }}" class="text-xs text-purple-600 hover:underline font-semibold">Celebrity Profile &rarr;</a>
                </div>
            @else
                <p class="text-sm text-gray-500 italic">No specific celebrity mapped</p>
            @endif
        </div>

        <!-- Project Link -->
        <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
            <span class="text-xs font-semibold uppercase text-gray-400 block mb-2">Attached Project</span>
            @if($contract->project)
                <p class="font-bold text-gray-900 text-base">{{ $contract->project->project_name }}</p>
                <p class="text-xs text-gray-500 font-mono mt-0.5">{{ $contract->project->project_code }}</p>
                <div class="mt-3">
                    <a href="{{ route('admin.projects.show', $contract->project) }}" class="text-xs text-indigo-600 hover:underline font-semibold">Open Project &rarr;</a>
                </div>
            @else
                <p class="text-sm text-gray-500 italic">Standalone Agency Contract</p>
            @endif
        </div>
    </div>

    <!-- Contract Terms & Conditions -->
    <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm space-y-6">
        <h3 class="text-sm font-bold uppercase tracking-wider text-gray-500 border-b pb-3">Legal Terms & Usage Rights</h3>

        <div class="grid grid-cols-2 sm:grid-cols-4 gap-6 text-xs">
            <div>
                <span class="text-gray-400 block uppercase">Start Date</span>
                <span class="font-bold text-gray-900 text-sm">{{ $contract->start_date?->format('d M Y') }}</span>
            </div>
            <div>
                <span class="text-gray-400 block uppercase">Expiry Date</span>
                <span class="font-bold text-gray-900 text-sm">{{ $contract->expiry_date?->format('d M Y') ?? 'Perpetual' }}</span>
            </div>
            <div>
                <span class="text-gray-400 block uppercase">Territory</span>
                <span class="font-bold text-gray-900 text-sm">{{ $contract->territory ?? 'India' }}</span>
            </div>
            <div>
                <span class="text-gray-400 block uppercase">Exclusivity</span>
                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold {{ $contract->exclusivity ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-700' }}">
                    {{ $contract->exclusivity ? 'Exclusive' : 'Non-Exclusive' }}
                </span>
            </div>
        </div>

        <!-- Channels -->
        @if($contract->permitted_channels && count($contract->permitted_channels) > 0)
            <div>
                <span class="text-xs font-semibold text-gray-500 uppercase block mb-2">Permitted Marketing Channels:</span>
                <div class="flex flex-wrap gap-2">
                    @foreach($contract->permitted_channels as $ch)
                        <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-blue-50 text-blue-700 border border-blue-200">
                            {{ $ch }}
                        </span>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Usage Rights Details -->
        <div>
            <span class="text-xs font-semibold text-gray-500 uppercase block mb-1">Covenants, Exclusions & Usage Scope:</span>
            <div class="p-4 bg-gray-50 rounded-xl text-xs text-gray-700 leading-relaxed border border-gray-100 whitespace-pre-line">
                {{ $contract->usage_rights ?: 'No specific usage constraints recorded. Standard agency terms apply.' }}
            </div>
        </div>
    </div>

    <!-- ATTACHED DOCUMENTS & DMS INTEGRATION -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-base font-bold text-gray-900">Secure Document Repository</h3>
                <p class="text-xs text-gray-500">Encrypted legal documents, executed PDFs, and version history</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-xs">
                <thead class="bg-gray-50 text-gray-500 uppercase font-semibold">
                    <tr>
                        <th class="px-4 py-3 text-left">Document File</th>
                        <th class="px-4 py-3 text-left">Version</th>
                        <th class="px-4 py-3 text-left">Category</th>
                        <th class="px-4 py-3 text-left">Size</th>
                        <th class="px-4 py-3 text-left">Upload Date</th>
                        <th class="px-4 py-3 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($contract->documents as $doc)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-semibold text-gray-900 flex items-center gap-2">
                                <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                <span>{{ $doc->original_name }}</span>
                                @if($doc->is_confidential)
                                    <span class="px-1.5 py-0.5 rounded text-[9px] bg-red-100 text-red-700 font-bold uppercase">Confidential</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-gray-600 font-mono">v{{ $doc->version ?? '1.0' }}</td>
                            <td class="px-4 py-3 text-gray-600 capitalize">{{ $doc->category }}</td>
                            <td class="px-4 py-3 text-gray-500">{{ round(($doc->file_size ?? 0) / 1024) }} KB</td>
                            <td class="px-4 py-3 text-gray-500">{{ $doc->created_at?->format('d M Y, h:i A') }}</td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('admin.documents.download', $doc) }}" class="inline-flex items-center px-3 py-1 rounded bg-indigo-50 text-indigo-700 hover:bg-indigo-100 font-semibold text-xs">
                                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                    Download
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-6 text-center text-gray-400">No document files attached yet to this contract.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

