<?php

/**
 * TalentFlow ERP - Celebrity & Talent Management System
 *
 * @package    App\Http\Controllers\Claims
 * @author     Sourish Mondal
 * @version    1.0.0
 * @since      Laravel 12.x / PHP 8.2+
 * @copyright  2026 TalentFlow ERP. All rights reserved.
 * @license    Proprietary / Enterprise Commercial
 */

namespace App\Http\Controllers\Claims;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Claim;
use App\Models\ClaimItem;
use App\Models\Project;
use App\Services\ClaimApprovalService;
use App\Services\CodeGenerator;
use DomainException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * Class ClaimController
 *
 * Processes employee and production reimbursement claims, receipt attachments, and auto-posts project expenses.
 *
 * @package App\Http\Controllers\Claims
 * @author  Sourish Mondal
 * @version 1.0.0
 */
class ClaimController extends Controller
{
    public function __construct(
        protected CodeGenerator $codeGenerator,
        protected ClaimApprovalService $approvalService
    ) {}

    public function index(Request $request): View
    {
        $query = Claim::with(['project.client', 'submitter', 'expense']);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('claim_no', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $claims = $query->latest('claim_date')->paginate(12)->withQueryString();
        $statuses = ['draft', 'submitted', 'pending', 'approved', 'rejected', 'paid'];

        return view('claims.index', compact('claims', 'statuses'));
    }

    public function create(Request $request): View
    {
        $suggestedNo = $this->codeGenerator->claimNumber();
        $projects = Project::whereIn('status', ['planned', 'active'])->orderBy('project_name')->get();
        $selectedProjectId = $request->input('project_id');

        return view('claims.create', compact('suggestedNo', 'projects', 'selectedProjectId'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'claim_no' => 'required|string|max:50|unique:claims,claim_no',
            'project_id' => 'required|exists:projects,id',
            'claim_date' => 'required|date',
            'description' => 'required|string',
            'currency' => 'required|string|size:3',
            'items' => 'required|array|min:1',
            'items.*.expense_category' => 'required|string|max:100',
            'items.*.expense_date' => 'required|date',
            'items.*.description' => 'nullable|string',
            'items.*.amount' => 'required|numeric|min:0.01',
        ]);

        $totalAmount = array_sum(array_column($validated['items'], 'amount'));

        $claim = DB::transaction(function () use ($validated, $totalAmount) {
            $claim = Claim::create([
                'claim_no' => $validated['claim_no'],
                'project_id' => $validated['project_id'],
                'submitted_by' => auth()->id() ?? 1,
                'claim_date' => $validated['claim_date'],
                'description' => $validated['description'],
                'total_amount' => $totalAmount,
                'currency' => $validated['currency'],
                'status' => 'draft',
            ]);

            foreach ($validated['items'] as $item) {
                ClaimItem::create([
                    'claim_id' => $claim->id,
                    'expense_category' => $item['expense_category'],
                    'expense_date' => $item['expense_date'],
                    'description' => $item['description'] ?? null,
                    'amount' => $item['amount'],
                ]);
            }

            ActivityLog::log('claim_created', "Created expense claim {$claim->claim_no} for INR {$claim->total_amount}", $claim);

            return $claim;
        });

        return redirect()->route('admin.claims.show', $claim)->with('success', 'Claim drafted successfully.');
    }

    public function show(Claim $claim): View
    {
        $claim->load(['project.client', 'submitter', 'items.receipt', 'approvals.user', 'expense']);
        return view('claims.show', compact('claim'));
    }

    public function submit(Claim $claim): RedirectResponse
    {
        $claim->update([
            'status' => 'pending',
            'submitted_at' => now(),
        ]);

        $claim->approvals()->create([
            'user_id' => auth()->id() ?? 1,
            'action' => 'submitted',
            'comments' => 'Claim submitted for manager and finance approval.',
            'action_at' => now(),
        ]);

        ActivityLog::log('claim_submitted', "Claim {$claim->claim_no} submitted for approval.", $claim);

        return back()->with('success', 'Claim submitted for approval.');
    }

    public function approve(Request $request, Claim $claim): RedirectResponse
    {
        try {
            $this->approvalService->approve(
                $claim->id,
                auth()->id() ?? 1,
                $request->input('comments', 'Approved by Finance.')
            );

            return back()->with('success', 'Claim approved! Associated expense has been posted to project expenses.');
        } catch (DomainException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function reject(Request $request, Claim $claim): RedirectResponse
    {
        try {
            $this->approvalService->reject(
                $claim->id,
                auth()->id() ?? 1,
                $request->input('comments', 'Claim rejected by Finance.')
            );

            return back()->with('info', 'Claim marked as rejected.');
        } catch (DomainException $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}

