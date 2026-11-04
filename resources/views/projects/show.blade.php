@extends('layouts.app')

@section('title', $project->project_name . ' (' . $project->project_code . ')')

@section('content')
<div class="space-y-6" x-data="{ activeTab: 'overview', showTaskModal: false, showEditModal: false }">
    <!-- Top Bar -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-6 rounded-xl shadow-sm border border-gray-200">
        <div>
            <div class="flex items-center gap-3">
                <span class="px-2.5 py-1 rounded text-xs font-mono font-semibold bg-gray-100 text-gray-800">{{ $project->project_code }}</span>
                @php
                    $statusClasses = [
                        'planned' => 'bg-sky-100 text-sky-800 border-sky-200',
                        'active' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                        'on_hold' => 'bg-amber-100 text-amber-800 border-amber-200',
                        'completed' => 'bg-indigo-100 text-indigo-800 border-indigo-200',
                        'cancelled' => 'bg-red-100 text-red-800 border-red-200',
                    ];
                @endphp
                <span class="px-3 py-1 rounded-full text-xs font-semibold border {{ $statusClasses[$project->status] ?? 'bg-gray-100 text-gray-800' }}">
                    {{ ucfirst(str_replace('_', ' ', $project->status)) }}
                </span>
                <span class="px-2.5 py-1 rounded text-xs font-semibold bg-purple-50 text-purple-700 border border-purple-200 uppercase">
                    {{ $project->project_type }}
                </span>
            </div>
            <h1 class="text-2xl font-bold text-gray-900 mt-2">{{ $project->project_name }}</h1>
            <p class="text-sm text-gray-500 mt-1 flex items-center gap-2">
                <span>Client: <strong class="text-gray-700">{{ $project->client?->company_name ?? 'N/A' }}</strong></span>
                <span>•</span>
                <span>Manager: <strong class="text-gray-700">{{ $project->projectManager?->name ?? 'Unassigned' }}</strong></span>
                <span>•</span>
                <span>Dates: <strong class="text-gray-700">{{ $project->start_date?->format('d M Y') }} - {{ $project->end_date?->format('d M Y') }}</strong></span>
            </p>
        </div>

        <div class="flex items-center gap-3">
            <button @click="showEditModal = true" class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 flex items-center gap-1.5">
                <svg class="w-4 h-4 text-gray-500" width="16" height="16" style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                Edit Details
            </button>
            <a href="{{ route('admin.bookings.create', ['project_id' => $project->id]) }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium shadow-sm flex items-center gap-1.5">
                <svg class="w-4 h-4" width="16" height="16" style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Add Booking
            </a>
        </div>
    </div>

    <!-- Financial KPI Summary Cards -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <span class="text-xs font-semibold text-gray-500 uppercase">Budget</span>
            <p class="text-lg font-bold text-gray-900 mt-1">₹{{ number_format($project->budget_amount, 2) }}</p>
            <span class="text-[11px] text-gray-400">Allocated budget</span>
        </div>

        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <span class="text-xs font-semibold text-gray-500 uppercase">Invoiced</span>
            <p class="text-lg font-bold text-blue-600 mt-1">₹{{ number_format($totalInvoiced, 2) }}</p>
            <span class="text-[11px] text-gray-400">{{ $project->invoices->count() }} invoice(s)</span>
        </div>

        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <span class="text-xs font-semibold text-gray-500 uppercase">Collected</span>
            <p class="text-lg font-bold text-emerald-600 mt-1">₹{{ number_format($totalReceived, 2) }}</p>
            <span class="text-[11px] text-gray-400">{{ $totalInvoiced > 0 ? round(($totalReceived/$totalInvoiced)*100) : 0 }}% collected</span>
        </div>

        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <span class="text-xs font-semibold text-gray-500 uppercase">Expenses</span>
            <p class="text-lg font-bold text-amber-600 mt-1">₹{{ number_format($totalExpenses, 2) }}</p>
            <span class="text-[11px] text-gray-400">Production / Claims</span>
        </div>

        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <span class="text-xs font-semibold text-gray-500 uppercase">Talent Payouts</span>
            <p class="text-lg font-bold text-purple-600 mt-1">₹{{ number_format($totalPayouts, 2) }}</p>
            <span class="text-[11px] text-gray-400">Net celebrity fees</span>
        </div>

        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm {{ $marginPercent < 15 ? 'border-amber-300 bg-amber-50/30' : '' }}">
            <span class="text-xs font-semibold text-gray-500 uppercase flex items-center justify-between">
                Margin
                @if($marginPercent < 15 && $totalInvoiced > 0)
                    <span class="text-[10px] px-1.5 py-0.5 rounded bg-amber-100 text-amber-800 font-bold">Alert &lt;15%</span>
                @endif
            </span>
            <p class="text-lg font-bold {{ $netProfit >= 0 ? 'text-gray-900' : 'text-red-600' }} mt-1">
                {{ $marginPercent }}%
            </p>
            <span class="text-[11px] text-gray-500">Net: ₹{{ number_format($netProfit, 2) }}</span>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="border-b border-gray-200 bg-white rounded-t-xl px-4 pt-2">
        <nav class="-mb-px flex space-x-8">
            <button @click="activeTab = 'overview'" :class="activeTab === 'overview' ? 'border-indigo-600 text-indigo-600 font-bold' : 'border-transparent text-gray-500 hover:text-gray-700'" class="whitespace-nowrap py-3 px-1 border-b-2 text-sm flex items-center gap-2">
                Overview & Scope
            </button>
            <button @click="activeTab = 'tasks'" :class="activeTab === 'tasks' ? 'border-indigo-600 text-indigo-600 font-bold' : 'border-transparent text-gray-500 hover:text-gray-700'" class="whitespace-nowrap py-3 px-1 border-b-2 text-sm flex items-center gap-2">
                Tasks & Milestones <span class="px-2 py-0.5 rounded-full text-xs bg-gray-100 text-gray-600">{{ $project->tasks->count() }}</span>
            </button>
            <button @click="activeTab = 'bookings'" :class="activeTab === 'bookings' ? 'border-indigo-600 text-indigo-600 font-bold' : 'border-transparent text-gray-500 hover:text-gray-700'" class="whitespace-nowrap py-3 px-1 border-b-2 text-sm flex items-center gap-2">
                Bookings <span class="px-2 py-0.5 rounded-full text-xs bg-gray-100 text-gray-600">{{ $project->bookings->count() }}</span>
            </button>
            <button @click="activeTab = 'finance'" :class="activeTab === 'finance' ? 'border-indigo-600 text-indigo-600 font-bold' : 'border-transparent text-gray-500 hover:text-gray-700'" class="whitespace-nowrap py-3 px-1 border-b-2 text-sm flex items-center gap-2">
                Invoices & Expenses
            </button>
            <button @click="activeTab = 'payouts'" :class="activeTab === 'payouts' ? 'border-indigo-600 text-indigo-600 font-bold' : 'border-transparent text-gray-500 hover:text-gray-700'" class="whitespace-nowrap py-3 px-1 border-b-2 text-sm flex items-center gap-2">
                Talent Payouts <span class="px-2 py-0.5 rounded-full text-xs bg-gray-100 text-gray-600">{{ $project->payouts->count() }}</span>
            </button>
            <button @click="activeTab = 'contracts'" :class="activeTab === 'contracts' ? 'border-indigo-600 text-indigo-600 font-bold' : 'border-transparent text-gray-500 hover:text-gray-700'" class="whitespace-nowrap py-3 px-1 border-b-2 text-sm flex items-center gap-2">
                Contracts & Legal <span class="px-2 py-0.5 rounded-full text-xs bg-gray-100 text-gray-600">{{ $project->contracts->count() }}</span>
            </button>
        </nav>
    </div>

    <!-- TAB 1: OVERVIEW -->
    <div x-show="activeTab === 'overview'" x-cloak class="space-y-6">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-6">
                <!-- Description Card -->
                <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
                    <h3 class="text-sm font-bold uppercase tracking-wider text-gray-500 mb-3">Project Description & Scope</h3>
                    <div class="text-sm text-gray-700 leading-relaxed whitespace-pre-line">
                        {{ $project->description ?: 'No detailed scope provided for this project.' }}
                    </div>
                </div>

                <!-- Quotation Origin (if converted from quotation) -->
                @if($project->quotation)
                    <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-sm font-bold uppercase tracking-wider text-gray-500">Origin Quotation</h3>
                            <a href="{{ route('admin.quotations.show', $project->quotation) }}" class="text-xs text-indigo-600 hover:underline font-medium">View Full Quotation &rarr;</a>
                        </div>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-xs">
                            <div>
                                <span class="text-gray-400 block">Quotation Code</span>
                                <span class="font-mono font-semibold text-gray-900">{{ $project->quotation->quotation_code }}</span>
                            </div>
                            <div>
                                <span class="text-gray-400 block">Status</span>
                                <span class="font-semibold text-emerald-700 uppercase">{{ $project->quotation->status }}</span>
                            </div>
                            <div>
                                <span class="text-gray-400 block">Subtotal</span>
                                <span class="font-medium text-gray-900">₹{{ number_format($project->quotation->subtotal, 2) }}</span>
                            </div>
                            <div>
                                <span class="text-gray-400 block">Grand Total</span>
                                <span class="font-bold text-gray-900">₹{{ number_format($project->quotation->grand_total, 2) }}</span>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <div class="space-y-6">
                <!-- Client Details -->
                <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
                    <h3 class="text-sm font-bold uppercase tracking-wider text-gray-500 mb-3">Client Details</h3>
                    @if($project->client)
                        <div class="space-y-2 text-sm">
                            <p class="font-bold text-gray-900 text-base">{{ $project->client->company_name }}</p>
                            <p class="text-xs text-gray-500 font-mono">{{ $project->client->client_code }} • {{ ucfirst($project->client->client_type) }}</p>
                            <p class="text-gray-600 text-xs">Email: {{ $project->client->billing_email ?? 'N/A' }}</p>
                            <p class="text-gray-600 text-xs">GSTIN: <span class="font-mono">{{ $project->client->tax_number ?? 'Unregistered' }}</span></p>
                            <div class="pt-2">
                                <a href="{{ route('admin.clients.show', $project->client) }}" class="text-xs text-indigo-600 hover:underline font-semibold">Open Client Profile &rarr;</a>
                            </div>
                        </div>
                    @else
                        <p class="text-xs text-gray-500">Internal or direct booking</p>
                    @endif
                </div>

                <!-- Assigned Team -->
                <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
                    <h3 class="text-sm font-bold uppercase tracking-wider text-gray-500 mb-3">Project Leadership</h3>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold text-sm">
                            {{ substr($project->projectManager?->name ?? 'U', 0, 1) }}
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-900">{{ $project->projectManager?->name ?? 'Unassigned' }}</p>
                            <p class="text-xs text-gray-500">Project Manager • {{ $project->projectManager?->email }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- TAB 2: TASKS & MILESTONES -->
    <div x-show="activeTab === 'tasks'" x-cloak style="display: none;" class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-lg font-bold text-gray-900">Project Tasks</h3>
                <p class="text-xs text-gray-500">Action items, shoots, approvals, and team deliverables</p>
            </div>
            <button @click="showTaskModal = true" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium shadow-sm flex items-center gap-1.5">
                <svg class="w-4 h-4" width="16" height="16" style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Add Task
            </button>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50 text-gray-500 text-xs font-semibold uppercase">
                    <tr>
                        <th class="px-4 py-3 text-left">Task</th>
                        <th class="px-4 py-3 text-left">Assignee</th>
                        <th class="px-4 py-3 text-left">Priority</th>
                        <th class="px-4 py-3 text-left">Due Date</th>
                        <th class="px-4 py-3 text-left">Status</th>
                        <th class="px-4 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($project->tasks as $task)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">
                                <div class="font-semibold text-gray-900">{{ $task->task_title }}</div>
                                @if($task->description)
                                    <div class="text-xs text-gray-500 truncate max-w-xs">{{ $task->description }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-gray-700">
                                {{ $task->assignee?->name ?? 'Unassigned' }}
                            </td>
                            <td class="px-4 py-3">
                                @php
                                    $prioClasses = [
                                        'urgent' => 'bg-red-100 text-red-800',
                                        'high' => 'bg-amber-100 text-amber-800',
                                        'medium' => 'bg-blue-100 text-blue-800',
                                        'low' => 'bg-gray-100 text-gray-800',
                                    ];
                                @endphp
                                <span class="px-2 py-0.5 rounded text-xs font-semibold {{ $prioClasses[$task->priority] ?? 'bg-gray-100' }}">
                                    {{ ucfirst($task->priority) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-gray-500 text-xs">
                                {{ $task->due_date ? $task->due_date->format('M d, Y') : '-' }}
                            </td>
                            <td class="px-4 py-3">
                                <form method="POST" action="{{ route('admin.projects.tasks.update', [$project, $task]) }}" class="inline">
                                    @csrf
                                    @method('PUT')
                                    <select name="status" onchange="this.form.submit()" class="text-xs py-1 px-2 border rounded-md font-medium {{ $task->status === 'done' ? 'bg-emerald-50 text-emerald-800 border-emerald-300' : ($task->status === 'in_progress' ? 'bg-blue-50 text-blue-800 border-blue-300' : 'bg-gray-50 text-gray-800 border-gray-300') }}">
                                        <option value="todo" {{ $task->status === 'todo' ? 'selected' : '' }}>To Do</option>
                                        <option value="in_progress" {{ $task->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                        <option value="done" {{ $task->status === 'done' ? 'selected' : '' }}>Done</option>
                                    </select>
                                </form>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <form method="POST" action="{{ route('admin.projects.tasks.destroy', [$project, $task]) }}" onsubmit="return confirm('Remove task?');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700 text-xs">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-sm text-gray-500">No tasks created yet for this project.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Milestones list -->
        @if($project->milestones->count() > 0)
            <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm mt-6">
                <h4 class="text-sm font-bold uppercase tracking-wider text-gray-500 mb-4">Milestones Roadmap</h4>
                <div class="space-y-3">
                    @foreach($project->milestones as $m)
                        <div class="flex items-center justify-between p-3 rounded-lg border border-gray-100 bg-gray-50">
                            <div>
                                <span class="font-semibold text-gray-900 text-sm">{{ $m->milestone_name }}</span>
                                <span class="text-xs text-gray-500 ml-2">Due: {{ $m->due_date?->format('M d, Y') ?? 'N/A' }}</span>
                            </div>
                            <span class="px-2 py-0.5 rounded text-xs font-semibold {{ $m->status === 'completed' ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-200 text-gray-800' }}">
                                {{ ucfirst($m->status) }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    <!-- TAB 3: BOOKINGS -->
    <div x-show="activeTab === 'bookings'" x-cloak style="display: none;" class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-lg font-bold text-gray-900">Project Bookings & Events</h3>
                <p class="text-xs text-gray-500">Shoot days, stage appearances, brand galas, and live activations</p>
            </div>
            <a href="{{ route('admin.bookings.create', ['project_id' => $project->id]) }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium shadow-sm flex items-center gap-1.5">
                <svg class="w-4 h-4" width="16" height="16" style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                New Booking
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @forelse($project->bookings as $booking)
                <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm hover:shadow transition">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-mono font-semibold px-2 py-0.5 bg-gray-100 rounded text-gray-800">{{ $booking->booking_code }}</span>
                        @php
                            $bStatus = [
                                'confirmed' => 'bg-emerald-100 text-emerald-800',
                                'tentative' => 'bg-amber-100 text-amber-800',
                                'cancelled' => 'bg-red-100 text-red-800',
                                'completed' => 'bg-indigo-100 text-indigo-800',
                            ];
                        @endphp
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $bStatus[$booking->status] ?? 'bg-gray-100' }}">
                            {{ ucfirst($booking->status) }}
                        </span>
                    </div>

                    <h4 class="font-bold text-gray-900 text-base mt-2 hover:text-indigo-600">
                        <a href="{{ route('admin.bookings.show', $booking) }}">{{ $booking->event_name }}</a>
                    </h4>

                    <div class="mt-3 text-xs text-gray-600 space-y-1">
                        <p class="flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-gray-400" width="14" height="14" style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            {{ $booking->start_time?->format('M d, Y h:i A') }} - {{ $booking->end_time?->format('h:i A') }}
                        </p>
                        <p class="flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-gray-400" width="14" height="14" style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                            {{ $booking->venue_name ?? 'Location TBD' }} ({{ $booking->city ?? 'N/A' }})
                        </p>
                    </div>

                    <!-- Celebrities -->
                    <div class="mt-4 pt-3 border-t border-gray-100">
                        <span class="text-[11px] font-semibold uppercase text-gray-400">Assigned Celebrities:</span>
                        <div class="flex flex-wrap gap-1.5 mt-1">
                            @forelse($booking->celebrities as $celeb)
                                <span class="px-2 py-0.5 rounded bg-purple-50 text-purple-700 text-xs font-medium border border-purple-100">
                                    {{ $celeb->stage_name }}
                                </span>
                            @empty
                                <span class="text-xs text-gray-400 italic">No talent assigned</span>
                            @endforelse
                        </div>
                    </div>

                    <div class="mt-4 pt-2 flex justify-end">
                        <a href="{{ route('admin.bookings.show', $booking) }}" class="text-xs text-indigo-600 hover:underline font-semibold">View Booking &rarr;</a>
                    </div>
                </div>
            @empty
                <div class="col-span-2 bg-white rounded-xl p-8 text-center border border-gray-200 text-gray-500 text-sm">
                    No bookings scheduled yet for this project.
                </div>
            @endforelse
        </div>
    </div>

    <!-- TAB 4: FINANCE & EXPENSES -->
    <div x-show="activeTab === 'finance'" x-cloak style="display: none;" class="space-y-6">
        <!-- Invoices -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h4 class="text-base font-bold text-gray-900">Client Invoices</h4>
                    <p class="text-xs text-gray-500">Tax invoices raised against client ABC Fashion</p>
                </div>
                <a href="{{ route('admin.invoices.create', ['project_id' => $project->id]) }}" class="px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-xs font-semibold shadow-sm">
                    + Raise Invoice
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-xs">
                    <thead class="bg-gray-50 text-gray-500 uppercase font-semibold">
                        <tr>
                            <th class="px-3 py-2 text-left">Invoice #</th>
                            <th class="px-3 py-2 text-left">Date</th>
                            <th class="px-3 py-2 text-right">Grand Total</th>
                            <th class="px-3 py-2 text-right">Paid</th>
                            <th class="px-3 py-2 text-right">Balance</th>
                            <th class="px-3 py-2 text-center">Status</th>
                            <th class="px-3 py-2 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($project->invoices as $inv)
                            <tr>
                                <td class="px-3 py-2 font-mono font-bold text-indigo-600">
                                    <a href="{{ route('admin.invoices.show', $inv) }}">{{ $inv->invoice_number }}</a>
                                </td>
                                <td class="px-3 py-2 text-gray-600">{{ $inv->invoice_date?->format('d M Y') }}</td>
                                <td class="px-3 py-2 text-right font-semibold">₹{{ number_format($inv->grand_total, 2) }}</td>
                                <td class="px-3 py-2 text-right text-emerald-600 font-medium">₹{{ number_format($inv->amount_paid, 2) }}</td>
                                <td class="px-3 py-2 text-right text-red-600 font-medium">₹{{ number_format($inv->balance_due, 2) }}</td>
                                <td class="px-3 py-2 text-center">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold uppercase {{ $inv->status === 'paid' ? 'bg-emerald-100 text-emerald-800' : ($inv->status === 'partially_paid' ? 'bg-amber-100 text-amber-800' : 'bg-gray-100 text-gray-800') }}">
                                        {{ str_replace('_', ' ', $inv->status) }}
                                    </span>
                                </td>
                                <td class="px-3 py-2 text-right space-x-2">
                                    <a href="{{ route('admin.invoices.show', $inv) }}" class="text-indigo-600 hover:underline">View</a>
                                    <a href="{{ route('admin.invoices.pdf', $inv) }}" class="text-gray-600 hover:underline">PDF</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="px-3 py-4 text-center text-gray-400">No invoices generated yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Project Expenses & Reimbursements -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h4 class="text-base font-bold text-gray-900">Project Expenses & Employee Claims</h4>
                    <p class="text-xs text-gray-500">Production costs, travel/hotel bills, and approved reimbursement payouts</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-xs">
                    <thead class="bg-gray-50 text-gray-500 uppercase font-semibold">
                        <tr>
                            <th class="px-3 py-2 text-left">Date</th>
                            <th class="px-3 py-2 text-left">Category</th>
                            <th class="px-3 py-2 text-left">Vendor / Description</th>
                            <th class="px-3 py-2 text-left">Source</th>
                            <th class="px-3 py-2 text-right">Amount</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($project->expenses as $exp)
                            <tr>
                                <td class="px-3 py-2 text-gray-600">{{ $exp->expense_date?->format('d M Y') }}</td>
                                <td class="px-3 py-2 font-medium capitalize">{{ $exp->category }}</td>
                                <td class="px-3 py-2 text-gray-800">{{ $exp->description }} @if($exp->vendor_name) ({{ $exp->vendor_name }}) @endif</td>
                                <td class="px-3 py-2">
                                    @if($exp->sourceClaim)
                                        <span class="px-1.5 py-0.5 rounded bg-blue-50 text-blue-700 text-[10px] font-mono">Claim: {{ $exp->sourceClaim->claim_code }}</span>
                                    @else
                                        <span class="text-gray-400 text-[10px]">Direct Expense</span>
                                    @endif
                                </td>
                                <td class="px-3 py-2 text-right font-bold text-gray-900">₹{{ number_format($exp->amount, 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-3 py-4 text-center text-gray-400">No expenses recorded.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- TAB 5: TALENT PAYOUTS -->
    <div x-show="activeTab === 'payouts'" x-cloak style="display: none;" class="space-y-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h4 class="text-base font-bold text-gray-900">Celebrity Payouts & Agency Commissions</h4>
                    <p class="text-xs text-gray-500">Gross fees, default 20% commission deduction, TDS withholding, and net celebrity payout</p>
                </div>
                <a href="{{ route('admin.commissions.index') }}" class="text-xs text-indigo-600 hover:underline font-semibold">Manage All Commissions &rarr;</a>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-xs">
                    <thead class="bg-gray-50 text-gray-500 uppercase font-semibold">
                        <tr>
                            <th class="px-3 py-2 text-left">Payout Code</th>
                            <th class="px-3 py-2 text-left">Celebrity</th>
                            <th class="px-3 py-2 text-right">Gross Amount</th>
                            <th class="px-3 py-2 text-right">Agency Comm (%)</th>
                            <th class="px-3 py-2 text-right">TDS Withheld</th>
                            <th class="px-3 py-2 text-right font-bold">Net Payable</th>
                            <th class="px-3 py-2 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($project->payouts as $payout)
                            <tr>
                                <td class="px-3 py-2 font-mono font-bold text-gray-800">{{ $payout->payout_code }}</td>
                                <td class="px-3 py-2 font-semibold text-gray-900">{{ $payout->celebrity?->stage_name }}</td>
                                <td class="px-3 py-2 text-right">₹{{ number_format($payout->gross_amount, 2) }}</td>
                                <td class="px-3 py-2 text-right text-indigo-600 font-medium">₹{{ number_format($payout->commission_amount, 2) }} ({{ $payout->commission_percentage }}%)</td>
                                <td class="px-3 py-2 text-right text-gray-500">₹{{ number_format($payout->tds_amount, 2) }}</td>
                                <td class="px-3 py-2 text-right font-bold text-emerald-600">₹{{ number_format($payout->net_payable, 2) }}</td>
                                <td class="px-3 py-2 text-center">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold uppercase {{ $payout->status === 'paid' ? 'bg-emerald-100 text-emerald-800' : ($payout->status === 'approved' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800') }}">
                                        {{ $payout->status }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="px-3 py-4 text-center text-gray-400">No celebrity payouts generated yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- TAB 6: CONTRACTS & DMS -->
    <div x-show="activeTab === 'contracts'" x-cloak style="display: none;" class="space-y-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h4 class="text-base font-bold text-gray-900">Project Contracts & Brand Agreements</h4>
                    <p class="text-xs text-gray-500">Legal agreements with clients and talent</p>
                </div>
                <a href="{{ route('admin.contracts.create', ['project_id' => $project->id]) }}" class="px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-xs font-semibold shadow-sm">
                    + New Contract
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-xs">
                    <thead class="bg-gray-50 text-gray-500 uppercase font-semibold">
                        <tr>
                            <th class="px-3 py-2 text-left">Contract #</th>
                            <th class="px-3 py-2 text-left">Title</th>
                            <th class="px-3 py-2 text-left">Celebrity</th>
                            <th class="px-3 py-2 text-left">Validity</th>
                            <th class="px-3 py-2 text-right">Value</th>
                            <th class="px-3 py-2 text-center">Status</th>
                            <th class="px-3 py-2 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($project->contracts as $contract)
                            <tr>
                                <td class="px-3 py-2 font-mono font-bold text-indigo-600">
                                    <a href="{{ route('admin.contracts.show', $contract) }}">{{ $contract->contract_code }}</a>
                                </td>
                                <td class="px-3 py-2 font-medium text-gray-800">{{ $contract->title }}</td>
                                <td class="px-3 py-2 text-gray-700">{{ $contract->celebrity?->stage_name ?? 'Client Only' }}</td>
                                <td class="px-3 py-2 text-gray-500">{{ $contract->start_date?->format('d M Y') }} - {{ $contract->end_date?->format('d M Y') }}</td>
                                <td class="px-3 py-2 text-right font-semibold">₹{{ number_format($contract->contract_value, 2) }}</td>
                                <td class="px-3 py-2 text-center">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold uppercase {{ $contract->status === 'active' ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-800' }}">
                                        {{ $contract->status }}
                                    </span>
                                </td>
                                <td class="px-3 py-2 text-right">
                                    <a href="{{ route('admin.contracts.show', $contract) }}" class="text-indigo-600 hover:underline">View</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="px-3 py-4 text-center text-gray-400">No contracts registered for this project.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- MODAL: ADD TASK -->
    <div x-show="showTaskModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" @click="showTaskModal = false"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full p-6">
                <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                    <h3 class="text-lg font-bold text-gray-900">Add Task to Project</h3>
                    <button @click="showTaskModal = false" class="text-gray-400 hover:text-gray-600">&times;</button>
                </div>

                <form method="POST" action="{{ route('admin.projects.tasks.store', $project) }}" class="mt-4 space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Task Title *</label>
                        <input type="text" name="task_title" required class="w-full px-3 py-2 border rounded-lg text-sm" placeholder="e.g. Costume Trial & Styling Session">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Assignee</label>
                        <select name="assigned_to" class="w-full px-3 py-2 border rounded-lg text-sm">
                            <option value="">Unassigned</option>
                            @foreach($allUsers as $u)
                                <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->email }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Priority</label>
                            <select name="priority" class="w-full px-3 py-2 border rounded-lg text-sm">
                                <option value="low">Low</option>
                                <option value="medium" selected>Medium</option>
                                <option value="high">High</option>
                                <option value="urgent">Urgent</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Status</label>
                            <select name="status" class="w-full px-3 py-2 border rounded-lg text-sm">
                                <option value="todo" selected>To Do</option>
                                <option value="in_progress">In Progress</option>
                                <option value="done">Done</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Start Date</label>
                            <input type="date" name="start_date" value="{{ date('Y-m-d') }}" class="w-full px-3 py-2 border rounded-lg text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Due Date</label>
                            <input type="date" name="due_date" value="{{ date('Y-m-d', strtotime('+7 days')) }}" class="w-full px-3 py-2 border rounded-lg text-sm">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Description / Notes</label>
                        <textarea name="description" rows="2" class="w-full px-3 py-2 border rounded-lg text-sm"></textarea>
                    </div>

                    <div class="flex justify-end gap-3 pt-3 border-t">
                        <button type="button" @click="showTaskModal = false" class="px-4 py-2 border rounded-lg text-sm">Cancel</button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-semibold">Create Task</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL: EDIT PROJECT DETAILS -->
    <div x-show="showEditModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" @click="showEditModal = false"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-xl sm:w-full p-6">
                <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                    <h3 class="text-lg font-bold text-gray-900">Edit Project</h3>
                    <button @click="showEditModal = false" class="text-gray-400 hover:text-gray-600">&times;</button>
                </div>

                <form method="POST" action="{{ route('admin.projects.update', $project) }}" class="mt-4 space-y-4">
                    @csrf
                    @method('PUT')
                    <div>
                        <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Project Name *</label>
                        <input type="text" name="project_name" value="{{ $project->project_name }}" required class="w-full px-3 py-2 border rounded-lg text-sm">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Project Type</label>
                            <select name="project_type" class="w-full px-3 py-2 border rounded-lg text-sm">
                                <option value="campaign" {{ $project->project_type == 'campaign' ? 'selected' : '' }}>Campaign</option>
                                <option value="event" {{ $project->project_type == 'event' ? 'selected' : '' }}>Event / Gala</option>
                                <option value="shoot" {{ $project->project_type == 'shoot' ? 'selected' : '' }}>Photo / Video Shoot</option>
                                <option value="endorsement" {{ $project->project_type == 'endorsement' ? 'selected' : '' }}>Endorsement</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Status</label>
                            <select name="status" class="w-full px-3 py-2 border rounded-lg text-sm">
                                <option value="planned" {{ $project->status == 'planned' ? 'selected' : '' }}>Planned</option>
                                <option value="active" {{ $project->status == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="on_hold" {{ $project->status == 'on_hold' ? 'selected' : '' }}>On Hold</option>
                                <option value="completed" {{ $project->status == 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="cancelled" {{ $project->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Project Manager</label>
                        <select name="project_manager_id" class="w-full px-3 py-2 border rounded-lg text-sm">
                            <option value="">Unassigned</option>
                            @foreach($allUsers as $u)
                                <option value="{{ $u->id }}" {{ $project->project_manager_id == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Start Date</label>
                            <input type="date" name="start_date" value="{{ $project->start_date?->format('Y-m-d') }}" required class="w-full px-3 py-2 border rounded-lg text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">End Date</label>
                            <input type="date" name="end_date" value="{{ $project->end_date?->format('Y-m-d') }}" required class="w-full px-3 py-2 border rounded-lg text-sm">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Budget Amount (₹)</label>
                        <input type="number" step="0.01" name="budget_amount" value="{{ $project->budget_amount }}" required class="w-full px-3 py-2 border rounded-lg text-sm">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Description</label>
                        <textarea name="description" rows="3" class="w-full px-3 py-2 border rounded-lg text-sm">{{ $project->description }}</textarea>
                    </div>

                    <div class="flex justify-end gap-3 pt-3 border-t">
                        <button type="button" @click="showEditModal = false" class="px-4 py-2 border rounded-lg text-sm">Cancel</button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-semibold">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

