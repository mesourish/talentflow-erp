<?php

/**
 * TalentFlow ERP - Celebrity & Talent Management System
 *
 * @package    App\Http\Controllers\CRM
 * @author     Sourish Mondal
 * @version    1.0.0
 * @since      Laravel 12.x / PHP 8.2+
 * @copyright  2026 TalentFlow ERP. All rights reserved.
 * @license    Proprietary / Enterprise Commercial
 */

namespace App\Http\Controllers\CRM;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Client;
use App\Models\Lead;
use App\Models\User;
use App\Services\CodeGenerator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Class LeadController
 *
 * Oversees sales leads pipeline, lead qualification, budget valuations, and deal conversion stages.
 *
 * @package App\Http\Controllers\CRM
 * @author  Sourish Mondal
 * @version 1.0.0
 */
class LeadController extends Controller
{
    public function __construct(
        protected CodeGenerator $codeGenerator
    ) {}

    public function index(Request $request): View
    {
        $query = Lead::with(['client', 'assignee']);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('lead_title', 'like', "%{$search}%")
                  ->orWhere('lead_code', 'like', "%{$search}%");
            });
        }

        if ($stage = $request->input('stage')) {
            $query->where('stage', $stage);
        }

        $leads = $query->latest()->paginate(12)->withQueryString();
        $stages = ['new', 'contacted', 'proposal', 'negotiation', 'won', 'lost'];

        // Kanban/Summary counts
        $stageCounts = Lead::selectRaw('stage, count(*) as count, sum(estimated_value) as total_val')
            ->groupBy('stage')
            ->pluck('count', 'stage');

        return view('crm.leads.index', compact('leads', 'stages', 'stageCounts'));
    }

    public function create(): View
    {
        $suggestedCode = $this->codeGenerator->leadCode();
        $clients = Client::where('status', 'active')->orderBy('company_name')->get();
        $users = User::where('is_active', true)->orderBy('name')->get();

        return view('crm.leads.create', compact('suggestedCode', 'clients', 'users'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'lead_code' => 'required|string|max:30|unique:leads,lead_code',
            'client_id' => 'nullable|exists:clients,id',
            'lead_title' => 'required|string|max:200',
            'lead_source' => 'required|string|max:100',
            'stage' => 'required|string|max:50',
            'estimated_value' => 'required|numeric|min:0',
            'probability' => 'required|integer|between:0,100',
            'expected_close_date' => 'nullable|date',
            'assigned_to' => 'nullable|exists:users,id',
            'notes' => 'nullable|string',
        ]);

        $lead = Lead::create($validated);

        ActivityLog::log('lead_created', "Opportunity lead {$lead->lead_title} ({$lead->lead_code}) registered.", $lead);

        return redirect()->route('admin.leads.index')->with('success', 'Lead created successfully.');
    }

    public function show(Lead $lead): View
    {
        $lead->load(['client', 'assignee', 'quotations', 'activities.user']);
        return view('crm.leads.show', compact('lead'));
    }

    public function edit(Lead $lead): View
    {
        $clients = Client::where('status', 'active')->orderBy('company_name')->get();
        $users = User::where('is_active', true)->orderBy('name')->get();
        return view('crm.leads.edit', compact('lead', 'clients', 'users'));
    }

    public function update(Request $request, Lead $lead): RedirectResponse
    {
        $validated = $request->validate([
            'client_id' => 'nullable|exists:clients,id',
            'lead_title' => 'required|string|max:200',
            'lead_source' => 'required|string|max:100',
            'stage' => 'required|string|max:50',
            'estimated_value' => 'required|numeric|min:0',
            'probability' => 'required|integer|between:0,100',
            'expected_close_date' => 'nullable|date',
            'assigned_to' => 'nullable|exists:users,id',
            'notes' => 'nullable|string',
        ]);

        $lead->update($validated);

        ActivityLog::log('lead_updated', "Updated lead status for {$lead->lead_title}", $lead);

        return redirect()->route('admin.leads.show', $lead)->with('success', 'Lead updated successfully.');
    }
}

