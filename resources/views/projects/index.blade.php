@extends('layouts.app')

@section('title', 'Projects Management')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Projects</h1>
            <p class="text-sm text-gray-500 mt-1">Manage brand campaigns, celebrity endorsements, shoots, and live events</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.projects.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                New Project
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
        <form method="GET" action="{{ route('admin.projects.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-4">
            <div class="sm:col-span-2">
                <label class="block text-xs font-semibold uppercase text-gray-500 mb-1">Search</label>
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by project name, code, or client..." class="w-full pl-9 pr-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase text-gray-500 mb-1">Status</label>
                <select name="status" class="w-full py-2 px-3 border border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">All Statuses</option>
                    @foreach($statuses as $st)
                        <option value="{{ $st }}" {{ request('status') === $st ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $st)) }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase text-gray-500 mb-1">Type</label>
                <div class="flex gap-2">
                    <select name="project_type" class="w-full py-2 px-3 border border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">All Types</option>
                        @foreach($types as $tp)
                            <option value="{{ $tp }}" {{ request('project_type') === $tp ? 'selected' : '' }}>{{ ucfirst($tp) }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg">Filter</button>
                    @if(request()->hasAny(['search', 'status', 'project_type']))
                        <a href="{{ route('admin.projects.index') }}" class="px-3 py-2 bg-red-50 hover:bg-red-100 text-red-600 text-sm font-medium rounded-lg flex items-center">Reset</a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <!-- Project Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($projects as $project)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition p-5 flex flex-col justify-between">
                <div>
                    <!-- Top Bar: Code + Status -->
                    <div class="flex items-center justify-between mb-3">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-mono font-semibold bg-gray-100 text-gray-800">
                            {{ $project->project_code }}
                        </span>
                        @php
                            $statusClasses = [
                                'planned' => 'bg-sky-100 text-sky-800',
                                'active' => 'bg-emerald-100 text-emerald-800',
                                'on_hold' => 'bg-amber-100 text-amber-800',
                                'completed' => 'bg-indigo-100 text-indigo-800',
                                'cancelled' => 'bg-red-100 text-red-800',
                            ];
                        @endphp
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $statusClasses[$project->status] ?? 'bg-gray-100 text-gray-800' }}">
                            {{ ucfirst(str_replace('_', ' ', $project->status)) }}
                        </span>
                    </div>

                    <!-- Project Title -->
                    <h3 class="text-lg font-bold text-gray-900 line-clamp-1 hover:text-indigo-600">
                        <a href="{{ route('admin.projects.show', $project) }}">{{ $project->project_name }}</a>
                    </h3>

                    <!-- Client & Type -->
                    <div class="mt-2 text-xs text-gray-600 flex items-center gap-2">
                        <span class="font-medium text-gray-800">{{ $project->client?->company_name ?? 'Internal / No Client' }}</span>
                        <span>•</span>
                        <span class="px-2 py-0.5 bg-gray-50 border border-gray-200 rounded text-gray-600 uppercase font-semibold text-[10px]">{{ $project->project_type }}</span>
                    </div>

                    <!-- Dates & Manager -->
                    <div class="mt-4 pt-3 border-t border-gray-100 grid grid-cols-2 gap-2 text-xs text-gray-500">
                        <div>
                            <span class="block text-gray-400">Timeline</span>
                            <span class="font-medium text-gray-700">{{ $project->start_date?->format('M d, Y') }} - {{ $project->end_date?->format('M d, Y') }}</span>
                        </div>
                        <div>
                            <span class="block text-gray-400">Manager</span>
                            <span class="font-medium text-gray-700 truncate block">{{ $project->projectManager?->name ?? 'Unassigned' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Footer Stats & CTA -->
                <div class="mt-5 pt-3 border-t border-gray-100 flex items-center justify-between text-xs">
                    <div>
                        <span class="text-gray-400 block text-[11px]">Budget</span>
                        <span class="text-sm font-bold text-gray-900">₹{{ number_format($project->budget_amount, 2) }}</span>
                    </div>
                    <div class="flex items-center gap-3 text-gray-500">
                        <span title="Tasks" class="flex items-center gap-1 font-medium">
                            <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                            {{ $project->tasks_count }}
                        </span>
                        <span title="Bookings" class="flex items-center gap-1 font-medium">
                            <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            {{ $project->bookings_count }}
                        </span>
                        <a href="{{ route('admin.projects.show', $project) }}" class="p-1.5 rounded-lg bg-indigo-50 text-indigo-600 hover:bg-indigo-100 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white rounded-xl p-12 text-center border border-gray-200">
                <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                <h3 class="mt-3 text-base font-semibold text-gray-900">No projects found</h3>
                <p class="mt-1 text-sm text-gray-500">Create a project or convert an approved quotation to get started.</p>
                <div class="mt-5">
                    <a href="{{ route('admin.projects.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                        Create Project
                    </a>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div>
        {{ $projects->links() }}
    </div>
</div>
@endsection

