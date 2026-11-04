<?php

/**
 * TalentFlow ERP - Celebrity & Talent Management System
 *
 * @package    App\Http\Controllers\Projects
 * @author     Sourish Mondal
 * @version    1.0.0
 * @since      Laravel 12.x / PHP 8.2+
 * @copyright  2026 TalentFlow ERP. All rights reserved.
 * @license    Proprietary / Enterprise Commercial
 */

namespace App\Http\Controllers\Projects;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Celebrity;
use App\Models\Client;
use App\Models\Project;
use App\Models\ProjectMilestone;
use App\Models\User;
use App\Services\CodeGenerator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Class ProjectController
 *
 * Oversees campaign deliverables, milestones, profitability margin tracking, and financial health alerts.
 *
 * @package App\Http\Controllers\Projects
 * @author  Sourish Mondal
 * @version 1.0.0
 */
class ProjectController extends Controller
{
    public function __construct(
        protected CodeGenerator $codeGenerator
    ) {}

    public function index(Request $request): View
    {
        $query = Project::with(['client', 'projectManager'])->withCount(['tasks', 'bookings', 'expenses']);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('project_name', 'like', "%{$search}%")
                  ->orWhere('project_code', 'like', "%{$search}%")
                  ->orWhereHas('client', fn($cq) => $cq->where('company_name', 'like', "%{$search}%"));
            });
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($type = $request->input('project_type')) {
            $query->where('project_type', $type);
        }

        $projects = $query->latest('start_date')->paginate(12)->withQueryString();
        $statuses = ['planned', 'active', 'on_hold', 'completed', 'cancelled'];
        $types = ['campaign', 'event', 'shoot', 'endorsement'];

        return view('projects.index', compact('projects', 'statuses', 'types'));
    }

    public function create(): View
    {
        $suggestedCode = $this->codeGenerator->projectCode();
        $clients = Client::where('status', 'active')->orderBy('company_name')->get();
        $managers = User::where('is_active', true)->orderBy('name')->get();

        return view('projects.create', compact('suggestedCode', 'clients', 'managers'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'project_code' => 'required|string|max:50|unique:projects,project_code',
            'client_id' => 'required|exists:clients,id',
            'project_name' => 'required|string|max:200',
            'project_type' => 'required|string|max:100',
            'project_manager_id' => 'nullable|exists:users,id',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'budget_amount' => 'required|numeric|min:0',
            'currency' => 'required|string|size:3',
            'status' => 'required|string|max:30',
        ]);

        $validated['created_by'] = auth()->id() ?? 1;

        $project = Project::create($validated);

        ActivityLog::log('project_created', "Created Project {$project->project_name} ({$project->project_code})", $project);

        return redirect()->route('admin.projects.show', $project)->with('success', 'Project created successfully.');
    }

    public function show(Project $project): View
    {
        $project->load([
            'client',
            'projectManager',
            'members',
            'tasks.assignee',
            'milestones',
            'bookings.celebrities',
            'expenses.sourceClaim',
            'invoices.payments',
            'payouts.celebrity',
            'contracts.celebrity',
            'claims.submitter',
        ]);

        $allUsers = User::where('is_active', true)->orderBy('name')->get();
        $allCelebrities = Celebrity::where('profile_status', 'active')->orderBy('stage_name')->get();

        // Financial totals summary for project
        $totalInvoiced = (float) $project->invoices->where('status', '!=', 'cancelled')->sum('grand_total');
        $totalReceived = (float) $project->invoices->sum('amount_paid');
        $totalExpenses = (float) $project->expenses->sum('amount');
        $totalPayouts = (float) $project->payouts->sum('net_payable');
        $totalCost = $totalExpenses + $totalPayouts;
        $netProfit = $totalInvoiced - $totalCost;
        $marginPercent = $totalInvoiced > 0 ? round(($netProfit / $totalInvoiced) * 100, 1) : 0;

        return view('projects.show', compact(
            'project',
            'allUsers',
            'allCelebrities',
            'totalInvoiced',
            'totalReceived',
            'totalExpenses',
            'totalPayouts',
            'totalCost',
            'netProfit',
            'marginPercent'
        ));
    }

    public function update(Request $request, Project $project): RedirectResponse
    {
        $validated = $request->validate([
            'project_name' => 'required|string|max:200',
            'project_type' => 'required|string|max:100',
            'project_manager_id' => 'nullable|exists:users,id',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'budget_amount' => 'required|numeric|min:0',
            'status' => 'required|string|max:30',
        ]);

        $project->update($validated);

        ActivityLog::log('project_updated', "Updated Project {$project->project_code}", $project);

        return back()->with('success', 'Project updated successfully.');
    }
}

