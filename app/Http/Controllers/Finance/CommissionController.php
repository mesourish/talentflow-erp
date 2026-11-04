<?php

/**
 * TalentFlow ERP - Celebrity & Talent Management System
 *
 * @package    App\Http\Controllers\Finance
 * @author     Sourish Mondal
 * @version    1.0.0
 * @since      Laravel 12.x / PHP 8.2+
 * @copyright  2026 TalentFlow ERP. All rights reserved.
 * @license    Proprietary / Enterprise Commercial
 */

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Celebrity;
use App\Models\CelebrityPayout;
use App\Models\Client;
use App\Models\CommissionRule;
use App\Models\Project;
use App\Services\CommissionCalculator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Class CommissionController
 *
 * Calculates agency commissions (default 20%), custom override rules, TDS withholding, and celebrity payouts.
 *
 * @package App\Http\Controllers\Finance
 * @author  Sourish Mondal
 * @version 1.0.0
 */
class CommissionController extends Controller
{
    public function __construct(
        protected CommissionCalculator $commissionCalculator
    ) {}

    public function index(): View
    {
        $rules = CommissionRule::with(['celebrity', 'client'])->latest()->get();
        $payouts = CelebrityPayout::with(['project.client', 'celebrity'])->latest()->paginate(15);

        $totalPayouts = (float) CelebrityPayout::where('status', 'paid')->sum('net_payable');
        $pendingPayouts = (float) CelebrityPayout::where('status', '!=', 'paid')->sum('net_payable');
        $totalCommissionEarned = (float) CelebrityPayout::sum('commission_amount');

        $projects = Project::whereIn('status', ['active', 'completed'])->get();
        $celebrities = Celebrity::where('profile_status', 'active')->get();
        $clients = Client::where('status', 'active')->get();

        return view('finance.commissions.index', compact(
            'rules',
            'payouts',
            'totalPayouts',
            'pendingPayouts',
            'totalCommissionEarned',
            'projects',
            'celebrities',
            'clients'
        ));
    }

    public function storeRule(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'applies_to' => 'required|string|max:50',
            'celebrity_id' => 'nullable|exists:celebrities,id',
            'client_id' => 'nullable|exists:clients,id',
            'commission_type' => 'required|string|in:percentage,fixed',
            'commission_value' => 'required|numeric|min:0',
            'effective_from' => 'required|date',
            'effective_to' => 'nullable|date|after_or_equal:effective_from',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        CommissionRule::create($validated);

        return back()->with('success', 'Commission rule created.');
    }

    public function storePayout(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'celebrity_id' => 'required|exists:celebrities,id',
            'gross_amount' => 'required|numeric|min:0.01',
            'other_deductions' => 'nullable|numeric|min:0',
        ]);

        $calc = $this->commissionCalculator->calculate(
            $validated['gross_amount'],
            $validated['celebrity_id'],
            null,
            $validated['other_deductions'] ?? 0.0
        );

        $payout = CelebrityPayout::create([
            'project_id' => $validated['project_id'],
            'celebrity_id' => $validated['celebrity_id'],
            'gross_amount' => $calc['gross_amount'],
            'commission_amount' => $calc['commission_amount'],
            'other_deductions' => $calc['other_deductions'],
            'net_payable' => $calc['net_payable'],
            'status' => 'draft',
        ]);

        ActivityLog::log('payout_created', "Created celebrity payout for INR {$payout->net_payable}", $payout);

        return back()->with('success', "Payout calculated: Net payable INR {$payout->net_payable} (Agency Commission: INR {$payout->commission_amount}).");
    }

    public function approvePayout(CelebrityPayout $payout): RedirectResponse
    {
        $payout->update(['status' => 'approved']);
        ActivityLog::log('payout_approved', "Payout of INR {$payout->net_payable} approved for {$payout->celebrity->stage_name}", $payout);
        return back()->with('success', 'Payout marked as approved.');
    }

    public function markPaid(Request $request, CelebrityPayout $payout): RedirectResponse
    {
        $validated = $request->validate([
            'payment_reference' => 'required|string|max:100',
        ]);

        $payout->update([
            'status' => 'paid',
            'paid_at' => now(),
            'payment_reference' => $validated['payment_reference'],
        ]);

        ActivityLog::log('payout_paid', "Payout of INR {$payout->net_payable} disbursed to {$payout->celebrity->stage_name} (Ref: {$payout->payment_reference})", $payout);

        return back()->with('success', 'Celebrity payout disbursed and marked as paid.');
    }
}

