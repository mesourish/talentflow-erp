@extends('layouts.app')

@section('title', 'Create Project')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Create New Project</h1>
            <p class="text-sm text-gray-500 mt-1">Register a new client campaign, event, or celebrity endorsement</p>
        </div>
        <a href="{{ route('admin.projects.index') }}" class="text-sm text-gray-600 hover:text-gray-900 flex items-center gap-1 font-medium">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back to Projects
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

    <form method="POST" action="{{ route('admin.projects.store') }}" class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 sm:p-8 space-y-6">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Project Code -->
            <div>
                <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Project Code *</label>
                <input type="text" name="project_code" value="{{ old('project_code', $suggestedCode) }}" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm font-mono bg-gray-50 focus:bg-white focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <!-- Client -->
            <div>
                <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Client *</label>
                <select name="client_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Select Client...</option>
                    @foreach($clients as $client)
                        <option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>
                            {{ $client->company_name }} ({{ $client->client_code }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Project Name -->
            <div class="md:col-span-2">
                <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Project Name *</label>
                <input type="text" name="project_name" value="{{ old('project_name') }}" placeholder="e.g. Festive Campaign 2026 / Brand Ambassador Launch" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <!-- Project Type -->
            <div>
                <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Project Type *</label>
                <select name="project_type" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="campaign" {{ old('project_type') == 'campaign' ? 'selected' : '' }}>Campaign</option>
                    <option value="event" {{ old('project_type') == 'event' ? 'selected' : '' }}>Event / Gala</option>
                    <option value="shoot" {{ old('project_type') == 'shoot' ? 'selected' : '' }}>Photo / Video Shoot</option>
                    <option value="endorsement" {{ old('project_type') == 'endorsement' ? 'selected' : '' }}>Brand Endorsement</option>
                </select>
            </div>

            <!-- Project Manager -->
            <div>
                <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Project Manager</label>
                <select name="project_manager_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Assign Later</option>
                    @foreach($managers as $manager)
                        <option value="{{ $manager->id }}" {{ old('project_manager_id') == $manager->id ? 'selected' : '' }}>
                            {{ $manager->name }} ({{ $manager->department?->name ?? 'General' }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Start Date -->
            <div>
                <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Start Date *</label>
                <input type="date" name="start_date" value="{{ old('start_date', date('Y-m-d')) }}" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <!-- End Date -->
            <div>
                <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">End Date *</label>
                <input type="date" name="end_date" value="{{ old('end_date', date('Y-m-d', strtotime('+30 days'))) }}" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <!-- Budget Amount -->
            <div>
                <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Budget Amount *</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500 text-sm">₹</span>
                    <input type="number" step="0.01" name="budget_amount" value="{{ old('budget_amount', '500000') }}" required class="w-full pl-8 pr-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>
            </div>

            <!-- Currency & Status -->
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Currency</label>
                    <input type="text" name="currency" value="{{ old('currency', 'INR') }}" readonly class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm bg-gray-50 text-gray-700">
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Status</label>
                    <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="planned" {{ old('status') == 'planned' ? 'selected' : '' }}>Planned</option>
                        <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="on_hold" {{ old('status') == 'on_hold' ? 'selected' : '' }}>On Hold</option>
                        <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    </select>
                </div>
            </div>

            <!-- Description -->
            <div class="md:col-span-2">
                <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Description & Scope of Work</label>
                <textarea name="description" rows="4" placeholder="Brief project background, objectives, and deliverables..." class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">{{ old('description') }}</textarea>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-100">
            <a href="{{ route('admin.projects.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50">Cancel</a>
            <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium shadow-sm transition">Create Project</button>
        </div>
    </form>
</div>
@endsection

