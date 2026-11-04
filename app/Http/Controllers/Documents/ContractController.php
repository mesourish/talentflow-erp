<?php

/**
 * TalentFlow ERP - Celebrity & Talent Management System
 *
 * @package    App\Http\Controllers\Documents
 * @author     Sourish Mondal
 * @version    1.0.0
 * @since      Laravel 12.x / PHP 8.2+
 * @copyright  2026 TalentFlow ERP. All rights reserved.
 * @license    Proprietary / Enterprise Commercial
 */

namespace App\Http\Controllers\Documents;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Celebrity;
use App\Models\Client;
use App\Models\Contract;
use App\Models\Project;
use App\Services\CodeGenerator;
use App\Services\DocumentUploadService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Class ContractController
 *
 * Manages digital contracts, validity periods, legal rights, and automated 30-day expiry notifications.
 *
 * @package App\Http\Controllers\Documents
 * @author  Sourish Mondal
 * @version 1.0.0
 */
class ContractController extends Controller
{
    public function __construct(
        protected CodeGenerator $codeGenerator,
        protected DocumentUploadService $uploadService
    ) {}

    public function index(Request $request): View
    {
        $query = Contract::with(['client', 'celebrity', 'project', 'documents']);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('contract_title', 'like', "%{$search}%")
                  ->orWhere('contract_no', 'like', "%{$search}%");
            });
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($type = $request->input('contract_type')) {
            $query->where('contract_type', $type);
        }

        if ($request->boolean('expiring_soon')) {
            $query->where('status', 'active')
                  ->whereNotNull('expiry_date')
                  ->where('expiry_date', '<=', now()->addDays(30))
                  ->where('expiry_date', '>=', now());
        }

        $contracts = $query->latest('start_date')->paginate(12)->withQueryString();
        $types = ['Endorsement', 'Appearance', 'Licence Agreement', 'NDA & Confidentiality', 'Social Media Influencer Agreement'];
        $statuses = ['draft', 'sent', 'active', 'expired', 'terminated'];

        return view('contracts.index', compact('contracts', 'types', 'statuses'));
    }

    public function create(): View
    {
        $suggestedNo = $this->codeGenerator->contractNumber();
        $clients = Client::where('status', 'active')->orderBy('company_name')->get();
        $celebrities = Celebrity::where('profile_status', 'active')->orderBy('stage_name')->get();
        $projects = Project::whereIn('status', ['planned', 'active'])->orderBy('project_name')->get();

        return view('contracts.create', compact('suggestedNo', 'clients', 'celebrities', 'projects'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'contract_no' => 'required|string|max:50|unique:contracts,contract_no',
            'project_id' => 'nullable|exists:projects,id',
            'client_id' => 'nullable|exists:clients,id',
            'celebrity_id' => 'nullable|exists:celebrities,id',
            'contract_type' => 'required|string|max:100',
            'contract_title' => 'required|string|max:200',
            'start_date' => 'required|date',
            'expiry_date' => 'nullable|date|after_or_equal:start_date',
            'usage_rights' => 'nullable|string',
            'territory' => 'nullable|string|max:150',
            'permitted_channels' => 'nullable|array',
            'exclusivity' => 'nullable|boolean',
            'status' => 'required|string|max:30',
            'file' => 'nullable|file|mimes:pdf,doc,docx|max:20480',
        ]);

        $contract = Contract::create([
            'contract_no' => $validated['contract_no'],
            'project_id' => $validated['project_id'] ?? null,
            'client_id' => $validated['client_id'] ?? null,
            'celebrity_id' => $validated['celebrity_id'] ?? null,
            'contract_type' => $validated['contract_type'],
            'contract_title' => $validated['contract_title'],
            'start_date' => $validated['start_date'],
            'expiry_date' => $validated['expiry_date'] ?? null,
            'usage_rights' => $validated['usage_rights'] ?? null,
            'territory' => $validated['territory'] ?? null,
            'permitted_channels' => $validated['permitted_channels'] ?? null,
            'exclusivity' => $request->boolean('exclusivity'),
            'status' => $validated['status'],
            'created_by' => auth()->id() ?? 1,
        ]);

        if ($request->hasFile('file')) {
            $this->uploadService->upload($request->file('file'), $contract, 'contract', true);
        }

        ActivityLog::log('contract_created', "Created Contract {$contract->contract_no} ({$contract->contract_title})", $contract);

        return redirect()->route('admin.contracts.show', $contract)->with('success', 'Contract registered successfully.');
    }

    public function show(Contract $contract): View
    {
        $contract->load(['client', 'celebrity', 'project', 'documents.accessLogs.user', 'creator']);
        return view('contracts.show', compact('contract'));
    }

    public function edit(Contract $contract): View
    {
        $clients = Client::where('status', 'active')->orderBy('company_name')->get();
        $celebrities = Celebrity::where('profile_status', 'active')->orderBy('stage_name')->get();
        $projects = Project::whereIn('status', ['planned', 'active'])->orderBy('project_name')->get();

        return view('contracts.edit', compact('contract', 'clients', 'celebrities', 'projects'));
    }

    public function update(Request $request, Contract $contract): RedirectResponse
    {
        $validated = $request->validate([
            'contract_type' => 'required|string|max:100',
            'contract_title' => 'required|string|max:200',
            'start_date' => 'required|date',
            'expiry_date' => 'nullable|date|after_or_equal:start_date',
            'usage_rights' => 'nullable|string',
            'territory' => 'nullable|string|max:150',
            'permitted_channels' => 'nullable|array',
            'exclusivity' => 'nullable|boolean',
            'status' => 'required|string|max:30',
        ]);

        $validated['exclusivity'] = $request->boolean('exclusivity');

        $contract->update($validated);

        ActivityLog::log('contract_updated', "Updated Contract {$contract->contract_no}", $contract);

        return redirect()->route('admin.contracts.show', $contract)->with('success', 'Contract updated successfully.');
    }
}

